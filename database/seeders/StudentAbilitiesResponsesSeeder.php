<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Participant;
use App\Models\ParticipantAccount;
use App\Models\Question;
use App\Models\Response;
use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Database\Seeder;

/**
 * LOCAL REVIEW ONLY — do not run in production.
 *
 * Simulates students completing "قدرات و اتجاهات الطلاب" so the scoring, interpretation bands,
 * radar/column charts and participant profiles can be reviewed. Scores are computed through the
 * real ScoringEngine via TestAttempt::calculateScores().
 *
 * Profiles cover realistic students (engineering-, medicine- and humanities-leaning, average,
 * struggling) plus edge cases:
 *   - Perfect and all-wrong answer sheets (100% / 0%, every band boundary at the extremes).
 *   - An "always A" guesser, which should land near 25% because correct answers are spread over A-D.
 *   - Exact boundary totals: 15/30 correct (= 50%, first Moderate value), 22/30 (73.3%, still
 *     Moderate), 23/30 (76.7%, first High), and readiness totals of 51 / 52 / 67 / 68
 *     (the source's Low / Moderate / High cut points).
 *
 * Idempotent: re-running deletes this seeder's own participants first.
 */
class StudentAbilitiesResponsesSeeder extends Seeder
{
    private const EMAIL_DOMAIN = '@students.demo';

    /** Test keys in assessment order (matches StudentAbilitiesSeeder). */
    private const TEST_KEYS = ['cognitive', 'dat', 'math', 'natural', 'social', 'arabic', 'english', 'cultural'];

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->error('StudentAbilitiesResponsesSeeder creates demo data and must not run in production.');

