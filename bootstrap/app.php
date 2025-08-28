<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AgentMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
         api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
         $middleware->alias([
            'role' => RoleMiddleware::class,
            'admin' => AdminMiddleware::class,
            'agent' => AgentMiddleware::class,
            'Image' => Intervention\Image\Facades\Image::class,
            'set.locale' => \App\Http\Middleware\SetLocale::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
          
        // Web Localization Middleware (Full stack)
        $middleware->appendToGroup('web', [
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
        ]);

        // API Localization Middleware (Lightweight)
        $middleware->appendToGroup('api', [
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
        ]);

        })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
