<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\ParticipantAccount;
use App\Models\Response;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clear all participant/assessment data
        $this->command->info('Clearing existing data...');

        $tables = ['responses', 'test_attempts', 'retake_grants', 'participants', 'participant_accounts', 'assessment_test', 'assessment_links', 'assessments'];

        // Also truncate activity_logs if the table exists
        $hasActivityLogs = DB::select("SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'activity_logs') as exists");
        if ($hasActivityLogs[0]->exists) {
            $tables[] = 'activity_logs';
        }

        DB::statement('TRUNCATE TABLE ' . implode(', ', $tables) . ' RESTART IDENTITY CASCADE');

        $this->command->info('Data cleared.');

        // 2. Get all published tests
        $admin = User::first();
        $tests = Test::published()->orderBy('id')->get();

        if ($tests->isEmpty()) {
            $this->command->error('No published tests found! Run test seeders first.');
            return;
        }

        $this->command->info("Found {$tests->count()} published tests.");

        // 3. Create assessment with all published tests
        $assessment = Assessment::create([
            'user_id' => $admin->id,
            'title' => [
                'en' => 'Comprehensive Psychometric Assessment',
                'ar' => 'التقييم النفسي الشامل',
            ],
            'description' => [
                'en' => 'A comprehensive assessment battery covering personality, leadership, emotional intelligence, creativity, motivation, coping styles, and professional competencies.',
                'ar' => 'بطارية تقييم شاملة تغطي الشخصية والقيادة والذكاء الوجداني والإبداع والدافعية وأساليب المواجهة والجدارات المهنية.',
            ],
            'status' => 'published',
            'show_results_to_participant' => true,
        ]);

        foreach ($tests as $index => $test) {
            $assessment->tests()->attach($test->id, ['sort_order' => $index + 1]);
        }

        $this->command->info("Created assessment with {$tests->count()} tests.");

        // 4. Create assessment link
        $link = AssessmentLink::create([
            'assessment_id' => $assessment->id,
            'created_by' => $admin->id,
            'title' => 'Demo Assessment Link',
            'is_active' => true,
            'collect_name' => true,
            'collect_email' => true,
            'collect_company' => true,
            'collect_job_title' => true,
            'collect_age' => true,
            'collect_gender' => true,
        ]);

        // 5. Create 2 participant accounts
        $accounts = [
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@demo.com',
                'company' => 'Edrak Group',
                'job_title' => 'HR Manager',
                'age' => 32,
                'gender' => 'male',
                'profile' => 'high_performer', // Will score generally high
            ],
            [
                'name' => 'Sara Mohamed',
                'email' => 'sara.mohamed@demo.com',
                'company' => 'Edrak Media',
                'job_title' => 'Marketing Specialist',
                'age' => 28,
                'gender' => 'female',
                'profile' => 'balanced', // Will score moderately with variation
            ],
        ];

        foreach ($accounts as $accountData) {
            $profile = $accountData['profile'];
            unset($accountData['profile']);

            $account = ParticipantAccount::create([
                'name' => $accountData['name'],
                'email' => $accountData['email'],
                'password' => bcrypt('password123'),
                'phone' => null,
                'company' => $accountData['company'],
                'job_title' => $accountData['job_title'],
                'age' => $accountData['age'],
                'gender' => $accountData['gender'],
            ]);

            $participant = Participant::create([
                'assessment_link_id' => $link->id,
                'participant_account_id' => $account->id,
                'name' => $accountData['name'],
                'email' => $accountData['email'],
                'company' => $accountData['company'],
                'job_title' => $accountData['job_title'],
                'age' => $accountData['age'],
                'gender' => $accountData['gender'],
            ]);

            $this->command->info("Simulating tests for {$accountData['name']}...");

            foreach ($tests as $test) {
                $startedAt = now()->subMinutes(rand(20, 60));
                $completedAt = $startedAt->copy()->addMinutes(rand(5, 25));

                $attempt = TestAttempt::create([
                    'participant_id' => $participant->id,
                    'test_id' => $test->id,
                    'assessment_id' => $assessment->id,
                    'assessment_link_id' => $link->id,
                    'status' => 'completed',
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                ]);

                $questions = $test->questions()->orderBy('sort_order')->get();
                $scaleMin = $test->scale_config['min'] ?? 1;
                $scaleMax = $test->scale_config['max'] ?? 5;

                foreach ($questions as $question) {
                    $value = $this->generateResponse($scaleMin, $scaleMax, $profile, $question);

                    Response::create([
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'value' => $value,
                        'answered_at' => $startedAt->copy()->addSeconds(rand(5, 30)),
                    ]);
                }

                $attempt->calculateScores();
            }

            $this->command->info("  ✓ Completed all {$tests->count()} tests for {$accountData['name']}");
        }

        $this->command->info('');
        $this->command->info('=== Demo Assessment Setup Complete ===');
        $this->command->info("Assessment Link: {$link->getUrl()}");
        $this->command->info("Participants: Ahmed Hassan (ahmed.hassan@demo.com) & Sara Mohamed (sara.mohamed@demo.com)");
        $this->command->info("Portal password: password123");
    }

    private function generateResponse(int $min, int $max, string $profile, $question): int
    {
        $range = $max - $min;
        $isReverse = $question->is_reverse_scored ?? false;

        // Check if question has score_map (like the creativity test)
        $scaleOverride = $question->scale_override;
        if (!empty($scaleOverride['score_map'])) {
            // For score_map questions, just pick a random valid option
            $keys = array_keys($scaleOverride['score_map']);
            return (int) $keys[array_rand($keys)];
        }

        // Generate base tendency
        if ($profile === 'high_performer') {
            // Generally high scores (65-90% of range)
            $base = $min + (int) round($range * (0.65 + (mt_rand(0, 25) / 100)));
        } else {
            // Balanced/moderate scores (35-70% of range) with more variation
            $base = $min + (int) round($range * (0.35 + (mt_rand(0, 35) / 100)));
        }

        // For reverse-scored items, the "positive" answer is low on the scale
        if ($isReverse) {
            $base = ($max + $min) - $base;
        }

        // Add some noise
        $noise = mt_rand(-1, 1);
        $value = $base + $noise;

        return max($min, min($max, $value));
    }
}
