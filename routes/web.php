<?php

use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Web\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Web\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Web\Staff\TaskController as StaffTaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes & Authentication
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'home'])->name('home');

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showAdminLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'adminLogin'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'adminLogout'])->name('logout');
});

// Staff Authentication Routes
Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('/login', [AuthController::class, 'showStaffLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'staffLogin'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'staffLogout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Portal Routes (Guarded by auth:admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Projects Management
    Route::resource('projects', AdminProjectController::class);

    // Staff Management
    Route::resource('staff', AdminStaffController::class);
    Route::patch('/staff/{staff}/toggle-status', [AdminStaffController::class, 'toggleStatus'])->name('staff.toggle-status');
    Route::patch('/staff/{staff}/toggle-verification', [AdminStaffController::class, 'toggleVerification'])->name('staff.toggle-verification');

    // Task Management
    Route::resource('tasks', AdminTaskController::class);
});

/*
|--------------------------------------------------------------------------
| Staff Portal Routes (Guarded by auth:staff & Active/Verified Checks)
|--------------------------------------------------------------------------
*/
Route::prefix('staff')->name('staff.')->middleware(['auth:staff', 'staff.active'])->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('/tasks', [StaffTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [StaffTaskController::class, 'show'])->name('tasks.show');
    Route::patch('/tasks/{task}/status', [StaffTaskController::class, 'updateStatus'])->name('tasks.update-status');
});
