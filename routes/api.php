<?php

use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Api\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Api\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Staff\TaskController as StaffTaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // ----------------------------------------------------
    // Public Authentication Endpoints
    // ----------------------------------------------------
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('api.admin.login');
    Route::post('/staff/login', [AuthController::class, 'staffLogin'])->name('api.staff.login');

    // ----------------------------------------------------
    // Authenticated Common Routes (Sanctum)
    // ----------------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('api.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

        // ------------------------------------------------
        // Admin Endpoints (Scoped to Admin Guard / Role)
        // ------------------------------------------------
        Route::prefix('admin')->middleware('admin')->group(function () {
            Route::get('/stats', [AdminDashboardController::class, 'stats'])->name('api.admin.stats');

            // Staff Management
            Route::get('/staff', [AdminStaffController::class, 'index'])->name('api.admin.staff.index');
            Route::post('/staff', [AdminStaffController::class, 'store'])->name('api.admin.staff.store');
            Route::get('/staff/{staff}', [AdminStaffController::class, 'show'])->name('api.admin.staff.show');
            Route::put('/staff/{staff}', [AdminStaffController::class, 'update'])->name('api.admin.staff.update');
            Route::delete('/staff/{staff}', [AdminStaffController::class, 'destroy'])->name('api.admin.staff.destroy');
            Route::patch('/staff/{staff}/toggle-status', [AdminStaffController::class, 'toggleStatus'])->name('api.admin.staff.toggle-status');
            Route::patch('/staff/{staff}/toggle-verification', [AdminStaffController::class, 'toggleVerification'])->name('api.admin.staff.toggle-verification');

            // Project Management
            Route::apiResource('projects', AdminProjectController::class)->names('api.admin.projects');

            // Task Management
            Route::apiResource('tasks', AdminTaskController::class)->names('api.admin.tasks');
        });

        // ------------------------------------------------
        // Staff Endpoints (Scoped to Staff Guard & Eligibility Checks)
        // ------------------------------------------------
        Route::prefix('staff')->middleware(['staff', 'staff.active'])->group(function () {
            Route::get('/stats', [StaffTaskController::class, 'stats'])->name('api.staff.stats');
            Route::get('/tasks', [StaffTaskController::class, 'index'])->name('api.staff.tasks.index');
            Route::get('/tasks/{task}', [StaffTaskController::class, 'show'])->name('api.staff.tasks.show');
            Route::patch('/tasks/{task}/status', [StaffTaskController::class, 'updateStatus'])->name('api.staff.tasks.update-status');
        });
    });
});
