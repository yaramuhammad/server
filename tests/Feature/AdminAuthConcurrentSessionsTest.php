<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not revoke an existing token when the same account logs in again', function () {
    $user = User::factory()->create([
        'password' => 'password',
        'role' => 'admin',
        'is_active' => true,
    ]);

    // First device logs in.
    $first = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk()->json('data.token');

    // Second device logs in with the same account.
    $second = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk()->json('data.token');

    expect($first)->not->toBe($second);

    // Both tokens must still work — logging in from device 2 must not have
    // revoked device 1's session.
    $this->withHeader('Authorization', "Bearer {$first}")
        ->getJson('/api/auth/me')
        ->assertOk();

    $this->withHeader('Authorization', "Bearer {$second}")
        ->getJson('/api/auth/me')
        ->assertOk();

    expect($user->tokens()->count())->toBe(2);
});

it('logout only revokes the calling device\'s own token', function () {
    $user = User::factory()->create([
        'password' => 'password',
        'role' => 'admin',
        'is_active' => true,
    ]);

    $first = $this->postJson('/api/auth/login', [
        'email' => $user->email, 'password' => 'password',
    ])->json('data.token');
    $this->postJson('/api/auth/login', [
        'email' => $user->email, 'password' => 'password',
    ])->json('data.token');

    expect($user->tokens()->count())->toBe(2);

    $this->withHeader('Authorization', "Bearer {$first}")
        ->postJson('/api/auth/logout')
        ->assertOk();

    // Exactly the calling device's token is gone; the other session's token
    // row is untouched.
    expect($user->tokens()->count())->toBe(1);
});

it('deactivating a user revokes all of that user\'s sessions', function () {
    $actor = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
    $user = User::factory()->create(['password' => 'password', 'role' => 'admin', 'is_active' => true]);

    $this->postJson('/api/auth/login', [
        'email' => $user->email, 'password' => 'password',
    ]);
    $this->postJson('/api/auth/login', [
        'email' => $user->email, 'password' => 'password',
    ]);
    expect($user->tokens()->count())->toBe(2);

    $actorToken = $actor->createToken('api-token')->plainTextToken;

    $this->withHeader('Authorization', "Bearer {$actorToken}")
        ->putJson("/api/admin/users/{$user->uuid}", ['is_active' => false])
        ->assertOk();

    expect($user->fresh()->tokens()->count())->toBe(0);
});
