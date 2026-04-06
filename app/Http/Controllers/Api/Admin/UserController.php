<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreUserRequest;
use App\Http\Requests\Api\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->orderByDesc('created_at')->paginate(20);

        return UserResource::collection($users)->additional([
            'success' => true,
            'message' => 'Success',
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $user = User::create($request->validated());

        return $this->success(new UserResource($user), 'User created.', 201);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return $this->success(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        // Prevent changing own role or deactivating self
        $authUser = $request->user();
        if ($authUser->id === $user->id) {
            $data = collect($request->validated())
                ->except(['role', 'is_active'])
                ->toArray();
        } else {
            $data = $request->validated();
        }

        $user->update($data);

        // Revoke tokens if user is being deactivated
        if (isset($data['is_active']) && !$data['is_active']) {
            $user->tokens()->delete();
        }

        return $this->success(new UserResource($user), 'User updated.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->tokens()->delete();
        $user->delete();

        return $this->success(null, 'User deleted.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => $request->input('password')]);

        return $this->success(null, 'Password reset successfully.');
    }
}
