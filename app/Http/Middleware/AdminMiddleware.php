<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            Log::warning('Unauthenticated admin access attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'path' => $request->path()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Authentication required'], 401);
            }
            
            return redirect()->route('admin.login')->with('error', 'Please login to access admin area.');
        }

        // Check if user has admin role
        if ($user->role !== 'admin') {
            Log::warning('Unauthorized admin access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Admin access required'], 403);
            }
            
            return redirect()->route('home')->with('error', 'Access denied. Admin privileges required.');
        }

        // Check if admin account is approved
        if ($user->status !== 'approved') {
            Log::warning('Inactive admin access attempt', [
                'user_id' => $user->id,
                'user_status' => $user->status,
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Admin account not approved'], 403);
            }
            
            return redirect()->route('home')->with('error', 'Your admin account is not approved yet.');
        }

        return $next($request);
    }
}
