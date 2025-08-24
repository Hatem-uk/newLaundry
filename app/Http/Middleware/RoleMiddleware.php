<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();
        
        if (!$user) {
            Log::warning('Unauthenticated access attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'path' => $request->path()
            ]);
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $requiredRoles = explode('|', $roles);
        
        // Check if user has required role
        if (!in_array($user->role, $requiredRoles)) {
            Log::warning('Unauthorized role access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'required_roles' => $requiredRoles,
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            
            return redirect()->route('home')->with('error', 'Access denied.');
        }

        // Check if user is approved (for all roles)
        if ($user->status !== 'approved') {
            Log::warning('Unauthorized status access attempt', [
                'user_id' => $user->id,
                'user_status' => $user->status,
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Account not approved'], 403);
            }
            
            return redirect()->route('home')->with('error', 'Your account is not approved yet.');
        }

        return $next($request);
    }
}
