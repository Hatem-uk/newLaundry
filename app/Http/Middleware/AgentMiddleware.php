<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AgentMiddleware
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
            Log::warning('Unauthenticated agent access attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'path' => $request->path()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Authentication required'], 401);
            }
            
            return redirect()->route('agent.login')->with('error', 'Please login to access agent area.');
        }

        // Check if user has agent role
        if ($user->role !== 'agent') {
            Log::warning('Unauthorized agent access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Agent access required'], 403);
            }
            
            return redirect()->route('home')->with('error', 'Access denied. Agent privileges required.');
        }

        // Check if agent account is approved
        if ($user->status !== 'approved') {
            Log::warning('Unapproved agent access attempt', [
                'user_id' => $user->id,
                'user_status' => $user->status,
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Agent account not approved'], 403);
            }
            
            if ($user->status === 'pending') {
                return redirect()->route('agent.login')->with('error', 'Your account is pending approval. Please wait for admin approval.');
            } elseif ($user->status === 'rejected') {
                return redirect()->route('agent.login')->with('error', 'Your account has been rejected. Please contact admin for more information.');
            }
            
            return redirect()->route('agent.login')->with('error', 'Your agent account is not active.');
        }

        // Check if agent profile is active
        if (!$user->agent || !$user->agent->is_active) {
            Log::warning('Inactive agent profile access attempt', [
                'user_id' => $user->id,
                'agent_exists' => $user->agent ? 'yes' : 'no',
                'agent_active' => $user->agent ? ($user->agent->is_active ? 'yes' : 'no') : 'n/a',
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Agent profile not active'], 403);
            }
            
            return redirect()->route('agent.login')->with('error', 'Your agent profile is not active. Please contact admin.');
        }

        return $next($request);
    }
}
