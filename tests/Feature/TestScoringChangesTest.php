<?php

use App\Models\Assessment;
use App\Models\Participant;
use App\Models\Response;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use Database\Seeders\CreativityTestSeeder;
use Database\Seeders\StudentAbilities\CrossCulturalReadinessBattery;
use Database\Seeders\StudentAbilities\HollandVocationalScale;
use Database\Seeders\StudentAbilitiesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function runDataMigration(string $file): void
{
    (require database_path("migrations/{$file}"))->up();
}

/** A completed attempt on $test answering every question with $answer($question). */
function completedAttempt(Test $test, callable $answer): TestAttempt
{
    $assessment = Assessment::create(['user_id' => $test->user_id, 'title' => ['en' => 'A', 'ar' => 'أ'], 'status' => 'published']);
    $assessment->tests()->attach($test->id, ['sort_order' => 1]);
    $link = $assessment->links()->create(['created_by' => $test->user_id, 'is_active' => true]);
    $participant = Participant::create(['assessment_link_id' => $link->id, 'name' => 'P']);

    $attempt = TestAttempt::create([
        'participant_id' => $participant->id,
        'test_id' => $test->id,
        'assessment_id' => $assessment->id,
        'assessment_link_id' => $link->id,
        'status' => 'in_progress',
        'started_at' => now()->subMinutes(10),
    ]);
    foreach ($test->questions()->orderBy('sort_order')->get() as $question) {
        Response::create(['test_attempt_id' => $attempt->id, 'question_id' => $question->id, 'value' => $answer($question)]);
    }
    $attempt->update(['status' => 'completed', 'completed_at' => now()]);
    $attempt->calculateScores();

    return $attempt->fresh();
}

function seedCreativityTest(): Test
{
    User::factory()->create();
    test()->seed(CreativityTestSeeder::class);

    return Test::where('title->ar', CreativityTestSeeder::TITLE_AR)->firstOrFail();
}

it('scores the creativity test 5 / 3 / 1 towards the creative answer, out of 150', function () {
    $test = seedCreativityTest();

    // "Agree" everywhere: 18 creative-agree items at 5, 12 reverse items at 1.
    $agree = completedAttempt($test, fn () => 1);
    expect((float) $agree->score_raw)->toBe(102.0)
        ->and((float) $agree->score_max)->toBe(150.0)
        ->and($agree->score_details['matched_range']['label']['en'])->toBe('Above Average');

    $neutral = completedAttempt($test, fn () => 2);
    expect((float) $neutral->score_raw)->toBe(90.0)
        ->and($neutral->score_details['matched_range']['label']['en'])->toBe('Average');

    $mostCreative = completedAttempt($test, fn ($q) => in_array($q->sort_order, CreativityTestSeeder::AGREE_IS_CREATIVE, true) ? 1 : 3);
    expect((float) $mostCreative->score_raw)->toBe(150.0)
        ->and($mostCreative->score_details['matched_range']['label']['en'])->toBe('Exceptionally Creative');
});

it('re-scores existing creativity answers when the migration runs', function () {
    $test = seedCreativityTest();
    $attempt = completedAttempt($test, fn () => 1);

    // Put the test back on its previous scoring, as it is in production before the migration.
    foreach ($test->questions as $question) {
        $question->update(['scale_override' => ['score_map' => ['1' => 2, '2' => 1, '3' => 0]] + $question->scale_override]);
    }
    $test->update(['scoring_config' => ['use_percentage' => false, 'ranges' => [['min' => -21, 'max' => 54, 'label' => ['en' => 'Old', 'ar' => 'قديم']]]]]);
    Response::where('test_attempt_id', $attempt->id)->update(['scored_value' => 2]);
    $attempt->calculateScores();
    expect((float) $attempt->fresh()->score_raw)->toBe(60.0);

    runDataMigration('2026_11_14_000001_rescore_creativity_test.php');

    $attempt->refresh();
    expect((float) $attempt->score_raw)->toBe(102.0)
        ->and($attempt->score_details['matched_range']['label']['en'])->toBe('Above Average')
        ->and(Response::where('test_attempt_id', $attempt->id)->where('scored_value', 5)->count())->toBe(18)
        ->and(Response::where('test_attempt_id', $attempt->id)->where('scored_value', 1)->count())->toBe(12);
});

it('refuses to re-score a creativity test whose questions are not the source items', function () {
    $test = seedCreativityTest();
    $test->questions()->where('sort_order', 5)->update(['text' => ['en' => 'Edited', 'ar' => 'عبارة معدلة']]);

    expect(fn () => runDataMigration('2026_11_14_000001_rescore_creativity_test.php'))
        ->toThrow(RuntimeException::class);
});

