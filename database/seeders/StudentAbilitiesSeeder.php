<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Database\Seeders\StudentAbilities\ArabicLanguageAchievementTest;
use Database\Seeders\StudentAbilities\CrossCulturalReadinessBattery;
use Database\Seeders\StudentAbilities\DifferentialAptitudeTests;
use Database\Seeders\StudentAbilities\EnglishLanguageAchievementTest;
use Database\Seeders\StudentAbilities\GeneralCognitiveBattery;
use Database\Seeders\StudentAbilities\MathematicsAchievementTest;
use Database\Seeders\StudentAbilities\NaturalSciencesAchievementTest;
use Database\Seeders\StudentAbilities\SocialSciencesAchievementTest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Random\Engine\Mt19937;
use Random\Randomizer;
use RuntimeException;

/**
 * "قدرات و اتجاهات الطلاب" — Student Abilities & Attitudes.
 *
 * Seeds eight tests (content lives in database/seeders/StudentAbilities/, in source-PDF order):
 *   - 7 multiple-choice ability / achievement tests: 1 point per correct answer, `category`
 *     scoring per section with Low / Moderate / High bands, plus an overall band on the total.
 *   - 1 Likert self-report battery (Cross-Cultural Readiness): `category` scoring per subscale
 *     plus the source's overall readiness index on the raw total.
 *
 * Multiple-choice options are reordered so correct answers spread evenly over A-D (the source
 * keys are heavily position-biased). The shuffle is seeded per test, so every run produces the
 * same order. Content and keys in the data files stay in PDF order for auditing.
 *
 * Then bundles the eight tests into one assessment with a public link that stays open for two
 * months and has no participant cap.
 */
class StudentAbilitiesSeeder extends Seeder
{
    public const TITLE_AR = 'قدرات و اتجاهات الطلاب';

    public const TITLE_EN = 'Student Abilities & Attitudes';

    /** Shared 3-band interpretation for the multiple-choice tests (percent correct). */
    private array $mcqBands;

    /** Overall interpretation of the total for the multiple-choice tests. */
    private array $mcqOverall;

    public function run(): void
    {
        $admin = User::first();

        if (! $admin) {
            $this->command->error('No user found. Run UserSeeder first.');

            return;
        }

        if (Assessment::where('title->ar', self::TITLE_AR)->exists()) {
            $this->command->warn('Assessment "' . self::TITLE_AR . '" already exists — nothing seeded.');

            return;
        }

        $low = ['en' => 'Low', 'ar' => 'منخفض'];
        $moderate = ['en' => 'Moderate', 'ar' => 'متوسط'];
        $high = ['en' => 'High', 'ar' => 'مرتفع'];

        $this->mcqBands = [
            ['min' => 0, 'max' => 49.99, 'label' => $low],
            ['min' => 50, 'max' => 74.99, 'label' => $moderate],
            ['min' => 75, 'max' => 100, 'label' => $high],
        ];

        $this->mcqOverall = [
            [
                'min' => 0, 'max' => 49.99, 'label' => $low,
                'description' => [
                    'en' => 'Below 50% correct: foundational gaps in this area; targeted remediation is recommended.',
                    'ar' => 'أقل من 50% إجابات صحيحة: توجد فجوات أساسية في هذا المجال، ويوصى ببرنامج علاجي موجه.',
                ],
            ],
            [
                'min' => 50, 'max' => 74.99, 'label' => $moderate,
                'description' => [
                    'en' => '50-74% correct: adequate command of this area with room to strengthen the weaker sections.',
                    'ar' => 'من 50% إلى 74% إجابات صحيحة: مستوى مقبول في هذا المجال مع إمكانية تقوية الأقسام الأضعف.',
                ],
            ],
            [
                'min' => 75, 'max' => 100, 'label' => $high,
                'description' => [
                    'en' => '75% correct or more: strong command of this area.',
                    'ar' => '75% إجابات صحيحة فأكثر: تمكن قوي في هذا المجال.',
                ],
            ],
        ];

        DB::transaction(function () use ($admin) {
            $tests = [
                $this->mcqTest($admin, GeneralCognitiveBattery::definition(), 'cognitive'),
                $this->mcqTest($admin, DifferentialAptitudeTests::definition(), 'dat'),
                $this->mcqTest($admin, MathematicsAchievementTest::definition(), 'math'),
                $this->mcqTest($admin, NaturalSciencesAchievementTest::definition(), 'natural'),
                $this->mcqTest($admin, SocialSciencesAchievementTest::definition(), 'social'),
                $this->mcqTest($admin, ArabicLanguageAchievementTest::definition(), 'arabic'),
                $this->mcqTest($admin, EnglishLanguageAchievementTest::definition(), 'english'),
                $this->likertTest($admin, CrossCulturalReadinessBattery::definition()),
            ];

            $this->buildAssessment($admin, $tests);
        });
    }

