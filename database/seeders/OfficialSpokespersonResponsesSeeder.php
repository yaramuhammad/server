<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Participant;
use App\Models\ParticipantAccount;
use App\Models\Response;
use App\Models\TestAttempt;
use Illuminate\Database\Seeder;

/**
 * Simulates a set of participants completing the "مقاييس المتحدث الرسمي" assessment
 * so the admin reports / analytics have data to show.
 *
 * Each participant gets a per-dimension "tendency" (0..1) that biases their Likert
 * answers, so the column charts show a distinct profile shape per person rather
 * than flat noise. Scores are computed through the real ScoringEngine via
 * TestAttempt::calculateScores().
 *
 * Idempotent: re-running deletes this seeder's own participants first.
 */
class OfficialSpokespersonResponsesSeeder extends Seeder
{
    public function run(): void
    {
        $assessment = Assessment::with('tests.questions')->where('title->en', 'Official Spokesperson Scales')->first();

        if (! $assessment) {
            $this->command->error('Assessment "Official Spokesperson Scales" not found. Run OfficialSpokespersonScalesSeeder first.');

            return;
        }

        $link = $assessment->links()->first();

        if (! $link) {
            $this->command->error('No link found for the assessment.');

            return;
        }

        // Marker so we can clean up on re-run without touching other data.
        $emailDomain = '@spokesperson.demo';

        $existing = Participant::where('assessment_link_id', $link->id)
            ->where('email', 'like', "%{$emailDomain}")
            ->get();

        if ($existing->isNotEmpty()) {
            $this->command->info("Removing {$existing->count()} existing demo participants from a previous run...");
            foreach ($existing as $p) {
                $p->delete(); // cascades: test_attempts -> responses
            }
        }

        // People + a coarse strength per competency area.
        // Keys map loosely onto the seven tests; values 0..1 shift the answer mean.
        $people = [
            [
                'name' => 'Hana Al-Sayed', 'company' => 'Ministry of Communications', 'job_title' => 'Chief Spokesperson',
                'age' => 44, 'gender' => 'female', 'locale' => 'ar',
                'profile' => ['credibility' => 0.92, 'self_efficacy' => 0.88, 'skills' => 0.90, 'media' => 0.86, 'audience' => 0.83, 'nonverbal' => 0.80, 'flexibility' => 0.85],
            ],
            [
                'name' => 'Tariq Nabil', 'company' => 'Gulf Energy Corp', 'job_title' => 'PR Manager',
                'age' => 37, 'gender' => 'male', 'locale' => 'en',
                'profile' => ['credibility' => 0.70, 'self_efficacy' => 0.55, 'skills' => 0.62, 'media' => 0.48, 'audience' => 0.66, 'nonverbal' => 0.58, 'flexibility' => 0.60],
            ],
            [
                'name' => 'Reem Odeh', 'company' => 'National Health Authority', 'job_title' => 'Media Officer',
                'age' => 29, 'gender' => 'female', 'locale' => 'ar',
                'profile' => ['credibility' => 0.58, 'self_efficacy' => 0.40, 'skills' => 0.45, 'media' => 0.38, 'audience' => 0.52, 'nonverbal' => 0.44, 'flexibility' => 0.42],
            ],
            [
                'name' => 'Bilal Haddad', 'company' => 'City Transit Agency', 'job_title' => 'Communications Lead',
                'age' => 41, 'gender' => 'male', 'locale' => 'en',
                'profile' => ['credibility' => 0.78, 'self_efficacy' => 0.72, 'skills' => 0.55, 'media' => 0.80, 'audience' => 0.50, 'nonverbal' => 0.68, 'flexibility' => 0.74],
            ],
            [
                'name' => 'Salma Kittani', 'company' => 'EdTech Ventures', 'job_title' => 'Head of Brand',
                'age' => 33, 'gender' => 'female', 'locale' => 'ar',
                'profile' => ['credibility' => 0.64, 'self_efficacy' => 0.85, 'skills' => 0.80, 'media' => 0.55, 'audience' => 0.88, 'nonverbal' => 0.76, 'flexibility' => 0.82],
            ],
            [
                'name' => 'Fadi Mansour', 'company' => 'Central Bank', 'job_title' => 'Deputy Spokesperson',
                'age' => 49, 'gender' => 'male', 'locale' => 'en',
                'profile' => ['credibility' => 0.50, 'self_efficacy' => 0.35, 'skills' => 0.40, 'media' => 0.30, 'audience' => 0.38, 'nonverbal' => 0.33, 'flexibility' => 0.36],
            ],
        ];

        // Which profile key drives each test, by its English title.
        $testKey = [
            'Official Spokesperson Credibility Scale' => 'credibility',
            'Public Speaking Self-Efficacy Scale' => 'self_efficacy',
            'Public Speaking Skills Scale' => 'skills',
            'Media Handling Competence Scale' => 'media',
            'Audience Orientation Scale' => 'audience',
            'Nonverbal Communication Competence Scale' => 'nonverbal',
            'Communication Flexibility Scale' => 'flexibility',
        ];

        $tests = $assessment->tests;

        foreach ($people as $i => $person) {
            $slug = strtolower(str_replace([' ', '-', "'"], ['.', '', ''], $person['name']));
            $email = $slug . '@spokesperson.demo';

            $account = ParticipantAccount::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $person['name'],
                    'password' => bcrypt('password123'),
                    'company' => $person['company'],
                    'job_title' => $person['job_title'],
                    'age' => $person['age'],
                    'gender' => $person['gender'],
                    'preferred_locale' => $person['locale'],
                ],
            );

