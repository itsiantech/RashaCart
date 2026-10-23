<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'admin.access' => \App\Http\Middleware\AdminAccess::class,
            'vendor.approved' => \App\Http\Middleware\EnsureVendorApproved::class,
            'tracking.attribution' => \App\Http\Middleware\TrackingAttributionMiddleware::class,
            'module.enabled' => \App\Http\Middleware\EnsureModuleEnabled::class,
            'customer' => \App\Http\Middleware\EnsureCustomer::class,
        ]);
        $middleware->appendToGroup('web', \App\Http\Middleware\TrackingAttributionMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
