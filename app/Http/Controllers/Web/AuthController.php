<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\StaffLoginRequest;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Landing / Welcome Page.
     */
    public function home(): View
    {
        return view('home');
    }

    /**
     * Show Admin Login form.
     */
    public function showAdminLoginForm(Request $request): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        $rememberedEmail = (string) $request->cookie('remembered_admin_email', '');

        // If cookie has a signed hash prefix (e.g. hash|email@example.com), extract email part
        if (str_contains($rememberedEmail, '|')) {
            $parts = explode('|', $rememberedEmail);
            $rememberedEmail = end($parts);
        }

        // Strictly extract valid email pattern to prevent any tokens/hashes from appearing
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

        // Demo admin fallback if email is remembered but password was not saved previously
        if ($rememberedEmail === 'admin@example.com' && empty($rememberedPassword)) {
            $rememberedPassword = 'password123';
        }

        return view('auth.admin-login', compact('rememberedEmail', 'rememberedPassword'));
    }

    /**
     * Handle Admin Login submission.
     */
    public function adminLogin(AdminLoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if ($remember) {
                \Illuminate\Support\Facades\Cookie::queue('remembered_admin_email', $request->email, 60 * 24 * 30);
                \Illuminate\Support\Facades\Cookie::queue('remembered_admin_password', base64_encode($request->password), 60 * 24 * 30);
            } else {
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('remembered_admin_email'));
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('remembered_admin_password'));
            }

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome back, ' . Auth::guard('admin')->user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Admin Logout.
     */
    public function adminLogout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('info', 'You have been logged out.');
    }

    /**
     * Show Staff Login form.
     */
    public function showStaffLoginForm(Request $request): View|RedirectResponse
    {
        if (Auth::guard('staff')->check()) {
            return redirect()->route('staff.dashboard');
        }

        $rememberedEmail = (string) $request->cookie('remembered_staff_email', '');

        // If cookie has a signed hash prefix (e.g. hash|email@example.com), extract email part
        if (str_contains($rememberedEmail, '|')) {
            $parts = explode('|', $rememberedEmail);
            $rememberedEmail = end($parts);
        }

        // Strictly extract valid email pattern to prevent any tokens/hashes from appearing
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

        return view('auth.staff-login', compact('rememberedEmail', 'rememberedPassword'));
    }

    /**
     * Handle Staff Login submission.
     */
    public function staffLogin(StaffLoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Check credentials
        if (! Auth::guard('staff')->validate($credentials)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $staff = Staff::where('email', $credentials['email'])->first();

        // Enforce account status check
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
            \Illuminate\Support\Facades\Cookie::queue('remembered_staff_email', $request->email, 60 * 24 * 30);
            \Illuminate\Support\Facades\Cookie::queue('remembered_staff_password', base64_encode($request->password), 60 * 24 * 30);
        } else {
            \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('remembered_staff_email'));
            \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('remembered_staff_password'));
        }

        return redirect()->intended(route('staff.dashboard'))
            ->with('success', 'Welcome back, ' . $staff->name . '!');
    }

    /**
     * Staff Logout.
     */
    public function staffLogout(Request $request): RedirectResponse
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login')->with('info', 'You have been logged out.');
    }
}
