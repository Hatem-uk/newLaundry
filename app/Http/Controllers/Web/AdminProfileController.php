<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Helpers\Helper;

class AdminProfileController extends Controller
{
    public function __construct()
    {
         $this->middleware('role:admin');
    }

    /**
     * Display the admin profile
     */
    public function show()
    {
        $admin = Auth::user();
        return view('admin.profile.show', compact('admin'));
    }

    /**
     * Show the form for editing the admin profile
     */
    public function edit()
    {
        $admin = Auth::user();
        return view('admin.profile.edit', compact('admin'));
    }

    /**
     * Update the admin profile
     */
    public function update(Request $request)
    {
        try {
            $admin = Auth::user();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('users')->ignore($admin->id)],
                'phone' => 'required|string|max:20',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'current_password' => 'nullable|required_with:new_password',
                'new_password' => 'nullable|min:6|confirmed',
                'new_password_confirmation' => 'nullable|min:6'
            ]);

            // Check current password if trying to change password
            if ($request->filled('current_password')) {
                if (!Hash::check($request->current_password, $admin->password)) {
                    return redirect()->back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
                }
            }

            $admin->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Handle image upload
            $imagePath = $admin->admin->image ?? null; // Keep existing image by default
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($admin->admin && $admin->admin->image) {
                    Helper::deleteFile($admin->admin->image);
                }
                // Upload new image
                $imagePath = Helper::uploadFile(
                    $request->file('image'),
                    'admin_images',
                    ['jpeg', 'png', 'jpg', 'gif'],
                    2048
                );
            }

            // Update admin profile if exists, create if not
            if ($admin->admin) {
                $admin->admin->update([
                    'phone' => $request->phone,
                    'image' => $imagePath,
                ]);
            } else {
                $admin->admin()->create([
                    'phone' => $request->phone,
                    'address' => '',
                    'image' => $imagePath
                ]);
            }

            // Update password if provided
            if ($request->filled('new_password')) {
                $admin->update([
                    'password' => Hash::make($request->new_password)
                ]);
            }

            $successMessage = Helper::messageSuccess('Profile updated');
            return redirect()->route('admin.profile.show')->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->withInput()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Delete the admin account
     */
    public function destroy(Request $request)
    {
        try {
            $admin = Auth::user();

            $request->validate([
                'password' => 'required|string',
                'confirmation' => 'required|in:delete'
            ]);

            if (!Hash::check($request->password, $admin->password)) {
                return redirect()->back()->withErrors(['password' => 'كلمة المرور غير صحيحة']);
            }

            if ($request->confirmation !== 'delete') {
                return redirect()->back()->withErrors(['confirmation' => 'يرجى كتابة "delete" للتأكيد']);
            }

            // Logout before deleting
            Auth::logout();
            
            $admin->delete();

            $successMessage = Helper::messageSuccess('Account deleted');
            return redirect()->route('admin.login')->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }
}