    // ------------------------------------------------------------------
    // Test builders
    // ------------------------------------------------------------------

    private function mcqTest(User $admin, array $def, string $seedKey): Test
    {
        $test = Test::create([
            'user_id' => $admin->id,
            'title' => $this->loc($def['title']),
            'description' => $this->loc($def['description']),
            'instructions' => $this->loc($def['instructions']),
            'status' => 'published',
            'scale_config' => ['min' => 1, 'max' => 4],
            'scoring_type' => 'category',
            'scoring_config' => [
                'categories' => $this->categories($def['categories'], $this->mcqBands),
                'ranges' => $this->mcqOverall,
                'use_percentage' => true,
            ],
            'chart_type' => $def['chart'],
            'randomize_questions' => false,
        ]);

        $rng = new Randomizer(new Mt19937(crc32('student-abilities:' . $seedKey)));
        $questions = $def['questions'];

        // Balanced target positions for the correct option: each of A-D gets n/4 (±1), in random order.
        $targets = array_slice(array_merge(...array_fill(0, intdiv(count($questions), 4) + 1, [0, 1, 2, 3])), 0, count($questions));
        $targets = $rng->shuffleArray($targets);

        foreach ($questions as $i => $q) {
            $target = $targets[$i];
            $distractorOrder = $rng->shuffleArray([0, 1, 2]);

            // Monolingual tests define one side only; the other locale shows the same text.
            $en = $q['en'] ?? $q['ar'];
            $ar = $q['ar'] ?? $q['en'];

            $enOptions = $this->arrange($en[1], $en[2], $target, $distractorOrder);
            $arOptions = $this->arrange($ar[1], $ar[2], $target, $distractorOrder);

            $labels = [];
            foreach ($enOptions as $k => $enLabel) {
                $labels[(string) ($k + 1)] = ['en' => $enLabel, 'ar' => $arOptions[$k]];
            }

            Question::create([
                'test_id' => $test->id,
                'text' => ['en' => $en[0], 'ar' => $ar[0]],
                'sort_order' => $i + 1,
                'is_reverse_scored' => false,
                'is_required' => true,
                'category_key' => $q['cat'],
                'weight' => 1.00,
                'correct_answer' => $target + 1,
                'scale_override' => ['min' => 1, 'max' => 4, 'labels' => $labels],
            ]);
        }

        $title = $this->loc($def['title'])['en'];
        $this->command->info("  ✓ {$title} — " . count($questions) . ' questions, ' . count($def['categories']) . " sections, {$def['chart']} chart");

        return $test;
    }

