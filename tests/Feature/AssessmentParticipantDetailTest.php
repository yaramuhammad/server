<?php

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\RetakeGrant;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * An assessment owned by a regular admin with two tests, and a participant with
 * no portal account who has only completed the first one.
 */
function seedAssessmentParticipant(): array
{
    $owner = User::factory()->create(['role' => 'admin']);

    $tests = collect(['First', 'Second'])->map(fn ($title) => Test::create([
        'user_id' => $owner->id,
        'title' => ['en' => $title, 'ar' => $title],
        'status' => 'published',
        'scale_config' => ['min' => 1, 'max' => 5],
        'scoring_type' => 'simple',
        'scoring_config' => [],
    ]));

    $assessment = Assessment::create(['user_id' => $owner->id, 'title' => ['en' => 'A', 'ar' => 'أ'], 'status' => 'published']);
    $tests->each(fn ($test, $index) => $assessment->tests()->attach($test->id, ['sort_order' => $index]));

    $link = AssessmentLink::create(['assessment_id' => $assessment->id, 'created_by' => $owner->id, 'is_active' => true]);
    $participant = Participant::create(['assessment_link_id' => $link->id, 'name' => 'P']);

    $attempt = TestAttempt::create([
        'participant_id' => $participant->id,
        'test_id' => $tests[0]->id,
        'assessment_id' => $assessment->id,
        'assessment_link_id' => $link->id,
        'status' => 'completed',
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    return [$owner, $assessment, $participant, $attempt, $tests];
}

it('shows the assessment owner each test status for a participant without an account', function () {
    [$owner, $assessment, $participant, $attempt] = seedAssessmentParticipant();

    $this->actingAs($owner, 'sanctum')
        ->getJson("/api/admin/assessments/{$assessment->uuid}/participants/{$participant->uuid}")
        ->assertOk()
        ->assertJsonPath('data.participant.id', $participant->uuid)
        ->assertJsonPath('data.tests.0.test.title', 'First')
        ->assertJsonPath('data.tests.0.status', 'completed')
        ->assertJsonPath('data.tests.0.attempt.id', $attempt->uuid)
        ->assertJsonPath('data.tests.1.test.title', 'Second')
        ->assertJsonPath('data.tests.1.status', 'not_started')
        ->assertJsonPath('data.tests.1.attempt', null);
});

it('keeps the completed result while a retake is in progress, then reports the newer one', function () {
    [$owner, $assessment, $participant, $previous, $tests] = seedAssessmentParticipant();
    $url = "/api/admin/assessments/{$assessment->uuid}/participants/{$participant->uuid}";

    $this->travel(1)->days();

    RetakeGrant::create([
        'participant_id' => $participant->id,
        'assessment_id' => $assessment->id,
        'granted_by' => $owner->id,
        'granted_at' => now(),
    ]);

    $retake = TestAttempt::create([
        'participant_id' => $participant->id,
        'test_id' => $tests[0]->id,
        'assessment_id' => $assessment->id,
        'assessment_link_id' => $participant->assessment_link_id,
        'status' => 'in_progress',
        'started_at' => now(),
    ]);

    $this->actingAs($owner, 'sanctum')->getJson($url)
        ->assertJsonPath('data.tests.0.attempt.id', $previous->uuid);

    $retake->update(['status' => 'completed', 'completed_at' => now()]);

    $this->actingAs($owner, 'sanctum')->getJson($url)
        ->assertJsonPath('data.tests.0.attempt.id', $retake->uuid);
});

it('lets the assessment owner download the participant profile PDF', function () {
    [$owner, $assessment, $participant] = seedAssessmentParticipant();

    $this->actingAs($owner, 'sanctum')
        ->get("/api/admin/assessments/{$assessment->uuid}/participants/{$participant->uuid}/psycho-profile/pdf")
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

it('forbids an admin who does not own the assessment', function () {
    [, $assessment, $participant] = seedAssessmentParticipant();
    $otherAdmin = User::factory()->create(['role' => 'admin']);
    $url = "/api/admin/assessments/{$assessment->uuid}/participants/{$participant->uuid}";

    $this->actingAs($otherAdmin, 'sanctum')->getJson($url)->assertForbidden();
    $this->actingAs($otherAdmin, 'sanctum')->get("{$url}/psycho-profile/pdf")->assertForbidden();
});

it('does not expose a participant through an assessment they are not part of', function () {
    [$owner, $assessment] = seedAssessmentParticipant();
    [, , $foreignParticipant] = seedAssessmentParticipant();
    $url = "/api/admin/assessments/{$assessment->uuid}/participants/{$foreignParticipant->uuid}";

    $this->actingAs($owner, 'sanctum')->getJson($url)->assertNotFound();
    $this->actingAs($owner, 'sanctum')->getJson("{$url}/psycho-profile/pdf")->assertNotFound();
});