it('rates cross-cultural readiness at 50% / 75% after the migration', function () {
    User::factory()->create();
    $this->seed(StudentAbilitiesSeeder::class);
    $test = Test::where('title->ar', CrossCulturalReadinessBattery::definition()['title']['ar'])->firstOrFail();

    // Previous bands: subscales 65 / 85%, overall on the raw total 52 / 68 of 80.
    $config = $test->scoring_config;
    $config['categories'] = array_map(fn ($c) => ['interpretation' => [
        ['min' => 0, 'max' => 64.99, 'label' => ['en' => 'Low Readiness', 'ar' => 'جاهزية منخفضة']],
        ['min' => 65, 'max' => 84.99, 'label' => ['en' => 'Moderate Readiness', 'ar' => 'جاهزية متوسطة']],
        ['min' => 85, 'max' => 100, 'label' => ['en' => 'High Readiness', 'ar' => 'جاهزية عالية']],
    ]] + $c, $config['categories']);
    $config['use_percentage'] = false;
    $config['ranges'] = [
        ['min' => 0, 'max' => 51, 'label' => ['en' => 'Low Readiness', 'ar' => 'جاهزية منخفضة']],
        ['min' => 52, 'max' => 67, 'label' => ['en' => 'Moderate Readiness', 'ar' => 'جاهزية متوسطة']],
        ['min' => 68, 'max' => 80, 'label' => ['en' => 'High Readiness', 'ar' => 'جاهزية عالية']],
    ];
    $test->update(['scoring_config' => $config]);

    // "Sometimes" everywhere: 48 / 80 = 60%, every subscale 12 / 20 = 60%.
    $attempt = completedAttempt($test, fn () => 3);
    expect($attempt->score_details['matched_range']['label']['en'])->toBe('Low Readiness')
        ->and($attempt->score_details['categories'][0]['interpretation']['en'])->toBe('Low Readiness');

    runDataMigration('2026_11_14_000002_rescale_cross_cultural_readiness.php');

    $attempt->refresh();
    expect($attempt->score_details['matched_range']['label']['en'])->toBe('Moderate Readiness')
        ->and($attempt->score_details['matched_range']['description']['en'])->toStartWith('Conditional readiness')
        ->and(collect($attempt->score_details['categories'])->pluck('interpretation.en')->unique()->all())->toBe(['Moderate Readiness']);
});

it('adds the Holland scale as the last test of the student battery once', function () {
    User::factory()->create();
    $this->seed(StudentAbilitiesSeeder::class);
    $assessment = Assessment::where('title->ar', StudentAbilitiesSeeder::TITLE_AR)->firstOrFail();
    $link = $assessment->links()->firstOrFail();

    // Recreate the eight-test assessment as it is in production before the migration.
    $holland = $assessment->tests()->where('title->ar', HollandVocationalScale::TITLE_AR)->firstOrFail();
    $assessment->tests()->detach($holland->id);
    $holland->delete();
    $seeded = new ReflectionClass(require database_path('migrations/2026_11_14_000003_add_holland_scale_to_student_abilities.php'));
    $assessment->update(['instructions' => $seeded->getConstant('SEEDED_INSTRUCTIONS'), 'description' => $seeded->getConstant('SEEDED_DESCRIPTION')]);
    $link->update(['welcome_message' => $seeded->getConstant('SEEDED_WELCOME_MESSAGE')]);

    runDataMigration('2026_11_14_000003_add_holland_scale_to_student_abilities.php');
    runDataMigration('2026_11_14_000003_add_holland_scale_to_student_abilities.php');

    $tests = $assessment->fresh()->tests;
    $added = $tests->last();
    expect($tests)->toHaveCount(9)
        ->and($added->getTranslation('title', 'ar'))->toBe(HollandVocationalScale::TITLE_AR)
        ->and($added->pivot->sort_order)->toBe(9)
        ->and($added->questions()->count())->toBe(180)
        ->and($added->questions()->get()->countBy('category_key')->all())->toBe(['R' => 30, 'I' => 30, 'A' => 30, 'S' => 30, 'E' => 30, 'C' => 30])
        ->and($assessment->fresh()->instructions)->toBe(StudentAbilitiesSeeder::INSTRUCTIONS)
        ->and($link->fresh()->welcome_message)->toBe(StudentAbilitiesSeeder::WELCOME_MESSAGE);
});

it('reports the three highest Holland types, breaking ties in R I A S E C order', function () {
    $user = User::factory()->create();
    $test = HollandVocationalScale::create($user->id);

    // R and I at 150, A, S and C tied at 90, E at 30.
    $answers = ['R' => 1, 'I' => 1, 'A' => 2, 'S' => 2, 'E' => 3, 'C' => 2];
    $attempt = completedAttempt($test, fn ($q) => $answers[$q->category_key]);

    $top = $attempt->score_details['top_categories'];
    expect(array_column($top, 'key'))->toBe(['R', 'I', 'A'])
        ->and($top[0]['score_raw'])->toBe(150)
        ->and($top[0]['score_max'])->toBe(150)
        ->and($top[2]['description']['ar'])->toStartWith('يرغب أصحاب هذه الشخصية بالتأليف')
        ->and($attempt->topCategoryCode())->toBe('RIA');
});

it('shows the Holland top types in the psycho-profile PDF and the Excel summary', function () {
    $user = User::factory()->create(['role' => 'super_admin']);
    $test = HollandVocationalScale::create($user->id);
    $attempt = completedAttempt($test, fn ($q) => ['R' => 3, 'I' => 1, 'A' => 1, 'S' => 1, 'E' => 3, 'C' => 3][$q->category_key]);
    $attempt->load('test');

    $html = view('reports.participant-combined', [
        'accountName' => 'P', 'accountEmail' => null, 'accountPhone' => null, 'accountCompany' => null,
        'accountJobTitle' => null, 'accountAge' => null, 'accountGender' => null,
        'assessments' => collect([['assessment_title' => 'A', 'study_preferences' => null, 'attempts' => collect([$attempt])]]),
        'includeResponses' => false, 'dir' => 'ltr',
    ])->render();

    expect($html)->toContain('Your top types')
        ->toContain('1st type: Investigative (I)')
        ->toContain('3rd type: Social (S)')
        ->not->toContain('Category Breakdown');

    $this->actingAs($user, 'sanctum')
        ->get("/api/admin/assessments/{$attempt->assessment->uuid}/export/summary-excel")
        ->assertOk();
});
