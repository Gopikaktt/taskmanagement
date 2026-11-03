<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\StaffLoginRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\StaffResource;
use App\Models\Admin;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    /**
     * Landing / Welcome Page -> Show Staff Login.
     */
    public function home(Request $request): Response|RedirectResponse
    {
        return $this->showStaffLoginForm($request);
    }

    /**
     * Show Admin Login form (Inertia).
     */
    public function showAdminLoginForm(Request $request): Response|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('staff')->check()) {
            return redirect()->route('staff.dashboard');
        }

        $rememberedEmail = (string) $request->cookie('remembered_admin_email', '');

        if (str_contains($rememberedEmail, '|')) {
            $parts = explode('|', $rememberedEmail);
            $rememberedEmail = end($parts);
        }

        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $rememberedEmail, $matches)) {
            $rememberedEmail = $matches[0];
        } else {
            $rememberedEmail = '';
        }

        $rememberedPassword = '';
        if ($rememberedEmail && $request->hasCookie('remembered_admin_password')) {
            $rawPass = (string) $request->cookie('remembered_admin_password');
            if (str_contains($rawPass, '|')) {
                $parts = explode('|', $rawPass);
                $rawPass = end($parts);
            }
            $decoded = base64_decode($rawPass, true);
            if ($decoded !== false) {
                $rememberedPassword = $decoded;
            }
        }

        return Inertia::render('Auth/AdminLogin', compact('rememberedEmail', 'rememberedPassword'));
    }

    /**
     * Handle Admin login (both Sanctum API token & Web Inertia session).
     */
    public function adminLogin(AdminLoginRequest $request): JsonResponse|RedirectResponse
    {
        // Handle API request (Sanctum Token)
        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            $admin = Admin::where('email', $request->email)->first();

            if (! $admin || ! Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid admin credentials.',
                ], 422);
            }

            $token = $admin->createToken('admin-api-token', ['role:admin'])->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Admin authenticated successfully.',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'admin' => new AdminResource($admin),
                ],
            ]);
        }

        // Handle Web Session Login (Inertia)
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if ($remember) {
                Cookie::queue('remembered_admin_email', $request->email, 60 * 24 * 30);
                Cookie::queue('remembered_admin_password', base64_encode($request->password), 60 * 24 * 30);
            } else {
                Cookie::queue(Cookie::forget('remembered_admin_email'));
                Cookie::queue(Cookie::forget('remembered_admin_password'));
            }

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome back, ' . Auth::guard('admin')->user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Admin Web Logout.
     */
    public function adminLogout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('info', 'You have been logged out.');
    }

    /**
     * Show Staff Login form (Inertia).
     */
    public function showStaffLoginForm(Request $request): Response|RedirectResponse
    {
        if (Auth::guard('staff')->check()) {
            return redirect()->route('staff.dashboard');
        }

        $rememberedEmail = (string) $request->cookie('remembered_staff_email', '');

        if (str_contains($rememberedEmail, '|')) {
            $parts = explode('|', $rememberedEmail);
            $rememberedEmail = end($parts);
        }

        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $rememberedEmail, $matches)) {
            $rememberedEmail = $matches[0];
        } else {
            $rememberedEmail = '';
        }

        $rememberedPassword = '';
        if ($rememberedEmail && $request->hasCookie('remembered_staff_password')) {
            $rawPass = (string) $request->cookie('remembered_staff_password');
            if (str_contains($rawPass, '|')) {
                $parts = explode('|', $rawPass);
                $rawPass = end($parts);
            }
            $decoded = base64_decode($rawPass, true);
            if ($decoded !== false) {
                $rememberedPassword = $decoded;
            }
        }

        return Inertia::render('Auth/StaffLogin', compact('rememberedEmail', 'rememberedPassword'));
    }

    /**
     * Handle Staff login (both Sanctum API token & Web Inertia session).
     */
    public function staffLogin(StaffLoginRequest $request): JsonResponse|RedirectResponse
    {
        // Handle API request (Sanctum Token)
        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            $staff = Staff::where('email', $request->email)->first();

            if (! $staff || ! Hash::check($request->password, $staff->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid staff credentials.',
                ], 422);
            }

            if (! $staff->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated. Please contact an administrator.',
                    'account_status' => 'inactive',
                ], 403);
            }

            if (! $staff->isVerified()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account email is unverified. Please verify your email before accessing the portal.',
                    'account_status' => 'unverified',
                ], 403);
            }

            $token = $staff->createToken('staff-api-token', ['role:staff'])->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Staff authenticated successfully.',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'staff' => new StaffResource($staff),
                ],
            ]);
        }

        // Handle Web Session Login (Inertia)
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::guard('staff')->validate($credentials)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $staff = Staff::where('email', $credentials['email'])->first();

        if (! $staff->isActive()) {
            return back()->withErrors([
                'email' => 'Your account is deactivated. Please contact an administrator.',
            ])->onlyInput('email');
        }

        if (! $staff->isVerified()) {
            return back()->withErrors([
                'email' => 'Your account email is unverified. Verification is required to access your tasks.',
            ])->onlyInput('email');
        }

        Auth::guard('staff')->attempt($credentials, $remember);
        $request->session()->regenerate();

        if ($remember) {
            Cookie::queue('remembered_staff_email', $request->email, 60 * 24 * 30);
            Cookie::queue('remembered_staff_password', base64_encode($request->password), 60 * 24 * 30);
        } else {
            Cookie::queue(Cookie::forget('remembered_staff_email'));
            Cookie::queue(Cookie::forget('remembered_staff_password'));
        }

        return redirect()->intended(route('staff.dashboard'))
            ->with('success', 'Welcome back, ' . $staff->name . '!');
    }

    /**
     * Staff Web Logout.
     */
    public function staffLogout(Request $request): RedirectResponse
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login')->with('info', 'You have been logged out.');
    }

    /**
     * Get authenticated user profile (API Sanctum).
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user instanceof Admin) {
            return response()->json([
                'success' => true,
                'role' => 'admin',
                'data' => new AdminResource($user),
            ]);
        }

        if ($user instanceof Staff) {
            return response()->json([
                'success' => true,
                'role' => 'staff',
                'data' => new StaffResource($user->loadCount('tasks')),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized.',
        ], 401);
    }

    /**
     * Revoke current access token (API Sanctum).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully. Token revoked.',
        ]);
    }
}
