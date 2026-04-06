<?php

namespace Database\Seeders;

use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Response;
use App\Models\TestAttempt;
use Illuminate\Database\Seeder;

class ParticipantAndResponseSeeder extends Seeder
{
    public function run(): void
    {
        $hrLink = AssessmentLink::where('title', 'HR Department - Q1 2026')->first();
        $researchLink = AssessmentLink::where('title', 'Research Study - Group A')->first();
        $expiredLink = AssessmentLink::where('title', 'Engineering Team - Dec 2025')->first();

        $this->seedHrParticipants($hrLink);
        $this->seedResearchParticipants($researchLink);
        $this->seedExpiredLinkParticipants($expiredLink);
    }

    private function seedHrParticipants(AssessmentLink $link): void
    {
        $assessment = $link->assessment;
        $tests = $assessment->tests()->orderByPivot('sort_order')->get();

        $participants = [
            ['name' => 'Ahmed Al-Rashid', 'email' => 'ahmed.r@company.com', 'department' => 'Engineering', 'age' => 32, 'gender' => 'male', 'burnout' => 'high', 'stress' => 'high'],
            ['name' => 'Fatima Hassan', 'email' => 'fatima.h@company.com', 'department' => 'Engineering', 'age' => 28, 'gender' => 'female', 'burnout' => 'high', 'stress' => 'medium'],
            ['name' => 'Mohammed Khalil', 'email' => 'mohammed.k@company.com', 'department' => 'Marketing', 'age' => 35, 'gender' => 'male', 'burnout' => 'medium', 'stress' => 'medium'],
            ['name' => 'Layla Ibrahim', 'email' => 'layla.i@company.com', 'department' => 'HR', 'age' => 41, 'gender' => 'female', 'burnout' => 'low', 'stress' => 'low'],
            ['name' => 'Yusuf Nasser', 'email' => 'yusuf.n@company.com', 'department' => 'Finance', 'age' => 29, 'gender' => 'male', 'burnout' => 'medium', 'stress' => 'high'],
            ['name' => 'Noor Al-Sayed', 'email' => 'noor.s@company.com', 'department' => 'Operations', 'age' => 37, 'gender' => 'female', 'burnout' => 'low', 'stress' => 'low'],
            ['name' => 'Khalid Omar', 'email' => 'khalid.o@company.com', 'department' => 'Engineering', 'age' => 44, 'gender' => 'male', 'burnout' => 'high', 'stress' => 'medium'],
            ['name' => 'Sara Mahmoud', 'email' => 'sara.m@company.com', 'department' => 'Marketing', 'age' => 26, 'gender' => 'female', 'burnout' => 'medium', 'stress' => 'low'],
            ['name' => 'Tariq Zayed', 'email' => 'tariq.z@company.com', 'department' => 'Finance', 'age' => 33, 'gender' => 'male', 'burnout' => 'medium', 'stress' => 'medium'],
            ['name' => 'Hana Al-Farsi', 'email' => 'hana.f@company.com', 'department' => 'HR', 'age' => 30, 'gender' => 'female', 'burnout' => 'low', 'stress' => 'low'],
        ];

        foreach ($participants as $i => $pData) {
            $participant = Participant::create([
                'assessment_link_id' => $link->id,
                'name' => $pData['name'],
                'email' => $pData['email'],
                'department' => $pData['department'],
                'age' => $pData['age'],
                'gender' => $pData['gender'],
                'ip_address' => '192.168.1.' . ($i + 10),
                'locale' => $i % 3 === 0 ? 'ar' : 'en',
            ]);

            // Each participant takes ALL tests in the assessment
            foreach ($tests as $test) {
                $questions = Question::where('test_id', $test->id)->orderBy('sort_order')->get();
                $scaleConfig = $test->scale_config;

                $startedAt = now()->subDays(rand(1, 8))->subHours(rand(1, 12));
                $completedAt = $startedAt->copy()->addMinutes(rand(10, 25));

                // Determine level based on test type
                $isBurnout = str_contains($test->getTranslation('title', 'en'), 'Burnout');
                $level = $isBurnout ? $pData['burnout'] : $pData['stress'];

                $attempt = TestAttempt::create([
                    'participant_id' => $participant->id,
                    'test_id' => $test->id,
                    'assessment_id' => $assessment->id,
                    'assessment_link_id' => $link->id,
                    'status' => 'completed',
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                ]);

                foreach ($questions as $question) {
                    $value = $this->generateResponse($question, $level, $scaleConfig);

                    Response::create([
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'value' => $value,
                        'answered_at' => $startedAt->copy()->addMinutes(rand(1, 20)),
                    ]);
                }

                $attempt->calculateScores();
            }
        }
    }

