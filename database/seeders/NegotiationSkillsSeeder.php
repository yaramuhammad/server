<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class NegotiationSkillsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        // Same band structure as RTBQ-20: each dimension has 4 questions x 5pt
        // (range 4-20 raw, 0-100%), and the total is 20 questions x 5pt
        // (range 20-100 raw, 0-100%). The same percentage thresholds apply
        // to both the overall and per-dimension interpretation.
        $interp = [
            ['min' => 0,  'max' => 39,  'label' => ['en' => 'Low',       'ar' => 'منخفض']],
            ['min' => 40, 'max' => 59,  'label' => ['en' => 'Moderate',  'ar' => 'متوسط']],
            ['min' => 60, 'max' => 79,  'label' => ['en' => 'High',      'ar' => 'مرتفع']],
            ['min' => 80, 'max' => 100, 'label' => ['en' => 'Very High', 'ar' => 'مرتفع جداً']],
        ];

        $test = Test::create([
            'user_id' => $admin->id,
            'title' => [
                'en' => 'Negotiation Skills Questionnaire for Prospective Entrepreneurs (NSQ-20)',
                'ar' => 'استبيان مهارات التفاوض لرواد الأعمال المحتملين (NSQ-20)',
            ],
            'description' => [
                'en' => 'A 20-item questionnaire measuring negotiation behaviors and attitudes in entrepreneurial and business contexts across five dimensions: Communication and Persuasion, Listening and Relationship Management, Strategic Thinking and Preparation, Emotional Regulation and Conflict Handling, and Decision-Making and Entrepreneurial Negotiation Confidence.',
                'ar' => 'استبيان من 20 بنداً يقيس سلوكيات واتجاهات التفاوض في السياقات الريادية والتجارية عبر خمسة أبعاد: التواصل والإقناع، والإصغاء وإدارة العلاقات، والتفكير الاستراتيجي والإعداد، وتنظيم الانفعالات وإدارة الصراع، واتخاذ القرار والثقة التفاوضية الريادية.',
            ],
            'instructions' => [
                'en' => 'The following statements describe behaviors and attitudes related to negotiation in entrepreneurial and business contexts. Please indicate the extent to which you agree with each statement: 1 = Strongly Disagree, 2 = Disagree, 3 = Neutral, 4 = Agree, 5 = Strongly Agree.',
                'ar' => 'تصف العبارات التالية سلوكيات واتجاهات مرتبطة بالتفاوض في السياقات الريادية والتجارية. يُرجى تحديد مدى موافقتك على كل عبارة: 1 = لا أوافق بشدة، 2 = لا أوافق، 3 = محايد، 4 = أوافق، 5 = أوافق بشدة.',
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
                    ['key' => 'communication', 'label' => ['en' => 'Communication and Persuasion Skills',                  'ar' => 'التواصل والإقناع'],                          'interpretation' => $interp],
                    ['key' => 'listening',     'label' => ['en' => 'Listening and Relationship Management',                'ar' => 'الإصغاء وإدارة العلاقات'],                    'interpretation' => $interp],
                    ['key' => 'strategic',     'label' => ['en' => 'Strategic Thinking and Preparation',                   'ar' => 'التفكير الاستراتيجي والإعداد'],               'interpretation' => $interp],
                    ['key' => 'emotional',     'label' => ['en' => 'Emotional Regulation and Conflict Handling',           'ar' => 'تنظيم الانفعالات وإدارة الصراع'],             'interpretation' => $interp],
                    ['key' => 'decision',      'label' => ['en' => 'Decision-Making and Negotiation Confidence',            'ar' => 'اتخاذ القرار والثقة التفاوضية'],              'interpretation' => $interp],
                ],
            ],
            'randomize_questions' => false,
            'chart_type' => 'column',
        ]);

        $categoryMap = [
            1 => 'communication', 2 => 'communication', 3 => 'communication', 4 => 'communication',
            5 => 'listening',     6 => 'listening',     7 => 'listening',     8 => 'listening',
            9 => 'strategic',    10 => 'strategic',    11 => 'strategic',    12 => 'strategic',
            13 => 'emotional',   14 => 'emotional',   15 => 'emotional',   16 => 'emotional',
            17 => 'decision',    18 => 'decision',    19 => 'decision',    20 => 'decision',
        ];

        $questions = [
            // Communication and Persuasion Skills
            1  => ['en' => 'I can clearly express my ideas and expectations during negotiations.', 'ar' => 'أستطيع التعبير عن أفكاري وتوقعاتي بوضوح خلال المفاوضات.'],
            2  => ['en' => 'I am able to persuade others using logical arguments and evidence.', 'ar' => 'لدي القدرة على إقناع الآخرين باستخدام الحجج المنطقية والأدلة.'],
            3  => ['en' => 'I communicate confidently when discussing business opportunities.', 'ar' => 'أتواصل بثقة عند مناقشة الفرص التجارية.'],
            4  => ['en' => 'I can adapt my communication style depending on the person I am negotiating with.', 'ar' => 'أستطيع تكييف أسلوب تواصلي بحسب الشخص الذي أتفاوض معه.'],

            // Listening and Relationship Management
            5  => ['en' => 'I pay close attention to the needs and concerns of the other party.', 'ar' => 'أولي اهتماماً وثيقاً لاحتياجات الطرف الآخر ومخاوفه.'],
            6  => ['en' => 'I can build trust quickly during business discussions.', 'ar' => 'أستطيع بناء الثقة بسرعة خلال المحادثات التجارية.'],
            7  => ['en' => 'I remain respectful and professional even during difficult negotiations.', 'ar' => 'أحافظ على الاحترام والمهنية حتى في أصعب المفاوضات.'],
            8  => ['en' => 'I try to create win–win outcomes whenever possible.', 'ar' => 'أسعى إلى تحقيق نتائج يربح فيها الطرفان كلما أمكن ذلك.'],

            // Strategic Thinking and Preparation
            9  => ['en' => 'I usually prepare alternative options before entering negotiations.', 'ar' => 'عادةً ما أُعدّ خيارات بديلة قبل الدخول في المفاوضات.'],
            10 => ['en' => 'I can identify the strengths and weaknesses of the other party\'s position.', 'ar' => 'أستطيع تحديد نقاط القوة والضعف في موقف الطرف الآخر.'],
            11 => ['en' => 'I am skilled at finding creative solutions during disagreements.', 'ar' => 'لدي مهارة في إيجاد حلول إبداعية عند الاختلاف.'],
            12 => ['en' => 'I can negotiate effectively even when information is incomplete.', 'ar' => 'أستطيع التفاوض بفاعلية حتى عندما تكون المعلومات غير مكتملة.'],

            // Emotional Regulation and Conflict Handling
            13 => ['en' => 'I remain calm when negotiations become tense or confrontational.', 'ar' => 'أبقى هادئاً عندما تصبح المفاوضات متوترة أو مواجِهة.'],
            14 => ['en' => 'I can control my emotions during high-pressure business discussions.', 'ar' => 'أستطيع التحكم في انفعالاتي خلال النقاشات التجارية عالية الضغط.'],
            15 => ['en' => 'I handle rejection or disagreement without becoming defensive.', 'ar' => 'أتعامل مع الرفض أو الاختلاف دون أن أتخذ موقفاً دفاعياً.'],
            16 => ['en' => 'I am comfortable addressing conflicts directly rather than avoiding them.', 'ar' => 'أشعر بالراحة في مواجهة الصراعات مباشرةً بدلاً من تجنبها.'],

            // Decision-Making and Entrepreneurial Negotiation Confidence
            17 => ['en' => 'I feel confident negotiating prices, contracts, or partnerships.', 'ar' => 'أشعر بالثقة عند التفاوض على الأسعار أو العقود أو الشراكات.'],
            18 => ['en' => 'I am willing to negotiate assertively to protect my business interests.', 'ar' => 'لدي الاستعداد للتفاوض بحزم من أجل حماية مصالحي التجارية.'],
            19 => ['en' => 'I can make timely decisions during negotiations without unnecessary delay.', 'ar' => 'أستطيع اتخاذ القرارات في وقتها خلال المفاوضات دون تأخير غير ضروري.'],
            20 => ['en' => 'I believe strong negotiation skills are essential for entrepreneurial success.', 'ar' => 'أعتقد أن مهارات التفاوض القوية ضرورية لتحقيق النجاح الريادي.'],
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

        $this->command->info("Created Negotiation Skills Questionnaire (NSQ-20) with {$test->questions()->count()} questions across 5 dimensions.");
    }
}
