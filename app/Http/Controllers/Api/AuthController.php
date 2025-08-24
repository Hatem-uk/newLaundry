<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\City;
use Illuminate\Support\Facades\Hash;
use App\Mail\MailOrders;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:laundry,worker,customer,admin',
            'phone' => 'nullable|string|max:20',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'laundry_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'laundry_id' => 'nullable|exists:laundries,id'
        ]);

        $status = ($request->role === 'customer' || $request->role === 'admin') ? 'approved' : 'pending';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $status,
            'phone' => $request->phone ?? null,
            'coins' => 0 // Default coin balance
        ]);

        // Create role-specific profile
        $this->createUserProfile($user, $request);

        // Send email notifications
        $this->sendRegistrationEmails($user);

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'message' => $status === 'approved' 
                ? 'Registration successful' 
                : 'Account pending approval',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'coins' => $user->coins
            ],
            'token' => $token
        ], 201);
    }

    /**
     * Create user profile based on role
     */
    private function createUserProfile(User $user, Request $request): void
    {
        switch ($request->role) {
            case 'laundry':
                $this->createLaundryProfile($user, $request);
                break;
            case 'worker':
                $this->createWorkerProfile($user, $request);
                break;
            case 'customer':
                $this->createCustomerProfile($user, $request);
                break;
            case 'admin':
                $this->createAdminProfile($user, $request);
                break;
        }
    }

    /**
     * Create laundry profile
     */
    private function createLaundryProfile(User $user, Request $request): void
    {
        $user->laundry()->create([
            'name' => $request->laundry_name ?? $request->name,
            'address' => $request->address ?? '',
            'phone' => $request->phone ?? '',
            'city_id' => $request->city_id,
            'status' => 'online',
            'is_active' => true,
            'working_hours' => $this->getDefaultWorkingHours(),
            'delivery_available' => true,
            'pickup_available' => true
        ]);
    }

    /**
     * Create worker profile
     */
    private function createWorkerProfile(User $user, Request $request): void
    {
        if (!$request->laundry_id) {
            throw new \Exception('Laundry ID is required for workers');
        }
        
        $user->worker()->create([
            'laundry_id' => $request->laundry_id,
            'position' => $request->position ?? 'Worker',
            'salary' => $request->salary ?? 0,
            'phone' => $request->phone ?? null,
            'status' => 'pending',
            'is_active' => true
        ]);
    }

    /**
     * Create customer profile
     */
    private function createCustomerProfile(User $user, Request $request): void
    {
        $user->customer()->create([
            'address' => $request->address ?? '',
            'phone' => $request->phone ?? '',
            'city_id' => $request->city_id,
            'coins' => 100 // Give new customers some initial coins
        ]);
    }

    /**
     * Create admin profile
     */
    private function createAdminProfile(User $user, Request $request): void
    {
        $user->admin()->create([
            'phone' => $request->phone ?? null,
            'is_active' => true
        ]);
    }

    /**
     * Get default working hours
     */
    private function getDefaultWorkingHours(): array
    {
        return [
            'monday' => ['09:00', '18:00'],
            'tuesday' => ['09:00', '18:00'],
            'wednesday' => ['09:00', '18:00'],
            'thursday' => ['09:00', '18:00'],
            'friday' => ['09:00', '18:00'],
            'saturday' => ['09:00', '18:00'],
            'sunday' => ['09:00', '18:00']
        ];
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email', 
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Check account approval
        if ($user->status !== 'approved') {
            return response()->json([
                'message' => 'Account not approved. Status: ' . $user->status
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'coins' => $user->coins
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function checkStatus(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'status' => $user->status,
            'role' => $user->role,
            'coins' => $user->coins,
            'profile_loaded' => $user->load($user->role)
        ]);
    }

    public function refreshToken(Request $request)
    {
        $user = $request->user();
        
        // Revoke current token
        $user->currentAccessToken()->delete();
        
        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Token refreshed successfully'
        ]);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user();
        
        $profile = null;
        switch ($user->role) {
            case 'laundry':
                $profile = $user->laundry()->with('city')->first();
                break;
            case 'customer':
                $profile = $user->customer()->with('city')->first();
                break;
            case 'worker':
                $profile = $user->worker()->with('laundry')->first();
                break;
            case 'admin':
                $profile = $user->admin;
                break;
        }
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'phone' => $user->phone,
                'coins' => $user->coins
            ],
            'profile' => $profile
        ]);
    }

    /**
     * Send registration email notifications
     */
    private function sendRegistrationEmails(User $user): void
    {
        try {
            switch ($user->role) {
                case 'laundry':
                    // Send notification to admin
                    MailOrders::sendLaundryRegistrationNotification($user);
                    // Send welcome email to laundry
                    MailOrders::sendLaundryWelcomeEmail($user);
                    break;
                    
                case 'agent':
                    // Send notification to admin
                    MailOrders::sendAgentRegistrationNotification($user);
                    // Send welcome email to agent
                    MailOrders::sendAgentWelcomeEmail($user);
                    break;
            }
        } catch (\Exception $e) {
            // Log error but don't fail registration
            \Log::error('Failed to send registration emails: ' . $e->getMessage());
        }
    }
}