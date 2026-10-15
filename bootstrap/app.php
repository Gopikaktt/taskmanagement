<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureStaff;
use App\Http\Middleware\EnsureStaffIsActiveAndVerified;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'staff' => EnsureStaff::class,
            'staff.active' => EnsureStaffIsActiveAndVerified::class,
        ]);

        $middleware->encryptCookies(except: [
            'remembered_admin_email',
            'remembered_admin_password',
            'remembered_staff_email',
            'remembered_staff_password',
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin*')) {
                return route('admin.login');
            }
            return route('staff.login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            if (Auth::guard('admin')->check()) {
                return route('admin.dashboard');
            }
            if (Auth::guard('staff')->check()) {
                return route('staff.dashboard');
            }
            return route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
