<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // First, check if locale is set in session
        if (session()->has('locale')) {
            $locale = session('locale');
            if (in_array($locale, LaravelLocalization::getSupportedLanguagesKeys())) {
                app()->setLocale($locale);
                return $next($request);
            }
        }
        
        // Second, try to get locale from URL path
        $path = $request->path();
        $segments = explode('/', $path);
        $localeFromUrl = $segments[0] ?? null;
        
        // Check if the first segment is a valid locale
        if ($localeFromUrl && in_array($localeFromUrl, LaravelLocalization::getSupportedLanguagesKeys())) {
            app()->setLocale($localeFromUrl);
            // Store in session for consistency
            session(['locale' => $localeFromUrl]);
            return $next($request);
        }
        
        // Fallback to Accept-Language header
        $locale = $request->header('Accept-Language', 'en');
        
        // Extract the primary language from Accept-Language header
        if (strpos($locale, ',') !== false) {
            $locale = explode(',', $locale)[0];
        }
        
        // Remove any quality values (e.g., "en;q=0.9")
        if (strpos($locale, ';') !== false) {
            $locale = explode(';', $locale)[0];
        }
        
        // Validate and set locale
        if (!in_array($locale, LaravelLocalization::getSupportedLanguagesKeys())) {
            $locale = 'en'; // Default to English
        }
        
        app()->setLocale($locale);
        session(['locale' => $locale]);
        
        return $next($request);
    }
}
