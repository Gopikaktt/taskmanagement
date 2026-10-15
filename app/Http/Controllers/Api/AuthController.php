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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Admin login to obtain Sanctum API token.
     */
    public function adminLogin(AdminLoginRequest $request): JsonResponse
    {
        $admin = Admin::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid admin credentials.',
            ], 422);
        }

        // Create token with admin abilities
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

    /**
     * Staff login to obtain Sanctum API token.
     */
    public function staffLogin(StaffLoginRequest $request): JsonResponse
    {
        $staff = Staff::where('email', $request->email)->first();

        if (! $staff || ! Hash::check($request->password, $staff->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid staff credentials.',
            ], 422);
        }

        // Account eligibility check at login
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

        // Create token with staff abilities
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

    /**
     * Get authenticated user profile.
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
     * Revoke current access token.
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
