<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class RavenAPMTestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $interp = [
            ['min' => 0, 'max' => 49, 'label' => ['en' => 'Low', 'ar' => 'منخفض']],
            ['min' => 50, 'max' => 74, 'label' => ['en' => 'Moderate', 'ar' => 'متوسط']],
            ['min' => 75, 'max' => 100, 'label' => ['en' => 'High', 'ar' => 'مرتفع']],
        ];

        $test = Test::create([
            'user_id' => $admin->id,
            'title' => [
                'en' => 'Raven\'s Advanced Progressive Matrices (APM)',
                'ar' => 'اختبار المصفوفات المتقدم',
            ],
            'description' => [
                'en' => 'A non-verbal intelligence test measuring abstract reasoning and problem-solving ability through visual pattern recognition.',
                'ar' => 'اختبار ذكاء غير لفظي يقيس التفكير المجرد وقدرة حل المشكلات من خلال التعرف على الأنماط البصرية.',
            ],
            'instructions' => [
                'en' => 'Each question shows a pattern with a missing piece. Look at the pattern carefully and choose the correct answer (1-8) that completes it.',
                'ar' => 'كل سؤال يعرض نمطًا به جزء ناقص. انظر إلى النمط بعناية واختر الإجابة الصحيحة (1-8) التي تكمله.',
            ],
            'status' => 'published',
            'scale_config' => ['min' => 1, 'max' => 8],
            'scoring_type' => 'category',
            'scoring_config' => [
                'categories' => [
                    [
                        'key' => 'training',
                        'label' => ['en' => 'Training Group', 'ar' => 'مجموعة التدريب'],
                        'interpretation' => $interp,
                    ],
                    [
                        'key' => 'test_part1',
                        'label' => ['en' => 'Test Group (Part 1)', 'ar' => 'المجموعة الاختبارية (الجزء 1)'],
                        'interpretation' => $interp,
                    ],
                    [
                        'key' => 'test_part2',
                        'label' => ['en' => 'Test Group (Part 2)', 'ar' => 'المجموعة الاختبارية (الجزء 2)'],
                        'interpretation' => $interp,
                    ],
                ],
            ],
            'randomize_questions' => false,
            'chart_type' => 'column',
        ]);

        // Answer key from PDF page 39
        // Training group (Q1-12)
        $trainingAnswers = [5, 1, 8, 4, 3, 8, 7, 2, 6, 5, 6, 7];
        // Test group part 1 (Q13-24)
        $testPart1Answers = [8, 4, 6, 1, 2, 4, 7, 4, 5, 1, 8, 7];
        // Test group part 2 (Q25-36)
        $testPart2Answers = [6, 3, 6, 8, 7, 8, 1, 5, 8, 4, 2, 3];

        $allAnswers = array_merge($trainingAnswers, $testPart1Answers, $testPart2Answers);

        for ($i = 0; $i < 36; $i++) {
            $qNum = $i + 1;

            if ($qNum <= 12) {
                $categoryKey = 'training';
            } elseif ($qNum <= 24) {
                $categoryKey = 'test_part1';
            } else {
                $categoryKey = 'test_part2';
            }

            Question::create([
                'test_id' => $test->id,
                'text' => [
                    'en' => "Question {$qNum}",
                    'ar' => "السؤال {$qNum}",
                ],
                'image_path' => "questions/apm/q{$qNum}.png",
                'sort_order' => $qNum,
                'is_reverse_scored' => false,
                'is_required' => true,
                'category_key' => $categoryKey,
                'weight' => 1.00,
                'correct_answer' => $allAnswers[$i],
                'scale_override' => [
                    'min' => 1,
                    'max' => 8,
                ],
            ]);
        }

        $this->command->info("Created Raven's APM test with {$test->questions()->count()} questions.");
    }
}
