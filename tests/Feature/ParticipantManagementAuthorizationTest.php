<?php

use App\Models\ParticipantAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedParticipantAccount(): ParticipantAccount
{
    return ParticipantAccount::create([
        'name' => 'Jane Participant',
        'email' => 'jane@example.com',
        'password' => bcrypt('password'),
    ]);
}

it('forbids a regular admin from listing participant accounts', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin, 'sanctum')
        ->getJson('/api/admin/participants')
        ->assertStatus(403);
});

it('allows a super_admin to list participant accounts', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);

    $this->actingAs($superAdmin, 'sanctum')
        ->getJson('/api/admin/participants')
        ->assertStatus(200);
});

it('forbids a regular admin from viewing another tenant\'s participant account', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $account = seedParticipantAccount();

    $this->actingAs($admin, 'sanctum')
        ->getJson("/api/admin/participants/{$account->uuid}")
        ->assertStatus(403);
});

it('forbids a regular admin from deleting a participant account', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $account = seedParticipantAccount();

    $this->actingAs($admin, 'sanctum')
        ->deleteJson("/api/admin/participant-accounts/{$account->uuid}")
        ->assertStatus(403);

    $this->assertDatabaseHas('participant_accounts', ['id' => $account->id]);
});

it('allows a super_admin to delete a participant account', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);
    $account = seedParticipantAccount();

    $this->actingAs($superAdmin, 'sanctum')
        ->deleteJson("/api/admin/participant-accounts/{$account->uuid}")
        ->assertStatus(200);

    $this->assertDatabaseMissing('participant_accounts', ['id' => $account->id]);
});
