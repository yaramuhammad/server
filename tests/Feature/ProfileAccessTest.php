<?php

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\ParticipantAccount;
use App\Models\Question;
use App\Models\Response;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Support\ParticipantSessionToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * A participant (attached to $account when given) who completed one test on a link with the
 * given profile access options.
 */
function participantOnLink(array $linkAttrs, ?ParticipantAccount $account = null): Participant
{
    $admin = User::factory()->create();

    $test = Test::create([
        'user_id' => $admin->id,
        'title' => ['en' => 'T', 'ar' => 'ت'],
        'status' => 'published',
        'scale_config' => ['min' => 1, 'max' => 5],
        'scoring_type' => 'simple',
        'scoring_config' => [],
    ]);
    $question = Question::create(['test_id' => $test->id, 'text' => ['en' => 'Q', 'ar' => 'س'], 'sort_order' => 0, 'is_required' => true]);

    $assessment = Assessment::create([
        'user_id' => $admin->id,
        'title' => ['en' => 'A', 'ar' => 'أ'],
        'status' => 'published',
    ]);
    $assessment->tests()->attach($test->id, ['sort_order' => 0]);

    $link = AssessmentLink::create(array_merge([
        'assessment_id' => $assessment->id,
        'created_by' => $admin->id,
        'is_active' => true,
    ], $linkAttrs));

    $participant = Participant::create([
        'assessment_link_id' => $link->id,
        'participant_account_id' => $account?->id,
        'name' => 'P',
    ]);

    $attempt = TestAttempt::create([
        'participant_id' => $participant->id,
        'test_id' => $test->id,
        'assessment_id' => $assessment->id,
        'assessment_link_id' => $link->id,
        'status' => 'in_progress',
        'started_at' => now()->subMinutes(5),
    ]);
    Response::create(['test_attempt_id' => $attempt->id, 'question_id' => $question->id, 'value' => 4]);
    $attempt->update(['status' => 'completed', 'completed_at' => now()]);
    $attempt->calculateScores();

    return $participant;
}

function sessionHeaders(Participant $participant): array
{
    return ['X-Session-Token' => ParticipantSessionToken::for($participant)];
}

it('reports the link profile options on the participant session', function () {
    $participant = participantOnLink(['allow_profile_view' => true, 'allow_profile_download' => false]);

    $this->getJson("/api/participate/session/{$participant->uuid}", sessionHeaders($participant))
        ->assertOk()
        ->assertJsonPath('data.can_view_results', true)
        ->assertJsonPath('data.can_download_profile', false);
});

it('only shows results when the link allows viewing the profile', function () {
    $blocked = participantOnLink(['allow_profile_view' => false]);
    $allowed = participantOnLink(['allow_profile_view' => true]);

    $this->getJson("/api/participate/session/{$blocked->uuid}/results", sessionHeaders($blocked))->assertForbidden();
    $this->getJson("/api/participate/session/{$allowed->uuid}/results", sessionHeaders($allowed))->assertOk();
});

it('only serves the profile PDF to link participants when the link allows downloading', function () {
    $blocked = participantOnLink(['allow_profile_view' => true, 'allow_profile_download' => false]);
    $allowed = participantOnLink(['allow_profile_view' => false, 'allow_profile_download' => true]);

    $this->get("/api/participate/session/{$blocked->uuid}/profile/pdf", sessionHeaders($blocked))->assertForbidden();
    $this->get("/api/participate/session/{$allowed->uuid}/profile/pdf", sessionHeaders($allowed))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

it('hides portal scores and profile entries for links that do not allow viewing', function () {
    $account = ParticipantAccount::create(['name' => 'Acc', 'email' => 'acc@example.com', 'password' => 'secret123']);
    participantOnLink(['allow_profile_view' => false], $account);
    participantOnLink(['allow_profile_view' => true], $account);

    $assessments = $this->actingAs($account, 'participant')->getJson('/api/portal/assessments')->assertOk()->json('data');
    $byAccess = collect($assessments)->keyBy(fn ($a) => $a['can_view_results'] ? 'view' : 'hidden');

    expect($byAccess['hidden']['tests'][0]['score_percentage'])->toBeNull()
        ->and($byAccess['view']['tests'][0]['score_percentage'])->not->toBeNull();

    $this->actingAs($account, 'participant')->getJson('/api/portal/combined-results')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('refuses the portal profile PDF unless at least one link allows downloading', function () {
    $account = ParticipantAccount::create(['name' => 'Acc', 'email' => 'acc@example.com', 'password' => 'secret123']);
    participantOnLink(['allow_profile_view' => true, 'allow_profile_download' => false], $account);

    $this->actingAs($account, 'participant')->get('/api/portal/psycho-profile/pdf')->assertForbidden();

    participantOnLink(['allow_profile_download' => true], $account);

    $this->actingAs($account, 'participant')->get('/api/portal/psycho-profile/pdf')
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
