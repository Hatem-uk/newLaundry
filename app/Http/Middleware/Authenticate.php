<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            // Check the current route prefix to determine which login route to redirect to
            $path = $request->path();
            
            if (str_starts_with($path, 'admin')) {
                return route('admin.login');
            }
            
            if (str_starts_with($path, 'agent')) {
                return route('agent.login');
            }
            
            // Default fallback to admin login
            return route('admin.login');
        }
        
        return null;
    }
}
