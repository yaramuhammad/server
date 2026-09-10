<?php

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Response;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Services\ScoringEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Build a completed TestAttempt with the given questions + raw answers, so
 * ScoringEngine::calculate() can be run against real persisted rows.
 *
 * @param  array  $questions  list of question attribute arrays
 * @param  array  $answers    question array-index => raw value
 */
function makeAttempt(array $testAttrs, array $questions, array $answers): TestAttempt
{
    $user = User::factory()->create();

    $test = Test::create(array_merge([
        'user_id' => $user->id,
        'title' => ['en' => 'T', 'ar' => 'ت'],
        'status' => 'published',
        'scale_config' => ['min' => 1, 'max' => 5],
        'scoring_type' => 'simple',
        'scoring_config' => [],
    ], $testAttrs));

    $questionModels = [];
    foreach ($questions as $i => $q) {
        $questionModels[$i] = Question::create(array_merge([
            'test_id' => $test->id,
            'text' => ['en' => "Q{$i}", 'ar' => "س{$i}"],
            'sort_order' => $i,
            'is_required' => true,
        ], $q));
    }

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'title' => ['en' => 'A', 'ar' => 'أ'],
        'status' => 'published',
    ]);
    $link = AssessmentLink::create([
        'assessment_id' => $assessment->id,
        'created_by' => $user->id,
        'is_active' => true,
    ]);
    $participant = Participant::create([
        'assessment_link_id' => $link->id,
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

    foreach ($answers as $qIndex => $value) {
        Response::create([
            'test_attempt_id' => $attempt->id,
            'question_id' => $questionModels[$qIndex]->id,
            'value' => $value,
        ]);
    }

    $attempt->update(['status' => 'completed', 'completed_at' => now()]);

    return $attempt->fresh();
}

it('scores a simple test as the sum of scored values with a percentage of max', function () {
    $attempt = makeAttempt(
        ['scoring_type' => 'simple', 'scale_config' => ['min' => 1, 'max' => 5]],
        [[], [], [], []],
        [0 => 5, 1 => 4, 2 => 3, 3 => 2], // raw sum 14, max 4*5 = 20
    );

    $result = app(ScoringEngine::class)->calculate($attempt);

    expect($result['summary']['score_raw'])->toBe(14)
        ->and($result['summary']['score_max'])->toBe(20)
        ->and($result['summary']['score_percentage'])->toBe(70.0)
        ->and($result['summary']['score_average'])->toBe(3.5)
        ->and($result['details']['type'])->toBe('simple');
});

it('applies reverse scoring for flagged questions', function () {
    // scale 1..5, reverse => scored = (max+min) - raw = 6 - raw
    $attempt = makeAttempt(
        ['scoring_type' => 'simple', 'scale_config' => ['min' => 1, 'max' => 5]],
        [
            ['is_reverse_scored' => false],
            ['is_reverse_scored' => true],
        ],
        [0 => 5, 1 => 5], // 5 + (6-5=1) = 6
    );

    $result = app(ScoringEngine::class)->calculate($attempt);

    expect($result['summary']['score_raw'])->toBe(6)
        ->and($result['summary']['score_max'])->toBe(10);
});

it('scores correct_answer questions as 1/0 with max 1 per question', function () {
    $attempt = makeAttempt(
        ['scoring_type' => 'simple', 'scale_config' => ['min' => 1, 'max' => 4]],
        [
            ['correct_answer' => 2],
            ['correct_answer' => 3],
            ['correct_answer' => 1],
        ],
        [0 => 2, 1 => 4, 2 => 1], // correct, wrong, correct => raw 2
    );

    $result = app(ScoringEngine::class)->calculate($attempt);

    expect($result['summary']['score_raw'])->toBe(2)
        ->and($result['summary']['score_max'])->toBe(3)           // 3 questions * 1
        ->and($result['summary']['score_percentage'])->toBe(66.67);
});

it('groups category scoring by question category_key and picks an interpretation band', function () {
    $config = [
        'categories' => [
            [
                'key' => 'openness',
                'label' => ['en' => 'Openness', 'ar' => 'الانفتاح'],
                'interpretation' => [
                    ['min' => 0, 'max' => 50, 'label' => ['en' => 'Low', 'ar' => 'منخفض']],
                    ['min' => 51, 'max' => 100, 'label' => ['en' => 'High', 'ar' => 'مرتفع']],
                ],
            ],
            [
                'key' => 'rigor',
                'label' => ['en' => 'Rigor', 'ar' => 'الدقة'],
                'interpretation' => [
                    ['min' => 0, 'max' => 100, 'label' => ['en' => 'Any', 'ar' => 'أي']],
                ],
            ],
        ],
    ];

    $attempt = makeAttempt(
        ['scoring_type' => 'category', 'scoring_config' => $config, 'scale_config' => ['min' => 1, 'max' => 5]],
        [
            ['category_key' => 'openness'],
            ['category_key' => 'openness'],
            ['category_key' => 'rigor'],
        ],
        [0 => 5, 1 => 5, 2 => 1], // openness 10/10 = 100% High; rigor 1/5 = 20%
    );

    $result = app(ScoringEngine::class)->calculate($attempt);
    $cats = collect($result['details']['categories'])->keyBy('key');

    expect($result['details']['type'])->toBe('category')
        ->and($cats['openness']['score_percentage'])->toBe(100.0)
        ->and($cats['openness']['interpretation']['en'])->toBe('High')
        ->and($cats['rigor']['score_percentage'])->toBe(20.0)
        ->and($result['summary']['score_raw'])->toBe(11)
        ->and($result['summary']['score_max'])->toBe(15);
});

it('maps a range test total to the matching named range', function () {
    $config = [
        'use_percentage' => true,
        'ranges' => [
            ['min' => 0, 'max' => 39, 'label' => ['en' => 'Low', 'ar' => 'منخفض']],
            ['min' => 40, 'max' => 79, 'label' => ['en' => 'Medium', 'ar' => 'متوسط']],
            ['min' => 80, 'max' => 100, 'label' => ['en' => 'High', 'ar' => 'مرتفع']],
        ],
    ];

    $attempt = makeAttempt(
        ['scoring_type' => 'range', 'scoring_config' => $config, 'scale_config' => ['min' => 1, 'max' => 5]],
        [[], [], [], []],
        [0 => 3, 1 => 3, 2 => 3, 3 => 3], // 12 / 20 = 60% => Medium
    );

    $result = app(ScoringEngine::class)->calculate($attempt);

    expect($result['details']['type'])->toBe('range')
        ->and($result['details']['matched_range']['label']['en'])->toBe('Medium')
        ->and($result['summary']['score_percentage'])->toBe(60.0);
});

it('applies per-question weights in weighted scoring', function () {
    $attempt = makeAttempt(
        ['scoring_type' => 'weighted', 'scale_config' => ['min' => 1, 'max' => 5]],
        [
            ['weight' => 1.0],
            ['weight' => 2.0],
        ],
        [0 => 4, 1 => 5], // raw = 4*1 + 5*2 = 14 ; max = 5*1 + 5*2 = 15
    );

    $result = app(ScoringEngine::class)->calculate($attempt);

    expect($result['details']['type'])->toBe('weighted')
        ->and((float) $result['summary']['score_raw'])->toBe(14.0)
        ->and((float) $result['summary']['score_max'])->toBe(15.0)
        ->and($result['summary']['score_percentage'])->toBe(93.33);
});

it('does not divide by zero when there are no responses', function () {
    $attempt = makeAttempt(
        ['scoring_type' => 'simple', 'scale_config' => ['min' => 1, 'max' => 5]],
        [[], []],
        [], // no answers
    );

    $result = app(ScoringEngine::class)->calculate($attempt);

    // No responses must not blow up; all figures collapse to zero.
    expect((float) $result['summary']['score_raw'])->toBe(0.0)
        ->and((float) $result['summary']['score_percentage'])->toBe(0.0)
        ->and((float) $result['summary']['score_average'])->toBe(0.0);
});

it('uses scale_config.score_map to map raw answers to scored values', function () {
    // MCQ-style: option 1..4 map to points 0/1/2/3
    $attempt = makeAttempt(
        ['scoring_type' => 'simple', 'scale_config' => ['min' => 1, 'max' => 4, 'score_map' => ['1' => 0, '2' => 1, '3' => 2, '4' => 3]]],
        [[], []],
        [0 => 4, 1 => 2], // 3 + 1 = 4
    );

    $result = app(ScoringEngine::class)->calculate($attempt);

    expect($result['summary']['score_raw'])->toBe(4);
});
