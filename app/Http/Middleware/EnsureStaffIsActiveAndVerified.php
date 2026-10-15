<?php

namespace App\Http\Middleware;

use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffIsActiveAndVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Try getting user from staff guard or default
        $user = Auth::guard('staff')->user() ?? $request->user();

        if (! $user instanceof Staff) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Staff authentication required.',
                ], 401);
            }

            return redirect()->route('staff.login');
        }

        // Check if account is active
        if (! $user->isActive()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated. Please contact an administrator.',
                    'account_status' => 'inactive',
                ], 403);
            }

            Auth::guard('staff')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.login')
                ->with('error', 'Your account has been deactivated. Please contact an administrator.');
        }

        // Check if account email is verified
        if (! $user->isVerified()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account email is unverified. Verification is required to access tasks.',
                    'account_status' => 'unverified',
                ], 403);
            }

            Auth::guard('staff')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.login')
                ->with('error', 'Your account email is unverified. Verification is required to access tasks.');
        }

        return $next($request);
    }
}
