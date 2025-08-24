<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\Helper;

class ApprovalController extends Controller
{
    public function pendingApprovals()
    {
        $pendingUsers = User::where('status', 'pending')
            ->whereIn('role', ['company', 'agent'])
            ->with(['company', 'agent'])
            ->get();
            
        return view('admin.approvals.index', compact('pendingUsers'));
    }

    public function showApproval(User $user)
    {
        $profile = null;
        
        if ($user->role === 'company' && $user->company) {
            $profile = $user->company;
        } elseif ($user->role === 'agent' && $user->agent) {
            $profile = $user->agent;
        }
        
        return view('admin.approvals.show', compact('user', 'profile'));
    }

    public function approveUser(User $user)
    {
        try {
            if (!in_array($user->role, ['company', 'agent'])) {
                return back()->with('error', 'Invalid user type for approval');
            }

            $user->update(['status' => 'approved']);
            
            // Send approval notification
            event(new UserApproved($user));
            
            $successMessage = Helper::messageSuccess('User approved');
            return redirect()->route('admin.approvals.pending')
                ->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    public function rejectUser(User $user)
    {
        try {
            $user->update(['status' => 'rejected']);
            
            // Send rejection notification
            event(new UserRejected($user));
            
            $successMessage = Helper::messageSuccess('User rejected');
            return back()->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }
}