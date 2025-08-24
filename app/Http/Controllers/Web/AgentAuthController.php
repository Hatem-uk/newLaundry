<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\MailOrders;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Helper;

class AgentAuthController extends Controller
{
    /**
     * Show agent registration form
     */
    public function showRegisterForm(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Send POST request to register endpoint']);
        }

        return view('agent.auth.register');
    }

    /**
     * Handle agent registration
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'           => 'required|string|max:255',
                'phone'          => 'required|string|max:255',
                'address'        => 'required|string|max:255',
                'email'          => 'required|email|unique:users',
                'password'       => 'required|min:6|confirmed',
                'license_number' => 'required|string|max:255|unique:agents',
                'city_id'        => 'required|exists:cities,id',
                'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Hash password
            $validated['password'] = Hash::make($validated['password']);

            // Create user with agent role and pending status
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'phone'    => $validated['phone'] ?? null,
                'password' => $validated['password'],
                'role'     => 'agent',
                'status'   => 'pending', // Agent accounts are pending approval
            ]);

            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = Helper::uploadFile(
                    $request->file('image'),
                    'agents_logos',
                    ['jpeg', 'png', 'jpg', 'gif'],
                    2048
                );
            }

            // Create agent profile with pending status
            $agent = $user->agent()->create([
                'name'           => $validated['name'],
                'address'        => $validated['address'],
                'phone'          => $validated['phone'],
                'license_number' => $validated['license_number'],
                'city_id'        => $validated['city_id'],
                'logo'           => $imagePath,
                'status'         => 'offline', // Set to offline until approved
                'is_active'      => false, // Set to inactive until approved
            ]);

            // Load the agent relationship
            $user->load('agent');

            // Send notification emails
            $this->sendRegistrationEmails($user);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Agent registration successful. Your account is pending approval.',
                    'user'    => $user,
                    'status'  => 'pending'
                ], 201);
            }

            // For web requests, redirect to login with success message
            $successMessage = Helper::messageSuccess('Agent registration successful');
            return redirect()->route('agent.login')
                ->with('success', $successMessage['message'] . ' Your account is pending approval. You will be notified once approved.');

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            
            if ($request->wantsJson()) {
                return response()->json(['error' => $errorMessage['message']], 500);
            }
            
            return redirect()->back()->withInput()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show agent login form
     */
    public function showLoginForm(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Send POST request to login endpoint']);
        }

        return view('agent.auth.login');
    }

    /**
     * Handle agent login
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            // Check if user exists and is an agent
            $user = User::where('email', $credentials['email'])
                ->where('role', 'agent')
                ->first();

            if (!$user) {
                $error = ['email' => 'Invalid credentials or not an agent'];
                
                if ($request->wantsJson()) {
                    return response()->json(['errors' => $error], 422);
                }
                
                return back()->withErrors($error);
            }

            // Check if user is an agent and approved
            if ($user->role !== 'agent') {
                return back()->withErrors([
                    'email' => 'These credentials do not match our records.',
                ]);
            }

            if ($user->status !== 'approved') {
                return back()->withErrors([
                    'email' => 'Your account is pending approval. Please wait for admin approval.',
                ]);
            }

            // Check if agent profile is active
            if (!$user->agent || !$user->agent->is_active) {
                $error = ['email' => 'Your agent account is not active. Please contact admin.'];
                
                if ($request->wantsJson()) {
                    return response()->json(['errors' => $error], 422);
                }
                
                return back()->withErrors($error);
            }

            // Attempt login
            if (Auth::guard('web')->attempt($credentials)) {
                $request->session()->regenerate();

                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'Login successful',
                        'user'    => $user->load('agent')
                    ]);
                }

                $successMessage = Helper::messageSuccess('Login successful');
                return redirect()->route('agent.dashboard')->with('success', $successMessage['message']);
            }

            $error = ['email' => 'Invalid credentials'];
            
            if ($request->wantsJson()) {
                return response()->json(['errors' => $error], 422);
            }
            
            return back()->withErrors($error);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            
            if ($request->wantsJson()) {
                return response()->json(['error' => $errorMessage['message']], 500);
            }
            
            return back()->withErrors(['email' => $errorMessage['message']]);
        }
    }

    /**
     * Handle agent logout
     */
    public function logout(Request $request)
    {
        try {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Logged out successfully']);
            }

            $successMessage = Helper::messageSuccess('Logged out successfully');
            return redirect()->route('agent.login')->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            
            if ($request->wantsJson()) {
                return response()->json(['error' => $errorMessage['message']], 500);
            }
            
            return redirect()->route('agent.login')->with('error', $errorMessage['message']);
        }
    }

    /**
     * Send registration email notifications
     */
    private function sendRegistrationEmails(User $user): void
    {
        try {
            // Send notification to admin about new agent registration
            MailOrders::sendAgentRegistrationNotification($user);
            
            // Send welcome email to agent
            MailOrders::sendAgentWelcomeEmail($user);
        } catch (\Exception $e) {
            // Log error but don't fail registration
            \Log::error('Failed to send agent registration emails: ' . $e->getMessage());
        }
    }
}
