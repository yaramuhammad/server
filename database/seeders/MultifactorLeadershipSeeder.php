<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class MultifactorLeadershipSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $interp = [
            ['min' => 0, 'max' => 39, 'label' => ['en' => 'Low', 'ar' => 'منخفض']],
            ['min' => 40, 'max' => 69, 'label' => ['en' => 'Moderate', 'ar' => 'متوسط']],
            ['min' => 70, 'max' => 100, 'label' => ['en' => 'High', 'ar' => 'مرتفع']],
        ];

        $test = Test::create([
            'user_id' => $admin->id,
            'title' => [
                'en' => 'Multifactor Leadership Questionnaire (MLQ)',
                'ar' => 'استبيان القيادة متعدد العوامل',
            ],
            'description' => [
                'en' => 'A 45-item questionnaire measuring leadership styles across Transformational, Transactional, and Passive Avoidant dimensions, plus Outcomes of Leadership.',
                'ar' => 'استبيان من 45 بنداً يقيس أساليب القيادة عبر الأبعاد التحويلية والتبادلية والتجنبية السلبية، بالإضافة إلى نتائج القيادة.',
            ],
            'instructions' => [
                'en' => 'This questionnaire describes the leadership style of the individual you are rating. Judge how frequently each statement fits the person you are describing: 1 = Not at all, 2 = Once in a while, 3 = Sometimes, 4 = Fairly often, 5 = Frequently, if not always.',
                'ar' => 'يصف هذا الاستبيان أسلوب القيادة للشخص الذي تقوم بتقييمه. حدد مدى تكرار انطباق كل عبارة على الشخص الذي تصفه: 1 = لا على الإطلاق، 2 = نادراً، 3 = أحياناً، 4 = في كثير من الأحيان، 5 = دائماً أو شبه دائم.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 1,
                'max' => 5,
                'labels' => [
                    '1' => ['en' => 'Not at all', 'ar' => 'لا على الإطلاق'],
                    '2' => ['en' => 'Once in a while', 'ar' => 'نادراً'],
                    '3' => ['en' => 'Sometimes', 'ar' => 'أحياناً'],
                    '4' => ['en' => 'Fairly often', 'ar' => 'في كثير من الأحيان'],
                    '5' => ['en' => 'Frequently, if not always', 'ar' => 'دائماً أو شبه دائم'],
                ],
            ],
            'scoring_type' => 'category',
            'scoring_config' => ['categories' => [
                ['key' => 'transformational', 'label' => ['en' => 'Transformational', 'ar' => 'القيادة التحويلية'], 'interpretation' => $interp],
                ['key' => 'transactional', 'label' => ['en' => 'Transactional', 'ar' => 'القيادة التبادلية'], 'interpretation' => $interp],
                ['key' => 'passive_avoidant', 'label' => ['en' => 'Passive Avoidant', 'ar' => 'القيادة التجنبية السلبية'], 'interpretation' => $interp],
                ['key' => 'outcomes', 'label' => ['en' => 'Outcomes of Leadership', 'ar' => 'نتائج القيادة'], 'interpretation' => $interp],
            ]],
            'randomize_questions' => false,
        ]);

        // Category map by question number (from the scoring key image)
        // Transformational: IA(10,18,21,25) + IB(6,14,23,34) + IM(9,13,26,36) + IS(2,8,30,32) + IC(15,19,29,31)
        // Transactional: CR(1,11,16,35) + MBEA(4,22,24,27)
        // Passive Avoidant: MBEP(3,12,17,20) + LF(5,7,28,33)
        // Outcomes of Leadership: EE(39,42,44) + EFF(37,40,43,45) + SAT(38,41)
        $categoryMap = [];
        foreach ([10,18,21,25, 6,14,23,34, 9,13,26,36, 2,8,30,32, 15,19,29,31] as $n) $categoryMap[$n] = 'transformational';
        foreach ([1,11,16,35, 4,22,24,27] as $n) $categoryMap[$n] = 'transactional';
        foreach ([3,12,17,20, 5,7,28,33] as $n) $categoryMap[$n] = 'passive_avoidant';
        foreach ([39,42,44, 37,40,43,45, 38,41] as $n) $categoryMap[$n] = 'outcomes';

        $questions = [
            // 1
            ['en' => 'Provides me with assistance in exchange for my efforts', 'ar' => 'يقدم لي المساعدة مقابل جهودي'],
            // 2
            ['en' => 'Re-examines critical assumptions to question whether they are appropriate', 'ar' => 'يعيد فحص الافتراضات الجوهرية للتساؤل عما إذا كانت مناسبة'],
            // 3
            ['en' => 'Fails to interfere until problems become serious', 'ar' => 'يفشل في التدخل حتى تصبح المشكلات خطيرة'],
            // 4
            ['en' => 'Focuses attention on irregularities, mistakes, exceptions, and deviations from standards', 'ar' => 'يركز انتباهه على المخالفات والأخطاء والاستثناءات والانحرافات عن المعايير'],
            // 5
            ['en' => 'Avoids getting involved when important issues arise', 'ar' => 'يتجنب التدخل عند ظهور قضايا مهمة'],
            // 6
            ['en' => 'Talks about his/her most important values and beliefs', 'ar' => 'يتحدث عن أهم قيمه ومعتقداته'],
            // 7
            ['en' => 'Is absent when needed', 'ar' => 'يكون غائباً عند الحاجة إليه'],
            // 8
            ['en' => 'Seeks differing perspectives when solving problems', 'ar' => 'يبحث عن وجهات نظر مختلفة عند حل المشكلات'],
            // 9
            ['en' => 'Talks optimistically about the future', 'ar' => 'يتحدث بتفاؤل عن المستقبل'],
            // 10
            ['en' => 'Instills pride in me for being associated with him/her', 'ar' => 'يبث فيّ الفخر لارتباطي به'],
            // 11
            ['en' => 'Discusses in specific terms who is responsible for achieving performance targets', 'ar' => 'يناقش بشكل محدد من المسؤول عن تحقيق أهداف الأداء'],
            // 12
            ['en' => 'Waits for things to go wrong before taking action', 'ar' => 'ينتظر حتى تسوء الأمور قبل اتخاذ إجراء'],
            // 13
            ['en' => 'Talks enthusiastically about what needs to be accomplished', 'ar' => 'يتحدث بحماس عما يجب إنجازه'],
            // 14
            ['en' => 'Specifies the importance of having a strong sense of purpose', 'ar' => 'يحدد أهمية وجود إحساس قوي بالهدف'],
            // 15
            ['en' => 'Spends time teaching and coaching', 'ar' => 'يقضي وقتاً في التعليم والتدريب'],
            // 16
            ['en' => 'Makes clear what one can expect to receive when performance goals are achieved', 'ar' => 'يوضح ما يمكن أن يتوقعه المرء عند تحقيق أهداف الأداء'],
            // 17
            ['en' => 'Shows that he/she is a firm believer in "If it ain\'t broke, don\'t fix it"', 'ar' => 'يُظهر أنه يؤمن بشدة بمبدأ "إذا لم يكن مكسوراً فلا تصلحه"'],
            // 18
            ['en' => 'Goes beyond self-interest for the good of the group', 'ar' => 'يتجاوز مصلحته الشخصية من أجل مصلحة المجموعة'],
            // 19
            ['en' => 'Treats me as an individual rather than just as a member of a group', 'ar' => 'يعاملني كفرد وليس مجرد عضو في مجموعة'],
            // 20
            ['en' => 'Demonstrates that problems must become chronic before taking action', 'ar' => 'يُظهر أن المشكلات يجب أن تصبح مزمنة قبل اتخاذ إجراء'],
            // 21
            ['en' => 'Acts in ways that builds my respect', 'ar' => 'يتصرف بطرق تبني احترامي له'],
            // 22
            ['en' => 'Concentrates his/her full attention on dealing with mistakes, complaints, and failures', 'ar' => 'يركز كل انتباهه على التعامل مع الأخطاء والشكاوى والإخفاقات'],
            // 23
            ['en' => 'Considers the moral and ethical consequences of decisions', 'ar' => 'يأخذ في الاعتبار العواقب الأخلاقية للقرارات'],
            // 24
            ['en' => 'Keeps track of all mistakes', 'ar' => 'يتابع جميع الأخطاء'],
            // 25
            ['en' => 'Displays a sense of power and confidence', 'ar' => 'يُظهر إحساساً بالقوة والثقة'],
            // 26
            ['en' => 'Articulates a compelling vision of the future', 'ar' => 'يصوغ رؤية مقنعة للمستقبل'],
            // 27
            ['en' => 'Directs my attention toward failures to meet standards', 'ar' => 'يوجه انتباهي نحو حالات الفشل في تلبية المعايير'],
            // 28
            ['en' => 'Avoids making decisions', 'ar' => 'يتجنب اتخاذ القرارات'],
            // 29
            ['en' => 'Considers me as having different needs, abilities, and aspirations from others', 'ar' => 'يعتبرني شخصاً له احتياجات وقدرات وتطلعات مختلفة عن الآخرين'],
            // 30
            ['en' => 'Gets me to look at problems from many different angles', 'ar' => 'يجعلني أنظر إلى المشكلات من زوايا مختلفة عديدة'],
            // 31
            ['en' => 'Helps me to develop my strengths', 'ar' => 'يساعدني على تطوير نقاط قوتي'],
            // 32
            ['en' => 'Suggests new ways of looking at how to complete assignments', 'ar' => 'يقترح طرقاً جديدة للنظر في كيفية إنجاز المهام'],
            // 33
            ['en' => 'Delays responding to urgent questions', 'ar' => 'يتأخر في الاستجابة للأسئلة العاجلة'],
            // 34
            ['en' => 'Emphasizes the importance of having a collective sense of mission', 'ar' => 'يؤكد على أهمية وجود إحساس جماعي بالرسالة'],
            // 35
            ['en' => 'Expresses satisfaction when I meet expectations', 'ar' => 'يعبر عن رضاه عندما ألبي التوقعات'],
            // 36
            ['en' => 'Expresses confidence that goals will be achieved', 'ar' => 'يعبر عن ثقته بأن الأهداف ستتحقق'],
            // 37
            ['en' => 'Is effective in meeting my job-related needs', 'ar' => 'فعّال في تلبية احتياجاتي المتعلقة بالعمل'],
            // 38
            ['en' => 'Uses methods of leadership that are satisfying', 'ar' => 'يستخدم أساليب قيادية مُرضية'],
            // 39
            ['en' => 'Gets me to do more than I expected to do', 'ar' => 'يجعلني أقوم بأكثر مما كنت أتوقع'],
            // 40
            ['en' => 'Is effective in representing me to higher authority', 'ar' => 'فعّال في تمثيلي أمام السلطة العليا'],
            // 41
            ['en' => 'Works with me in a satisfactory way', 'ar' => 'يعمل معي بطريقة مُرضية'],
            // 42
            ['en' => 'Heightens my desire to succeed', 'ar' => 'يزيد من رغبتي في النجاح'],
            // 43
            ['en' => 'Is effective in meeting organizational requirements', 'ar' => 'فعّال في تلبية المتطلبات التنظيمية'],
            // 44
            ['en' => 'Increases my willingness to try harder', 'ar' => 'يزيد من استعدادي لبذل المزيد من الجهد'],
            // 45
            ['en' => 'Leads a group that is effective', 'ar' => 'يقود مجموعة فعّالة'],
        ];

        foreach ($questions as $index => $q) {
            $qNum = $index + 1;
            Question::create([
                'test_id' => $test->id,
                'text' => ['en' => $q['en'], 'ar' => $q['ar']],
                'sort_order' => $qNum,
                'is_reverse_scored' => false,
                'is_required' => true,
                'category_key' => $categoryMap[$qNum],
                'weight' => 1.00,
            ]);
        }

        $this->command->info("Created Multifactor Leadership Questionnaire (MLQ) with {$test->questions()->count()} questions across 4 categories.");
    }
}
