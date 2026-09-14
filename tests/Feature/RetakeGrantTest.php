<?php

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\RetakeGrant;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Support\ParticipantSessionToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedRetakeableSession(): array
{
    $user = User::factory()->create();
    $test = Test::create([
        'user_id' => $user->id,
        'title' => ['en' => 'T', 'ar' => 'ت'],
        'status' => 'published',
        'scale_config' => ['min' => 1, 'max' => 5],
        'scoring_type' => 'simple',
        'scoring_config' => [],
    ]);
    $assessment = Assessment::create(['user_id' => $user->id, 'title' => ['en' => 'A', 'ar' => 'أ'], 'status' => 'published']);
    $assessment->tests()->attach($test->id, ['sort_order' => 0]);
    $link = AssessmentLink::create(['assessment_id' => $assessment->id, 'created_by' => $user->id, 'is_active' => true]);
    $participant = Participant::create(['assessment_link_id' => $link->id, 'name' => 'P']);

    return [$participant, $link, $test, $assessment, $user];
}

it('marks a retake grant used once the participant starts a new attempt', function () {
    [$participant, , $test, $assessment, $user] = seedRetakeableSession();

    // Complete an initial attempt so there's a "previous round" to retake.
    // created_at is Eloquent's own auto-timestamp, not started_at/completed_at
    // — travel back in time while creating it so the grant's granted_at
    // (now) unambiguously falls after it.
    $this->travelTo(now()->subDay(), function () use ($participant, $test, $assessment) {
        TestAttempt::create([
            'participant_id' => $participant->id,
            'test_id' => $test->id,
            'assessment_id' => $assessment->id,
            'assessment_link_id' => $participant->assessment_link_id,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
        ]);
    });

    $grant = RetakeGrant::create([
        'participant_id' => $participant->id,
        'assessment_id' => $assessment->id,
        'granted_by' => $user->id,
        'granted_at' => now(),
    ]);

    expect($grant->isUsed())->toBeFalse();

    $headers = ['X-Session-Token' => ParticipantSessionToken::for($participant)];
    $this->getJson("/api/participate/session/{$participant->uuid}/test/{$test->uuid}", $headers)
        ->assertStatus(201);

    expect($grant->fresh()->isUsed())->toBeTrue();
});

it('does not touch an already-used retake grant when the participant resumes an in-progress attempt', function () {
    [$participant, , $test, $assessment, $user] = seedRetakeableSession();

    $usedAt = now()->subHour();
    $grant = RetakeGrant::create([
        'participant_id' => $participant->id,
        'assessment_id' => $assessment->id,
        'granted_by' => $user->id,
        'granted_at' => now()->subDay(),
        'used_at' => $usedAt,
    ]);

    // An attempt already in progress for the current round.
    TestAttempt::create([
        'participant_id' => $participant->id,
        'test_id' => $test->id,
        'assessment_id' => $assessment->id,
        'assessment_link_id' => $participant->assessment_link_id,
        'status' => 'in_progress',
        'started_at' => now(),
    ]);

    $headers = ['X-Session-Token' => ParticipantSessionToken::for($participant)];
    $this->getJson("/api/participate/session/{$participant->uuid}/test/{$test->uuid}", $headers)
        ->assertStatus(200);

    expect($grant->fresh()->used_at->timestamp)->toBe($usedAt->timestamp);
});
