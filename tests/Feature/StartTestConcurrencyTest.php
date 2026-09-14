<?php

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Support\ParticipantSessionToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedStartableSession(): array
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

    return [$participant, $link, $test];
}

it('reuses the same in-progress attempt when start-test is called twice for the same participant+test', function () {
    [$participant, , $test] = seedStartableSession();

    $headers = ['X-Session-Token' => ParticipantSessionToken::for($participant)];

    // The first call creates the attempt (201); the second finds it already
    // exists and returns it as-is (200).
    $first = $this->getJson("/api/participate/session/{$participant->uuid}/test/{$test->uuid}", $headers)
        ->assertStatus(201);
    $second = $this->getJson("/api/participate/session/{$participant->uuid}/test/{$test->uuid}", $headers)
        ->assertStatus(200);

    // Both calls must resolve to the same underlying attempt — the lock
    // around the check-then-create must not leave a second, orphaned
    // in-progress attempt behind.
    expect($first->json('data.attempt.id'))->toBe($second->json('data.attempt.id'));
    expect(TestAttempt::where('participant_id', $participant->id)->where('test_id', $test->id)->count())->toBe(1);
});
