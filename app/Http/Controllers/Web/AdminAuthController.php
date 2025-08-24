<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\MailOrders;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Helper;

class AdminAuthController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegisterForm()
    {
        return view('admin.auth.register');
    }

    /**
     * Handle admin registration
     */
    public function register(Request $request)
    {
        try {
            $validated = $this->validateRegistrationData($request);
            $user = $this->createAdminUser($validated, $request);
            $this->sendRegistrationEmails($user);

            $successMessage = Helper::messageSuccess('Admin registration successful');
            return redirect()->route('admin.login')->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->withInput()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        try {
            $credentials = $this->validateLoginData($request);
            
            if ($this->attemptLogin($credentials)) {
                $request->session()->regenerate();
                
                $successMessage = Helper::messageSuccess('Login successful');
                return redirect()->route('admin.dashboard')->with('success', $successMessage['message']);
            }

            return back()->withErrors(['email' => 'Invalid credentials or not an admin']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return back()->withErrors(['email' => $errorMessage['message']]);
        }
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        try {
            $this->performLogout($request);
            
            $successMessage = Helper::messageSuccess('Logged out successfully');
            return redirect()->route('admin.login')->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->route('admin.login')->with('error', $errorMessage['message']);
        }
    }

    /**
     * Validate registration data
     */
    private function validateRegistrationData(Request $request): array
    {
        return $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:255',
            'address'  => 'nullable|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    }

    /**
     * Validate login data
     */
    private function validateLoginData(Request $request): array
    {
        return $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
    }

    /**
     * Create admin user
     */
    private function createAdminUser(array $validated, Request $request): User
    {
        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        // Create user with admin role and active status
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role'     => 'admin',
            'status'   => 'approved',
        ]);

        $imagePath = $this->handleImageUpload($request);

        // Create admin profile
        $user->admin()->create([
            'name'    => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone'   => $validated['phone'] ?? null,
            'image'   => $imagePath,
        ]);

        $user->load('admin');
        return $user;
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload(Request $request): ?string
    {
        if ($request->hasFile('image')) {
            return Helper::uploadFile(
                $request->file('image'),
                'admin_images',
                ['jpeg', 'png', 'jpg', 'gif'],
                2048
            );
        }
        return null;
    }

    /**
     * Attempt login
     */
    private function attemptLogin(array $credentials): bool
    {
        return Auth::guard('web')->attempt(array_merge($credentials, ['role' => 'admin']));
    }

    /**
     * Perform logout
     */
    private function performLogout(Request $request): void
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Send registration email notifications
     */
    private function sendRegistrationEmails(User $user): void
    {
        try {
            \Log::info('New admin registered: ' . $user->email);
            // You can add admin-specific email notifications here if needed
            // MailOrders::sendAdminRegistrationNotification($user);
        } catch (\Exception $e) {
            \Log::error('Failed to send admin registration emails: ' . $e->getMessage());
        }
    }
}