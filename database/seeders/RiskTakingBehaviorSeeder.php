<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class RiskTakingBehaviorSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        // Shared interpretation bands. The RTBQ-20's official total bands
        // (20-39, 40-59, 60-79, 80-100 raw) map cleanly to the same
        // percentages because each band is exactly 1/5 of the 20-100 range.
        // The same percentage thresholds apply per-dimension since each
        // dimension also runs 4 questions x 5pt = 4-20 raw.
        $interp = [
            ['min' => 0,  'max' => 39,  'label' => ['en' => 'Low',       'ar' => 'منخفض']],
            ['min' => 40, 'max' => 59,  'label' => ['en' => 'Moderate',  'ar' => 'متوسط']],
            ['min' => 60, 'max' => 79,  'label' => ['en' => 'High',      'ar' => 'مرتفع']],
            ['min' => 80, 'max' => 100, 'label' => ['en' => 'Very High', 'ar' => 'مرتفع جداً']],
        ];

        $test = Test::create([
            'user_id' => $admin->id,
            'title' => [
                'en' => 'Risk-Taking Behavior Questionnaire for Prospective Entrepreneurs (RTBQ-20)',
                'ar' => 'استبيان سلوك المخاطرة لرواد الأعمال المحتملين (RTBQ-20)',
            ],
            'description' => [
                'en' => 'A 20-item questionnaire measuring attitudes and behaviors associated with entrepreneurial risk-taking across five dimensions: Financial, Career and Opportunity, Social and Interpersonal, Innovation and Strategic, and Psychological Tolerance for Risk and Uncertainty.',
                'ar' => 'استبيان من 20 بنداً يقيس الاتجاهات والسلوكيات المرتبطة بسلوك المخاطرة الريادية عبر خمسة أبعاد: المخاطرة المالية، والمخاطرة المهنية والفرص، والمخاطرة الاجتماعية والشخصية، والمخاطرة الابتكارية والاستراتيجية، والتحمل النفسي للمخاطرة وعدم اليقين.',
            ],
            'instructions' => [
                'en' => 'Below are statements related to attitudes and behaviors associated with entrepreneurial risk-taking. Please indicate the extent to which you agree with each statement: 1 = Strongly Disagree, 2 = Disagree, 3 = Neutral, 4 = Agree, 5 = Strongly Agree.',
                'ar' => 'فيما يلي عبارات تتعلق بالاتجاهات والسلوكيات المرتبطة بسلوك المخاطرة الريادية. يُرجى تحديد مدى موافقتك على كل عبارة: 1 = لا أوافق بشدة، 2 = لا أوافق، 3 = محايد، 4 = أوافق، 5 = أوافق بشدة.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 1,
                'max' => 5,
                'labels' => [
                    '1' => ['en' => 'Strongly Disagree', 'ar' => 'لا أوافق بشدة'],
                    '2' => ['en' => 'Disagree',          'ar' => 'لا أوافق'],
                    '3' => ['en' => 'Neutral',           'ar' => 'محايد'],
                    '4' => ['en' => 'Agree',             'ar' => 'أوافق'],
                    '5' => ['en' => 'Strongly Agree',    'ar' => 'أوافق بشدة'],
                ],
            ],
            'scoring_type' => 'category',
            'scoring_config' => [
                'categories' => [
                    ['key' => 'financial',     'label' => ['en' => 'Financial Risk-Taking',                     'ar' => 'المخاطرة المالية'],                          'interpretation' => $interp],
                    ['key' => 'career',        'label' => ['en' => 'Career and Opportunity Risk-Taking',         'ar' => 'المخاطرة المهنية والفرص'],                  'interpretation' => $interp],
                    ['key' => 'social',        'label' => ['en' => 'Social and Interpersonal Risk-Taking',       'ar' => 'المخاطرة الاجتماعية والشخصية'],             'interpretation' => $interp],
                    ['key' => 'innovation',    'label' => ['en' => 'Innovation and Strategic Risk-Taking',       'ar' => 'المخاطرة الابتكارية والاستراتيجية'],         'interpretation' => $interp],
                    ['key' => 'psychological', 'label' => ['en' => 'Psychological Tolerance for Risk',           'ar' => 'التحمل النفسي للمخاطرة وعدم اليقين'],        'interpretation' => $interp],
                ],
            ],
            'randomize_questions' => false,
            'chart_type' => 'column',
        ]);

        $categoryMap = [
            1 => 'financial', 2 => 'financial', 3 => 'financial', 4 => 'financial',
            5 => 'career',    6 => 'career',    7 => 'career',    8 => 'career',
            9 => 'social',   10 => 'social',   11 => 'social',   12 => 'social',
            13 => 'innovation', 14 => 'innovation', 15 => 'innovation', 16 => 'innovation',
            17 => 'psychological', 18 => 'psychological', 19 => 'psychological', 20 => 'psychological',
        ];

        $questions = [
            // Financial Risk-Taking
            1  => ['en' => 'I am willing to invest my own money in a business opportunity even when success is uncertain.', 'ar' => 'لدي الاستعداد لاستثمار أموالي الخاصة في فرصة تجارية حتى عندما يكون النجاح غير مؤكد.'],
            2  => ['en' => 'I feel comfortable making financial decisions that involve moderate to high uncertainty.', 'ar' => 'أشعر بالراحة عند اتخاذ قرارات مالية تنطوي على درجة متوسطة إلى مرتفعة من عدم اليقين.'],
            3  => ['en' => 'I would leave a stable job to pursue a promising business opportunity.', 'ar' => 'لدي الاستعداد لترك وظيفة مستقرة من أجل متابعة فرصة تجارية واعدة.'],
            4  => ['en' => 'I am prepared to tolerate temporary financial losses in order to achieve long-term entrepreneurial success.', 'ar' => 'لدي الاستعداد لتحمل خسائر مالية مؤقتة من أجل تحقيق نجاح ريادي على المدى الطويل.'],

            // Career and Opportunity Risk-Taking
            5  => ['en' => 'I actively seek opportunities that involve uncertainty and challenge.', 'ar' => 'أبحث بنشاط عن الفرص التي تنطوي على عدم يقين وتحدٍ.'],
            6  => ['en' => 'I prefer career paths that offer growth potential even if they involve instability.', 'ar' => 'أفضل المسارات المهنية التي توفر إمكانات للنمو حتى وإن كانت تنطوي على عدم استقرار.'],
            7  => ['en' => 'I am willing to pursue unconventional business ideas despite possible criticism from others.', 'ar' => 'لدي الاستعداد لتبني أفكار تجارية غير تقليدية على الرغم من احتمال انتقاد الآخرين.'],
            8  => ['en' => 'I can make important business decisions even when information is incomplete.', 'ar' => 'أستطيع اتخاذ قرارات تجارية مهمة حتى عندما تكون المعلومات غير مكتملة.'],

            // Social and Interpersonal Risk-Taking
            9  => ['en' => 'I am comfortable presenting new business ideas to unfamiliar people or investors.', 'ar' => 'أشعر بالراحة عند تقديم أفكار تجارية جديدة لأشخاص أو مستثمرين لا أعرفهم.'],
            10 => ['en' => 'I am willing to challenge traditional ways of doing things in professional settings.', 'ar' => 'لدي الاستعداد لتحدي الطرق التقليدية في إنجاز الأمور داخل بيئات العمل المهنية.'],
            11 => ['en' => 'I can handle negative feedback without abandoning my business goals.', 'ar' => 'أستطيع التعامل مع الملاحظات السلبية دون التخلي عن أهدافي التجارية.'],
            12 => ['en' => 'I am willing to take responsibility for decisions that may fail.', 'ar' => 'لدي الاستعداد لتحمل مسؤولية القرارات التي قد تفشل.'],

            // Innovation and Strategic Risk-Taking
            13 => ['en' => 'I enjoy experimenting with new approaches even if they may not succeed.', 'ar' => 'أستمتع بتجربة أساليب جديدة حتى وإن كان من المحتمل ألا تنجح.'],
            14 => ['en' => 'I am likely to adopt innovative solutions before others do.', 'ar' => 'من المحتمل أن أتبنى الحلول الابتكارية قبل أن يفعل ذلك الآخرون.'],
            15 => ['en' => 'I prefer trying new business methods over relying only on proven routines.', 'ar' => 'أفضل تجربة أساليب تجارية جديدة بدلاً من الاعتماد فقط على الإجراءات المعتادة.'],
            16 => ['en' => 'I am willing to enter highly competitive markets if I believe the opportunity is worthwhile.', 'ar' => 'لدي الاستعداد لدخول أسواق شديدة التنافسية إذا كنت أعتقد أن الفرصة تستحق ذلك.'],

            // Psychological Tolerance for Risk and Uncertainty
            17 => ['en' => 'Uncertainty about the future motivates rather than discourages me.', 'ar' => 'يدفعني عدم اليقين بشأن المستقبل إلى التحفيز بدلاً من الإحباط.'],
            18 => ['en' => 'I remain confident when facing unpredictable business situations.', 'ar' => 'أبقى واثقاً عند مواجهة مواقف تجارية لا يمكن التنبؤ بها.'],
            19 => ['en' => 'I can function effectively under pressure and ambiguity.', 'ar' => 'أستطيع الأداء بفاعلية تحت الضغط والغموض.'],
            20 => ['en' => 'I see calculated risk-taking as necessary for entrepreneurial success.', 'ar' => 'أرى أن المخاطرة المحسوبة ضرورية لتحقيق النجاح الريادي.'],
        ];

        foreach ($questions as $qNum => $q) {
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

        $this->command->info("Created Risk-Taking Behavior Questionnaire (RTBQ-20) with {$test->questions()->count()} questions across 5 dimensions.");
    }
}