    private function likertTest(User $admin, array $def): Test
    {
        $test = Test::create([
            'user_id' => $admin->id,
            'title' => $def['title'],
            'description' => $def['description'],
            'instructions' => $def['instructions'],
            'status' => 'published',
            'scale_config' => ['min' => 1, 'max' => 5, 'labels' => $def['likert']],
            'scoring_type' => 'category',
            'scoring_config' => [
                'categories' => $this->categories($def['categories'], $def['interpretation']),
                'ranges' => $def['overall']['ranges'],
                'use_percentage' => $def['overall']['use_percentage'],
            ],
            'chart_type' => $def['chart'],
            'randomize_questions' => false,
        ]);

        foreach ($def['questions'] as $i => $q) {
            Question::create([
                'test_id' => $test->id,
                'text' => $q['text'],
                'sort_order' => $i + 1,
                'is_reverse_scored' => false,
                'is_required' => true,
                'category_key' => $q['cat'],
                'weight' => 1.00,
            ]);
        }

        $this->command->info("  ✓ {$def['title']['en']} — " . count($def['questions']) . ' items, ' . count($def['categories']) . " subscales, {$def['chart']} chart");

        return $test;
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Place the correct option (1-based $key) at $target and the three distractors, in
     * $distractorOrder, around it.
     *
     * @param  list<string>  $options  four options in source order
     * @return list<string>
     */
    private function arrange(array $options, int $key, int $target, array $distractorOrder): array
    {
        if (count($options) !== 4 || $key < 1 || $key > 4) {
            throw new RuntimeException('Each question needs exactly 4 options and a key from 1 to 4: ' . json_encode($options, JSON_UNESCAPED_UNICODE));
        }

        $correct = $options[$key - 1];
        unset($options[$key - 1]);
        $distractors = array_values($options);

        $arranged = array_map(fn ($i) => $distractors[$i], $distractorOrder);
        array_splice($arranged, $target, 0, [$correct]);

        return $arranged;
    }

    /** @param  array<string, string|array{en:string,ar:string}>  $categories  key => label */
    private function categories(array $categories, array $interpretation): array
    {
        return array_map(
            fn ($key, $label) => [
                'key' => $key,
                'label' => $this->loc($label),
                'interpretation' => $interpretation,
            ],
            array_keys($categories),
            array_values($categories),
        );
    }

    /** A plain string is a single-language test field: show it unchanged in both locales. */
    private function loc(string|array $value): array
    {
        return is_string($value) ? ['en' => $value, 'ar' => $value] : $value;
    }

    // ------------------------------------------------------------------
    // Assessment + link
    // ------------------------------------------------------------------

    private function buildAssessment(User $admin, array $tests): void
    {
        $assessment = Assessment::create([
            'user_id' => $admin->id,
            'title' => [
                'en' => self::TITLE_EN,
                'ar' => self::TITLE_AR,
            ],
            'description' => [
                'en' => "A comprehensive student battery: general cognitive ability, differential aptitudes (DAT), achievement in mathematics, natural sciences, social sciences, Arabic and English, and cross-cultural readiness for studying abroad.",
                'ar' => 'بطارية شاملة للطلاب تقيس القدرات المعرفية العامة، والقدرات الفارقة (DAT)، والتحصيل في الرياضيات والعلوم الطبيعية والعلوم الاجتماعية واللغتين العربية والإنجليزية، والجاهزية للتكيف الثقافي والدراسة في الخارج.',
            ],
            'instructions' => [
                'en' => 'This assessment contains eight tests. Seven are multiple-choice tests with one correct answer per question; the last is a self-report scale with no right or wrong answers. The Arabic test is in Arabic and the English test is in English. Work carefully and answer every question.',
                'ar' => 'يتضمن هذا التقييم ثمانية اختبارات: سبعة اختبارات اختيار من متعدد لكل سؤال فيها إجابة صحيحة واحدة، والأخير مقياس تقرير ذاتي لا توجد فيه إجابات صحيحة أو خاطئة. اختبار اللغة العربية باللغة العربية واختبار اللغة الإنجليزية باللغة الإنجليزية. اعمل بتركيز وأجب عن جميع الأسئلة.',
            ],
            'status' => 'published',
            'show_results_to_participant' => true,
        ]);

        foreach ($tests as $index => $test) {
            $assessment->tests()->attach($test->id, ['sort_order' => $index + 1]);
        }

        $link = AssessmentLink::create([
            'assessment_id' => $assessment->id,
            'created_by' => $admin->id,
            'title' => self::TITLE_AR . ' — رابط عام',
            'starts_at' => now(),
            'expires_at' => now()->addMonths(2),
            'max_participants' => null,
            'is_active' => true,
            'collect_name' => true,
            'collect_email' => true,
            'collect_phone' => false,
            'collect_company' => false,
            'collect_job_title' => false,
            'collect_age' => true,
            'collect_gender' => true,
            'allow_profile_view' => true,
            'allow_profile_download' => true,
            'welcome_message' => [
                'en' => 'Welcome. This assessment includes eight tests about your abilities, academic achievement and readiness to study abroad. It is long (about two and a half hours in total) and each test is submitted separately. Please answer carefully and honestly.',
                'ar' => 'مرحبًا بك. يتضمن هذا التقييم ثمانية اختبارات حول قدراتك وتحصيلك الدراسي وجاهزيتك للدراسة في الخارج. التقييم طويل (نحو ساعتين ونصف إجمالًا)، ويُسلَّم كل اختبار على حدة. يرجى الإجابة بتركيز وصدق.',
            ],
            'completion_message' => [
                'en' => 'Thank you for completing the Student Abilities & Attitudes assessment.',
                'ar' => 'شكرًا لإكمالك تقييم قدرات و اتجاهات الطلاب.',
            ],
        ]);

        $questionCount = collect($tests)->sum(fn (Test $t) => $t->questions()->count());

        $this->command->info('');
        $this->command->info('=== ' . self::TITLE_AR . ' / ' . self::TITLE_EN . ' ===');
        $this->command->info("Assessment #{$assessment->id} created with " . count($tests) . " tests ({$questionCount} questions).");
        $this->command->info("Link active {$link->starts_at->toDateString()} → {$link->expires_at->toDateString()} (unlimited participants)");
        $this->command->info("Participant URL: {$link->getUrl()}");
    }
}