    private function seedResearchParticipants(AssessmentLink $link): void
    {
        $assessment = $link->assessment;
        $tests = $assessment->tests()->orderByPivot('sort_order')->get();

        for ($i = 0; $i < 8; $i++) {
            $participant = Participant::create([
                'assessment_link_id' => $link->id,
                'age' => rand(22, 55),
                'gender' => ['male', 'female', 'male', 'female', 'male', 'female', 'other', 'prefer_not_to_say'][$i],
                'custom_data' => ['education_level' => ['high_school', 'bachelors', 'masters', 'doctorate', 'bachelors', 'masters', 'bachelors', 'doctorate'][$i]],
                'ip_address' => '10.0.0.' . ($i + 100),
                'locale' => $i % 2 === 0 ? 'ar' : 'en',
            ]);

            $isFullyCompleted = $i < 6; // 6 fully completed, 2 partially
            $stressLevel = ['low', 'medium', 'high', 'low', 'medium', 'high', 'low', 'medium'][$i];

            foreach ($tests as $testIndex => $test) {
                $questions = Question::where('test_id', $test->id)->orderBy('sort_order')->get();
                $scaleConfig = $test->scale_config;

                $startedAt = now()->subDays(rand(1, 4))->subHours(rand(0, 8));

                // For partially completed participants: complete first test, leave second in_progress
                $isThisTestCompleted = $isFullyCompleted || $testIndex === 0;

                $attempt = TestAttempt::create([
                    'participant_id' => $participant->id,
                    'test_id' => $test->id,
                    'assessment_id' => $assessment->id,
                    'assessment_link_id' => $link->id,
                    'status' => $isThisTestCompleted ? 'completed' : 'in_progress',
                    'started_at' => $startedAt,
                    'completed_at' => $isThisTestCompleted ? $startedAt->copy()->addMinutes(rand(5, 15)) : null,
                ]);

                $questionsToAnswer = $isThisTestCompleted ? $questions : $questions->take(rand(3, 7));

                foreach ($questionsToAnswer as $question) {
                    $value = $this->generateResponse($question, $stressLevel, $scaleConfig);

                    Response::create([
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'value' => $value,
                        'answered_at' => $startedAt->copy()->addMinutes(rand(1, 10)),
                    ]);
                }

                if ($isThisTestCompleted) {
                    $attempt->calculateScores();
                }
            }
        }
    }

    private function seedExpiredLinkParticipants(AssessmentLink $link): void
    {
        $assessment = $link->assessment;
        $tests = $assessment->tests()->orderByPivot('sort_order')->get();

        $expiredParticipants = [
            ['name' => 'Ali Kareem', 'email' => 'ali.k@company.com', 'level' => 'low'],
            ['name' => 'Rania Saeed', 'email' => 'rania.s@company.com', 'level' => 'medium'],
            ['name' => 'Hassan Jaber', 'email' => 'hassan.j@company.com', 'level' => 'high'],
        ];

        foreach ($expiredParticipants as $i => $pData) {
            $participant = Participant::create([
                'assessment_link_id' => $link->id,
                'name' => $pData['name'],
                'email' => $pData['email'],
                'ip_address' => '172.16.0.' . ($i + 1),
                'locale' => 'ar',
            ]);

            foreach ($tests as $test) {
                $questions = Question::where('test_id', $test->id)->orderBy('sort_order')->get();
                $scaleConfig = $test->scale_config;

                $startedAt = now()->subMonths(2)->subDays(rand(0, 15));
                $completedAt = $startedAt->copy()->addMinutes(rand(12, 28));

                $attempt = TestAttempt::create([
                    'participant_id' => $participant->id,
                    'test_id' => $test->id,
                    'assessment_id' => $assessment->id,
                    'assessment_link_id' => $link->id,
                    'status' => 'completed',
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                ]);

                foreach ($questions as $question) {
                    $value = $this->generateResponse($question, $pData['level'], $scaleConfig);

                    Response::create([
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'value' => $value,
                        'answered_at' => $startedAt->copy()->addMinutes(rand(1, 20)),
                    ]);
                }

                $attempt->calculateScores();
            }
        }
    }

    private function generateResponse(Question $question, string $level, array $scaleConfig): int
    {
        $min = $scaleConfig['min'];
        $max = $scaleConfig['max'];
        $range = $max - $min;

        // For reverse-scored items, invert the level
        $effectiveLevel = $question->is_reverse_scored
            ? match ($level) { 'high' => 'low', 'low' => 'high', default => 'medium' }
            : $level;

        $base = match ($effectiveLevel) {
            'high' => $min + (int) ($range * 0.6),
            'medium' => $min + (int) ($range * 0.35),
            'low' => $min + (int) ($range * 0.15),
        };

        $noise = rand(-1, 1);
        return max($min, min($max, $base + $noise));
    }
}
