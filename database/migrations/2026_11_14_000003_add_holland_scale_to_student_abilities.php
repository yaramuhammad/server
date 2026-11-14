<?php

use App\Models\Assessment;
use App\Models\TestAttempt;
use Database\Seeders\StudentAbilities\HollandVocationalScale;
use Database\Seeders\StudentAbilitiesSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds the Holland Vocational Factors Scale as the last test of "قدرات و اتجاهات الطلاب" on
 * installs where that assessment already exists (fresh installs get it from the seeder).
 *
 * The assessment's description and instructions and its links' welcome message described eight
 * tests; they are updated to the nine-test wording, but only where they still read exactly as
 * originally seeded, so an admin's own edits are left alone.
 *
 * Rolling back removes the test again, unless someone has already taken it.
 */
return new class extends Migration
{
    private const SEEDED_DESCRIPTION = [
        'en' => 'A comprehensive student battery: general cognitive ability, differential aptitudes (DAT), achievement in mathematics, natural sciences, social sciences, Arabic and English, and cross-cultural readiness for studying abroad.',
        'ar' => 'بطارية شاملة للطلاب تقيس القدرات المعرفية العامة، والقدرات الفارقة (DAT)، والتحصيل في الرياضيات والعلوم الطبيعية والعلوم الاجتماعية واللغتين العربية والإنجليزية، والجاهزية للتكيف الثقافي والدراسة في الخارج.',
    ];

    private const SEEDED_INSTRUCTIONS = [
        'en' => 'This assessment contains eight tests. Seven are multiple-choice tests with one correct answer per question; the last is a self-report scale with no right or wrong answers. The Arabic test is in Arabic and the English test is in English. Work carefully and answer every question.',
        'ar' => 'يتضمن هذا التقييم ثمانية اختبارات: سبعة اختبارات اختيار من متعدد لكل سؤال فيها إجابة صحيحة واحدة، والأخير مقياس تقرير ذاتي لا توجد فيه إجابات صحيحة أو خاطئة. اختبار اللغة العربية باللغة العربية واختبار اللغة الإنجليزية باللغة الإنجليزية. اعمل بتركيز وأجب عن جميع الأسئلة.',
    ];

    private const SEEDED_WELCOME_MESSAGE = [
        'en' => 'Welcome. This assessment includes eight tests about your abilities, academic achievement and readiness to study abroad. It is long (about two and a half hours in total) and each test is submitted separately. Please answer carefully and honestly.',
        'ar' => 'مرحبًا بك. يتضمن هذا التقييم ثمانية اختبارات حول قدراتك وتحصيلك الدراسي وجاهزيتك للدراسة في الخارج. التقييم طويل (نحو ساعتين ونصف إجمالًا)، ويُسلَّم كل اختبار على حدة. يرجى الإجابة بتركيز وصدق.',
    ];

    public function up(): void
    {
        $assessments = Assessment::where('title->ar', StudentAbilitiesSeeder::TITLE_AR)->with('links')->get();

        foreach ($assessments as $assessment) {
            if ($assessment->tests()->where('title->ar', HollandVocationalScale::TITLE_AR)->exists()) {
                continue;
            }

            DB::transaction(function () use ($assessment) {
                $test = HollandVocationalScale::create($assessment->user_id);

                $lastPosition = (int) DB::table('assessment_test')->where('assessment_id', $assessment->id)->max('sort_order');
                $assessment->tests()->attach($test->id, ['sort_order' => $lastPosition + 1]);

                if ($assessment->description == self::SEEDED_DESCRIPTION) {
                    $assessment->description = StudentAbilitiesSeeder::DESCRIPTION;
                }
                if ($assessment->instructions == self::SEEDED_INSTRUCTIONS) {
                    $assessment->instructions = StudentAbilitiesSeeder::INSTRUCTIONS;
                }
                $assessment->save();

                foreach ($assessment->links as $link) {
                    if ($link->welcome_message == self::SEEDED_WELCOME_MESSAGE) {
                        $link->update(['welcome_message' => StudentAbilitiesSeeder::WELCOME_MESSAGE]);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        $assessments = Assessment::where('title->ar', StudentAbilitiesSeeder::TITLE_AR)->with('links')->get();

        foreach ($assessments as $assessment) {
            DB::transaction(function () use ($assessment) {
                foreach ($assessment->tests()->where('title->ar', HollandVocationalScale::TITLE_AR)->get() as $test) {
                    if (TestAttempt::where('test_id', $test->id)->exists()) {
                        throw new RuntimeException("The Holland test {$test->id} already has attempts; not removing it.");
                    }
                    $assessment->tests()->detach($test->id);
                    $test->delete();
                }

                if ($assessment->description == StudentAbilitiesSeeder::DESCRIPTION) {
                    $assessment->description = self::SEEDED_DESCRIPTION;
                }
                if ($assessment->instructions == StudentAbilitiesSeeder::INSTRUCTIONS) {
                    $assessment->instructions = self::SEEDED_INSTRUCTIONS;
                }
                $assessment->save();

                foreach ($assessment->links as $link) {
                    if ($link->welcome_message == StudentAbilitiesSeeder::WELCOME_MESSAGE) {
                        $link->update(['welcome_message' => self::SEEDED_WELCOME_MESSAGE]);
                    }
                }
            });
        }
    }
};