            $participant = Participant::create([
                'assessment_link_id' => $link->id,
                'participant_account_id' => $account->id,
                'name' => $person['name'],
                'email' => $email,
                'company' => $person['company'],
                'job_title' => $person['job_title'],
                'age' => $person['age'],
                'gender' => $person['gender'],
                'locale' => $person['locale'],
            ]);

            $startedBase = now()->subDays(6 - $i)->setTime(9 + $i, 15);

            foreach ($tests as $t) {
                $key = $testKey[$t->getTranslation('title', 'en')] ?? 'credibility';
                $baseTendency = $person['profile'][$key] ?? 0.6;

                $startedAt = (clone $startedBase)->addMinutes($i * 3);
                $completedAt = (clone $startedAt)->addMinutes(rand(4, 9))->addSeconds(rand(0, 59));

                $attempt = TestAttempt::create([
                    'participant_id' => $participant->id,
                    'test_id' => $t->id,
                    'assessment_id' => $assessment->id,
                    'assessment_link_id' => $link->id,
                    'status' => 'completed',
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                ]);

                // Give each of the 3 dimensions its own offset around the base
                // tendency so the column chart shows a real profile shape.
                $catKeys = collect($t->scoring_config['categories'])->pluck('key')->all();
                $catShift = [];
                foreach ($catKeys as $ci => $ck) {
                    $catShift[$ck] = [-0.15, 0.0, 0.15][$ci % 3] + (mt_rand(-6, 6) / 100);
                }

                foreach ($t->questions as $q) {
                    $tendency = max(0.05, min(0.95, $baseTendency + ($catShift[$q->category_key] ?? 0)));
                    $value = $this->likert($tendency);

                    Response::create([
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $q->id,
                        'value' => $value,
                        'answered_at' => (clone $startedAt)->addSeconds(rand(10, 240)),
                    ]);
                }

                $attempt->calculateScores();
            }

            $overall = $participant->attempts()->completed()->avg('score_percentage');
            $this->command->info(sprintf('  ✓ %-16s — 7/7 tests completed, avg %.1f%%', $person['name'], $overall));
        }

        $this->command->info('');
        $this->command->info('=== Demo responses seeded ===');
        $this->command->info(count($people) . ' participants completed all 7 tests on link #' . $link->id . '.');
        $this->command->info('Assessment results:  /admin/assessments/' . $assessment->uuid . '/results');
        $this->command->info('Analytics:           /admin/assessments/' . $assessment->uuid . '/analytics');
    }

    /**
     * Draw a 1-5 Likert answer whose distribution is centred by $tendency (0..1).
     * tendency 0.5 → roughly centred on 3; 0.9 → mostly 4-5; 0.2 → mostly 1-3.
     */
    private function likert(float $tendency): int
    {
        $mean = 1 + $tendency * 4;                 // 1..5
        $noise = (mt_rand(-100, 100) / 100) * 0.9; // ±0.9
        $val = (int) round($mean + $noise);

        return max(1, min(5, $val));
    }
}
