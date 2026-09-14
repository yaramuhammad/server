<?php

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedAttemptOwnedBy(User $owner): TestAttempt
{
    $test = Test::create([
        'user_id' => $owner->id,
        'title' => ['en' => 'T', 'ar' => 'ت'],
        'status' => 'published',
        'scale_config' => ['min' => 1, 'max' => 5],
        'scoring_type' => 'simple',
        'scoring_config' => [],
    ]);
    $assessment = Assessment::create(['user_id' => $owner->id, 'title' => ['en' => 'A', 'ar' => 'أ'], 'status' => 'published']);
    $assessment->tests()->attach($test->id, ['sort_order' => 0]);
    $link = AssessmentLink::create(['assessment_id' => $assessment->id, 'created_by' => $owner->id, 'is_active' => true]);
    $participant = Participant::create(['assessment_link_id' => $link->id, 'name' => 'P']);

    return TestAttempt::create([
        'participant_id' => $participant->id,
        'test_id' => $test->id,
        'assessment_id' => $assessment->id,
        'assessment_link_id' => $link->id,
        'status' => 'completed',
        'started_at' => now(),
        'completed_at' => now(),
    ]);
}

it('forbids an admin from viewing another admin\'s attempt detail', function () {
    $owner = User::factory()->create(['role' => 'admin']);
    $otherAdmin = User::factory()->create(['role' => 'admin']);
    $attempt = seedAttemptOwnedBy($owner);

    $this->actingAs($otherAdmin, 'sanctum')
        ->getJson("/api/admin/attempts/{$attempt->uuid}")
        ->assertStatus(403);
});

it('forbids an admin from viewing another admin\'s attempt responses', function () {
    $owner = User::factory()->create(['role' => 'admin']);
    $otherAdmin = User::factory()->create(['role' => 'admin']);
    $attempt = seedAttemptOwnedBy($owner);

    $this->actingAs($otherAdmin, 'sanctum')
        ->getJson("/api/admin/attempts/{$attempt->uuid}/responses")
        ->assertStatus(403);
});

it('allows the owning admin to view their own attempt detail', function () {
    $owner = User::factory()->create(['role' => 'admin']);
    $attempt = seedAttemptOwnedBy($owner);

    $this->actingAs($owner, 'sanctum')
        ->getJson("/api/admin/attempts/{$attempt->uuid}")
        ->assertStatus(200);
});

it('allows a super_admin to view any admin\'s attempt detail', function () {
    $owner = User::factory()->create(['role' => 'admin']);
    $superAdmin = User::factory()->create(['role' => 'super_admin']);
    $attempt = seedAttemptOwnedBy($owner);

    $this->actingAs($superAdmin, 'sanctum')
        ->getJson("/api/admin/attempts/{$attempt->uuid}")
        ->assertStatus(200);
});