            return;
        }

        $assessment = Assessment::with(['tests.questions'])->where('title->ar', StudentAbilitiesSeeder::TITLE_AR)->first();

        if (! $assessment) {
            $this->command->error('Assessment "' . StudentAbilitiesSeeder::TITLE_AR . '" not found. Run StudentAbilitiesSeeder first.');

            return;
        }

        $link = $assessment->links()->first();

        if (! $link) {
            $this->command->error('No link found for the assessment.');

            return;
        }

        $existing = Participant::where('assessment_link_id', $link->id)
            ->where('email', 'like', '%' . self::EMAIL_DOMAIN)
            ->get();

        if ($existing->isNotEmpty()) {
            $this->command->info("Removing {$existing->count()} demo students from a previous run...");
            foreach ($existing as $p) {
                $p->delete(); // cascades: test_attempts -> responses
            }
        }

        mt_srand(20261030);

        $tests = $assessment->tests->sortBy('pivot.sort_order')->values();

        foreach ($this->students() as $i => $student) {
            $email = strtolower(str_replace([' ', '-', "'"], ['.', '', ''], $student['name'])) . self::EMAIL_DOMAIN;

            $account = ParticipantAccount::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $student['name'],
                    'password' => bcrypt('password123'),
                    'age' => $student['age'],
                    'gender' => $student['gender'],
                    'preferred_locale' => $student['locale'],
                ],
            );

            $participant = Participant::create([
                'assessment_link_id' => $link->id,
                'participant_account_id' => $account->id,
                'name' => $student['name'],
                'email' => $email,
                'age' => $student['age'],
                'gender' => $student['gender'],
                'locale' => $student['locale'],
            ]);

            $clock = now()->subDays(8 - $i)->setTime(9, 0)->addMinutes($i * 7);

            foreach ($tests as $ti => $test) {
                $testKey = self::TEST_KEYS[$ti];
                $questions = $test->questions->sortBy('sort_order')->values();

                $startedAt = (clone $clock);
                $duration = $testKey === 'cultural' ? rand(4, 8) : rand(14, 32);
                $completedAt = (clone $startedAt)->addMinutes($duration)->addSeconds(rand(0, 59));
                $clock = (clone $completedAt)->addMinutes(rand(2, 10));

                $attempt = TestAttempt::create([
                    'participant_id' => $participant->id,
                    'test_id' => $test->id,
                    'assessment_id' => $assessment->id,
                    'assessment_link_id' => $link->id,
                    'status' => 'completed',
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                ]);

                $values = $testKey === 'cultural'
                    ? $this->likertAnswers($questions, $student)
                    : $this->mcqAnswers($questions, $testKey, $student);

                $step = max(1, intdiv($duration * 60, max(1, $questions->count())));
                foreach ($questions as $qi => $q) {
                    Response::create([
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $q->id,
                        'value' => $values[$qi],
                        'answered_at' => (clone $startedAt)->addSeconds(($qi + 1) * $step),
                    ]);
                }

                $attempt->calculateScores();
            }

            $this->report($participant, $tests, $student['note']);
        }

        $this->command->info('');
        $this->command->info('=== Demo students seeded ===');
        $this->command->info(count($this->students()) . ' students completed all 8 tests on link #' . $link->id . ' (password: password123).');
        $this->command->info('Assessment results:  /admin/assessments/' . $assessment->uuid . '/results');
    }

    // ------------------------------------------------------------------
    // Answer generators
    // ------------------------------------------------------------------

    /**
     * @param  \Illuminate\Support\Collection<int, Question>  $questions
     * @return list<int>
     */
    private function mcqAnswers($questions, string $testKey, array $student): array
    {
        $strategy = $student['strategy'] ?? 'ability';

        if ($strategy === 'always_a') {
            return $questions->map(fn () => 1)->all();
        }

        // Exactly $n correct answers, spread across the test.
        if (isset($student['exact'][$testKey])) {
            $n = $student['exact'][$testKey];
            $count = $questions->count();
            $correctIdx = [];
            for ($k = 0; $k < $n; $k++) {
                $correctIdx[intdiv($k * $count, $n)] = true;
            }

            return $questions->map(fn (Question $q, $qi) => isset($correctIdx[$qi]) ? $q->correct_answer : $this->wrong($q))->all();
        }

        return $questions->map(function (Question $q) use ($testKey, $student, $strategy) {
            $p = match ($strategy) {
                'perfect' => 1.0,
                'all_wrong' => 0.0,
                default => $student['ability']["{$testKey}.{$q->category_key}"]
                    ?? $student['ability'][$testKey]
                    ?? $student['ability']['default'],
            };

            return (mt_rand(0, 999) / 1000) < $p ? $q->correct_answer : $this->wrong($q);
        })->all();
    }

    private function wrong(Question $q): int
    {
        $options = array_values(array_diff([1, 2, 3, 4], [$q->correct_answer]));

        return $options[array_rand($options)];
    }

    /**
     * Either an exact raw total (16-80, spread evenly over the items) or per-subscale tendencies.
     *
     * @param  \Illuminate\Support\Collection<int, Question>  $questions
     * @return list<int>
     */
    private function likertAnswers($questions, array $student): array
    {
        $count = $questions->count();

        if (isset($student['readiness_total'])) {
            $base = intdiv($student['readiness_total'], $count);
            $extra = $student['readiness_total'] % $count;

            return $questions->map(fn ($q, $qi) => min(5, $base + ($qi < $extra ? 1 : 0)))->all();
        }

        return $questions->map(function (Question $q) use ($student) {
            $tendency = $student['readiness'][$q->category_key] ?? $student['readiness']['default'] ?? 0.6;
            $value = (int) round(1 + $tendency * 4 + (mt_rand(-100, 100) / 100) * 0.8);

            return max(1, min(5, $value));
        })->all();
    }

    // ------------------------------------------------------------------
    // Students
    // ------------------------------------------------------------------

    private function students(): array
    {
        return [
            [
                'name' => 'Omar Khaled', 'age' => 18, 'gender' => 'male', 'locale' => 'ar',
                'note' => 'engineering profile (SR/MR/NA high)',
                'ability' => [
                    'default' => 0.65,
                    'dat.sr' => 0.95, 'dat.mr' => 0.95, 'dat.na' => 0.9, 'dat.vr' => 0.55, 'dat.lu' => 0.45, 'dat.sp' => 0.45,
                    'cognitive.spatial' => 0.9, 'cognitive.numerical' => 0.9,
                    'math' => 0.9, 'natural.physics' => 0.9, 'natural.chemistry' => 0.75, 'natural.biology' => 0.5,
                    'social' => 0.45, 'arabic' => 0.55, 'english' => 0.7,
                ],
                'readiness' => ['default' => 0.8, 'autonomy' => 0.9, 'cultural_intelligence' => 0.7],
            ],
            [
                'name' => 'Laila Hassan', 'age' => 17, 'gender' => 'female', 'locale' => 'en',
                'note' => 'medicine profile (VR/NA/AR high)',
                'ability' => [
                    'default' => 0.75,
                    'dat.vr' => 0.95, 'dat.na' => 0.9, 'dat.ar' => 0.95, 'dat.mr' => 0.5, 'dat.sr' => 0.6,
                    'natural.biology' => 0.95, 'natural.chemistry' => 0.9, 'natural.physics' => 0.7,
                    'math' => 0.8, 'english' => 0.85, 'arabic' => 0.7, 'social.psychology' => 0.85,
                ],
                'readiness' => ['default' => 0.75, 'resilience' => 0.55, 'values_identity' => 0.9],
            ],
            [
                'name' => 'Youssef Adel', 'age' => 18, 'gender' => 'male', 'locale' => 'ar',
                'note' => 'law/media profile (VR/LU/SP high)',
                'ability' => [
                    'default' => 0.55,
                    'dat.vr' => 0.95, 'dat.lu' => 0.95, 'dat.sp' => 0.95, 'dat.mr' => 0.3, 'dat.sr' => 0.35, 'dat.na' => 0.45,
                    'cognitive.verbal_logic' => 0.9, 'cognitive.spatial' => 0.35,
                    'arabic' => 0.9, 'social' => 0.9, 'english' => 0.75, 'math' => 0.35, 'natural' => 0.4,
                ],
                'readiness' => ['default' => 0.55, 'cultural_intelligence' => 0.85, 'autonomy' => 0.35],
            ],
            [
                'name' => 'Nour El-Din', 'age' => 17, 'gender' => 'female', 'locale' => 'en',
                'note' => 'average student, boundary totals: math 15/30 (50% → Moderate), social 22/30 (73.3% → Moderate), english 23/30 (76.7% → High), readiness total 52 (→ Moderate)',
                'ability' => ['default' => 0.6],
                'exact' => ['math' => 15, 'social' => 22, 'english' => 23],
                'readiness_total' => 52,
            ],
            [
                'name' => 'Karim Mostafa', 'age' => 19, 'gender' => 'male', 'locale' => 'ar',
                'note' => 'struggling student, math 14/30 (46.7% → Low), readiness total 51 (→ Low)',
                'ability' => ['default' => 0.35, 'arabic' => 0.5, 'social' => 0.45],
                'exact' => ['math' => 14],
                'readiness_total' => 51,
            ],
            [
                'name' => 'Mariam Saeed', 'age' => 18, 'gender' => 'female', 'locale' => 'ar',
                'note' => 'edge case: perfect answer sheet, readiness total 80',
                'strategy' => 'perfect',
                'readiness_total' => 80,
            ],
            [
                'name' => 'Hadi Farouk', 'age' => 17, 'gender' => 'male', 'locale' => 'en',
                'note' => 'edge case: every answer wrong, readiness total 16',
                'strategy' => 'all_wrong',
                'readiness_total' => 16,
            ],
            [
                'name' => 'Salma Tarek', 'age' => 18, 'gender' => 'female', 'locale' => 'en',
                'note' => 'edge case: always picks option A (expect ≈27%), readiness total 67 (→ Moderate)',
                'strategy' => 'always_a',
                'readiness_total' => 67,
            ],
            [
                'name' => 'Ahmed Samir', 'age' => 18, 'gender' => 'male', 'locale' => 'ar',
                'note' => 'strong all-rounder, readiness total 68 (→ High)',
                'ability' => ['default' => 0.85, 'dat.psa' => 1.0],
                'readiness_total' => 68,
            ],
        ];
    }

    private function report(Participant $participant, $tests, string $note): void
    {
        $parts = [];
        foreach ($tests as $ti => $test) {
            $attempt = $participant->attempts()->where('test_id', $test->id)->first();
            $label = $attempt->score_details['matched_range']['label']['en'] ?? '-';
            $parts[] = sprintf('%s %s/%s %s', self::TEST_KEYS[$ti], $attempt->score_raw + 0, $attempt->score_max + 0, $label);
        }

        $this->command->info("  ✓ {$participant->name} — {$note}");
        $this->command->line('      ' . implode(' · ', $parts));
    }
}
