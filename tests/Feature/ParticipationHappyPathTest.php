<?php

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Support\ParticipantSessionToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('walks a participant through register, start, answer, complete, and view results', function () {
    $admin = User::factory()->create();

    $test = Test::create([
        'user_id' => $admin->id,
        'title' => ['en' => 'Quick Test', 'ar' => 'اختبار سريع'],
        'status' => 'published',
        'scale_config' => ['min' => 1, 'max' => 5],
        'scoring_type' => 'simple',
        'scoring_config' => [],
    ]);

    $q1 = Question::create(['test_id' => $test->id, 'text' => ['en' => 'Q1', 'ar' => 'س1'], 'sort_order' => 0, 'is_required' => true]);
    $q2 = Question::create(['test_id' => $test->id, 'text' => ['en' => 'Q2', 'ar' => 'س2'], 'sort_order' => 1, 'is_required' => true]);

    $assessment = Assessment::create([
        'user_id' => $admin->id,
        'title' => ['en' => 'Assessment', 'ar' => 'تقييم'],
        'status' => 'published',
        'show_results_to_participant' => true,
    ]);
    $assessment->tests()->attach($test->id, ['sort_order' => 0]);

    $link = AssessmentLink::create([
        'assessment_id' => $assessment->id,
        'created_by' => $admin->id,
        'is_active' => true,
    ]);

    // 1. Register (only collect_name is on by default).
    $registerResponse = $this->postJson("/api/participate/{$link->token}/register", [
        'name' => 'Jane Participant',
    ])->assertStatus(201);

    $participantUuid = $registerResponse->json('data.id');
    $participant = Participant::where('uuid', $participantUuid)->firstOrFail();

    $headers = ['X-Session-Token' => ParticipantSessionToken::for($participant)];

    // 2. Start the test.
    $startResponse = $this->getJson(
        "/api/participate/session/{$participant->uuid}/test/{$test->uuid}",
        $headers
    )->assertStatus(201);

    expect($startResponse->json('data.questions'))->toHaveCount(2);

    // 3. Submit answers for both questions.
    $this->postJson(
        "/api/participate/session/{$participant->uuid}/test/{$test->uuid}/responses",
        [
            'responses' => [
                ['question_id' => $q1->id, 'value' => 4],
                ['question_id' => $q2->id, 'value' => 2],
            ],
        ],
        $headers
    )->assertStatus(200);

    // 4. Complete the test.
    $completeResponse = $this->postJson(
        "/api/participate/session/{$participant->uuid}/test/{$test->uuid}/complete",
        [],
        $headers
    )->assertStatus(200);

    expect($completeResponse->json('data.status'))->toBe('completed');
    expect((int) $completeResponse->json('data.score_raw'))->toBe(6);

    $attempt = TestAttempt::where('participant_id', $participant->id)->where('test_id', $test->id)->firstOrFail();
    expect($attempt->status)->toBe('completed');
    expect($attempt->responses()->count())->toBe(2);

    // 5. Fetch results.
    $resultsResponse = $this->getJson(
        "/api/participate/session/{$participant->uuid}/results",
        $headers
    )->assertStatus(200);

    expect($resultsResponse->json('data.results'))->toHaveCount(1);
});

it('refuses to complete a test with unanswered required questions', function () {
    $admin = User::factory()->create();

    $test = Test::create([
        'user_id' => $admin->id,
        'title' => ['en' => 'T', 'ar' => 'ت'],
        'status' => 'published',
        'scale_config' => ['min' => 1, 'max' => 5],
        'scoring_type' => 'simple',
        'scoring_config' => [],
    ]);
    Question::create(['test_id' => $test->id, 'text' => ['en' => 'Q1', 'ar' => 'س1'], 'sort_order' => 0, 'is_required' => true]);
    Question::create(['test_id' => $test->id, 'text' => ['en' => 'Q2', 'ar' => 'س2'], 'sort_order' => 1, 'is_required' => true]);

    $assessment = Assessment::create(['user_id' => $admin->id, 'title' => ['en' => 'A', 'ar' => 'أ'], 'status' => 'published']);
    $assessment->tests()->attach($test->id, ['sort_order' => 0]);
    $link = AssessmentLink::create(['assessment_id' => $assessment->id, 'created_by' => $admin->id, 'is_active' => true]);
    $participant = Participant::create(['assessment_link_id' => $link->id, 'name' => 'P']);

    $headers = ['X-Session-Token' => ParticipantSessionToken::for($participant)];

    $this->getJson("/api/participate/session/{$participant->uuid}/test/{$test->uuid}", $headers)
        ->assertStatus(201);

    // Only one of two required questions gets answered.
    $this->postJson(
        "/api/participate/session/{$participant->uuid}/test/{$test->uuid}/complete",
        [],
        $headers
    )->assertStatus(422);

    $attempt = TestAttempt::where('participant_id', $participant->id)->firstOrFail();
    expect($attempt->status)->toBe('in_progress');
});
