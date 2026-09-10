<?php

use App\Models\Question;
use App\Models\Response;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeQuestionAndAttempt(array $questionAttrs = [], array $scaleConfig = ['min' => 1, 'max' => 5]): array
{
    $user = User::factory()->create();
    $test = Test::create([
        'user_id' => $user->id,
        'title' => ['en' => 'T', 'ar' => 'ت'],
        'status' => 'published',
        'scale_config' => $scaleConfig,
        'scoring_type' => 'simple',
        'scoring_config' => [],
    ]);
    $question = Question::create(array_merge([
        'test_id' => $test->id,
        'text' => ['en' => 'Q', 'ar' => 'س'],
        'sort_order' => 0,
        'is_required' => true,
    ], $questionAttrs));

    $assessment = Assessment::create(['user_id' => $user->id, 'title' => ['en' => 'A', 'ar' => 'أ'], 'status' => 'published']);
    $link = AssessmentLink::create(['assessment_id' => $assessment->id, 'created_by' => $user->id, 'is_active' => true]);
    $participant = Participant::create(['assessment_link_id' => $link->id, 'name' => 'P']);
    $attempt = TestAttempt::create([
        'participant_id' => $participant->id,
        'test_id' => $test->id,
        'assessment_id' => $assessment->id,
        'assessment_link_id' => $link->id,
        'status' => 'in_progress',
        'started_at' => now(),
    ]);

    return [$question, $attempt];
}

it('computes scored_value on create from the question scale', function () {
    [$question, $attempt] = makeQuestionAndAttempt();

    $r = Response::create([
        'test_attempt_id' => $attempt->id,
        'question_id' => $question->id,
        'value' => 4,
    ]);

    expect($r->scored_value)->toBe(4);
});

it('recomputes scored_value when the raw value is changed on an existing response', function () {
    [$question, $attempt] = makeQuestionAndAttempt(['is_reverse_scored' => true]); // scored = 6 - raw

    $r = Response::create([
        'test_attempt_id' => $attempt->id,
        'question_id' => $question->id,
        'value' => 5,
    ]);
    expect($r->scored_value)->toBe(1); // 6 - 5

    // participant revises the answer
    $r->update(['value' => 2]);
    $r->refresh();

    expect($r->value)->toBe(2)
        ->and($r->scored_value)->toBe(4); // 6 - 2, was previously left at 1 (the bug)
});

it('recomputes scored_value on updateOrCreate for an already-answered question', function () {
    [$question, $attempt] = makeQuestionAndAttempt(['correct_answer' => 3]); // scored 1 if correct else 0

    Response::updateOrCreate(
        ['test_attempt_id' => $attempt->id, 'question_id' => $question->id],
        ['value' => 1], // wrong
    );
    expect(Response::first()->scored_value)->toBe(0);

    Response::updateOrCreate(
        ['test_attempt_id' => $attempt->id, 'question_id' => $question->id],
        ['value' => 3], // now correct
    );

    $r = Response::first();
    expect($r->value)->toBe(3)
        ->and($r->scored_value)->toBe(1);
});

it('does not override an explicitly provided scored_value', function () {
    [$question, $attempt] = makeQuestionAndAttempt();

    $r = Response::create([
        'test_attempt_id' => $attempt->id,
        'question_id' => $question->id,
        'value' => 5,
        'scored_value' => 99,
    ]);
    expect($r->scored_value)->toBe(99);

    $r->update(['value' => 1, 'scored_value' => 42]);
    $r->refresh();
    expect($r->scored_value)->toBe(42);
});
