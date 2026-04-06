<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials.', 401);
        }

        if (!$user->is_active) {
            return $this->error('Your account has been deactivated.', 403);
        }

        // Revoke existing tokens to enforce single active session
        $user->tokens()->where('name', 'api-token')->delete();

        $abilities = $user->isSuperAdmin() ? ['*'] : ['admin'];
        $token = $user->createToken('api-token', $abilities)->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successful.');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully.');
    }

    public function refresh(Request $request)
    {
        $user = $request->user();

        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        $abilities = $user->isSuperAdmin() ? ['*'] : ['admin'];
        $token = $user->createToken('api-token', $abilities)->plainTextToken;

        return $this->success([
            'token' => $token,
        ], 'Token refreshed successfully.');
    }

    public function me(Request $request)
    {
        return $this->success(new UserResource($request->user()));
    }
}
