<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class PsychologicalHardinessSeeder extends Seeder
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
                'en' => 'Psychological Hardiness Scale',
                'ar' => 'مقياس الصلابة النفسية',
            ],
            'description' => [
                'en' => 'A 47-item scale measuring psychological hardiness across three dimensions: Commitment, Control, and Challenge.',
                'ar' => 'مقياس من 47 بنداً يقيس الصلابة النفسية عبر ثلاثة أبعاد: الالتزام، التحكم، والتحدي.',
            ],
            'instructions' => [
                'en' => 'Read each statement and determine the extent to which it applies to you: 1 = Strongly Disagree, 2 = Disagree, 3 = Neutral, 4 = Agree, 5 = Strongly Agree.',
                'ar' => 'اقرأ كل عبارة وحدد مدى انطباقها عليك: 1 = لا أوافق تماماً، 2 = لا أوافق، 3 = بين بين، 4 = أوافق، 5 = أوافق تماماً.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 1,
                'max' => 5,
                'labels' => [
                    '1' => ['en' => 'Strongly Disagree', 'ar' => 'لا أوافق تماماً'],
                    '2' => ['en' => 'Disagree', 'ar' => 'لا أوافق'],
                    '3' => ['en' => 'Neutral', 'ar' => 'بين بين'],
                    '4' => ['en' => 'Agree', 'ar' => 'أوافق'],
                    '5' => ['en' => 'Strongly Agree', 'ar' => 'أوافق تماماً'],
                ],
            ],
            'scoring_type' => 'category',
            'scoring_config' => ['categories' => [
                ['key' => 'commitment', 'label' => ['en' => 'Commitment', 'ar' => 'الالتزام'], 'interpretation' => $interp],
                ['key' => 'control', 'label' => ['en' => 'Control', 'ar' => 'التحكم'], 'interpretation' => $interp],
                ['key' => 'challenge', 'label' => ['en' => 'Challenge', 'ar' => 'التحدي'], 'interpretation' => $interp],
            ]],
            'randomize_questions' => false,
            'chart_type' => 'doughnut',
        ]);

        // Category assignments from last pages:
        // Commitment: 1,4,7,10,13,16,19,22,25,28,31,34,37,40,43,46
        // Control: 2,5,8,11,14,17,20,23,26,29,32,35,38,41,44
        // Challenge: 3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,47
        $categoryMap = [];
        foreach ([1,4,7,10,13,16,19,22,25,28,31,34,37,40,43,46] as $n) $categoryMap[$n] = 'commitment';
        foreach ([2,5,8,11,14,17,20,23,26,29,32,35,38,41,44] as $n) $categoryMap[$n] = 'control';
        foreach ([3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,47] as $n) $categoryMap[$n] = 'challenge';

        // Reverse-scored items: where the PDF shows scale as 5,4,3,2,1 (left to right = disagree to agree)
        // i.e., answering "Strongly Agree" (rightmost) gives 1 instead of 5
        $reverseItems = [7, 11, 16, 21, 23, 25, 28, 32, 35, 36, 37, 38, 42, 46, 47];

        $questions = [
            1  => ['en' => 'No matter what the obstacles, I can achieve my goals', 'ar' => 'مهما كانت العقبات فإنني أستطيع تحقيق أهدافي'],
            2  => ['en' => 'I make my own decisions and they are not dictated to me from an external source', 'ar' => 'اتخذ قراراتي بنفسي ولا تُملى علي من مصدر خارجي'],
            3  => ['en' => 'I believe that the joy and excitement of life lie in one\'s ability to face its challenges', 'ar' => 'اعتقد أن متعة الحياة وإثارتها تكمن في قدرة الفرد على مواجهة تحدياتها'],
            4  => ['en' => 'The value of life lies in one\'s loyalty to certain principles and values', 'ar' => 'قيمة الحياة تكمن في ولاء الفرد لبعض المبادئ والقيم'],
            5  => ['en' => 'When I make my future plans, I am usually confident in my ability to execute them', 'ar' => 'عندما أضع خططي المستقبلية غالباً ما أكون متأكداً من قدراتى على تنفيذها'],
            6  => ['en' => 'I confront problems head-on to solve them', 'ar' => 'أقتحم المشكلات لحلها'],
            7  => ['en' => 'Most of my time is wasted on meaningless activities', 'ar' => 'معظم أوقات حياتي تضيع في أنشطة لا معنى لها'],
            8  => ['en' => 'My success in my affairs (work, study, etc.) depends on my effort, not luck or chance', 'ar' => 'نجاحي في أموري (عمل - دراسة ..إلخ) يعتمد على مجهودي وليس على الحظ أو الصدفة'],
            9  => ['en' => 'I have curiosity and a desire to know what I don\'t know', 'ar' => 'لدي حب استطلاع ورغبة في معرفة ما لا أعرفه'],
            10 => ['en' => 'I believe my life has a purpose and meaning worth living for', 'ar' => 'أعتقد أن لحياتي هدفاً ومعنى أعيش من أجله'],
            11 => ['en' => 'Life is about opportunities, not work and struggle', 'ar' => 'الحياة فرص وليست عمل وكفاح'],
            12 => ['en' => 'I believe that an exciting life is one that involves problems I can face', 'ar' => 'أعتقد أن الحياة المثيرة هي التي تنطوي على مشكلات أستطيع أن أواجهها'],
            13 => ['en' => 'I have certain values and principles that I adhere to and maintain', 'ar' => 'لدي قيم ومبادئ معينة ألتزم بها وأحافظ عليها'],
            14 => ['en' => 'I believe that failure is due to reasons within the person themselves', 'ar' => 'اعتقد أن الفشل يعود إلى أسباب تكمن في الشخص نفسه'],
            15 => ['en' => 'I have the ability to persevere until I finish solving any problem I face', 'ar' => 'لدي قدرة على المثابرة حتى أنتهي من حل أي مشكلة تواجهني'],
            16 => ['en' => 'I have no goals worth holding onto or defending', 'ar' => 'لا يوجد لدي من الأهداف ما يدعو للتمسك بها أو الدفاع عنها'],
            17 => ['en' => 'I believe that everything that happens to me is usually the result of my planning', 'ar' => 'أعتقد أن كل ما يحدث لي غالباً هو نتيجة تخطيطي'],
            18 => ['en' => 'Problems mobilize my strengths and ability to challenge', 'ar' => 'المشكلات تستنفر قواي وقدراتي على التحدي'],
            19 => ['en' => 'I do not hesitate to participate in any activity that serves my community', 'ar' => 'لا أتردد في المشاركة في أي نشاط يخدم المجتمع الذي أعيش فيه'],
            20 => ['en' => 'There is really no such thing as luck', 'ar' => 'لا يوجد في الواقع شيء اسمه الحظ'],
            21 => ['en' => 'I feel fear and threat about what may happen in my life', 'ar' => 'أشعر بالخوف والتهديد لما قد يطرأ على حياتي من ظروف وأحداث'],
            22 => ['en' => 'I take the initiative to stand by others when they face any problem', 'ar' => 'أبادر بالوقوف بجانب الآخرين عند مواجهتهم لأي مشكلة'],
            23 => ['en' => 'I believe that chance and luck play an important role in my life', 'ar' => 'أعتقد أن الصدفة والحظ يلعبان دوراً هاماً في حياتي'],
            24 => ['en' => 'When I solve a problem, I find pleasure in moving on to solve another', 'ar' => 'عندما أحل مشكلة أجد متعة في التحرك لحل مشكلة أخرى'],
            25 => ['en' => 'I believe that "staying away from people is a blessing"', 'ar' => 'أعتقد أن "البعد عن الناس غنيمة"'],
            26 => ['en' => 'I can control the course of my life affairs', 'ar' => 'أستطيع التحكم في مجرى أمور حياتي'],
            27 => ['en' => 'I believe that facing problems is a test of my endurance and perseverance', 'ar' => 'أعتقد أن مواجهة المشكلات اختبار لقوة تحملي وقدرتي على المثابرة'],
            28 => ['en' => 'My preoccupation with myself leaves me no opportunity to think about anything else', 'ar' => 'اهتمامي بنفسي لا يترك لي فرصة للتفكير في أي شيء آخر'],
            29 => ['en' => 'I believe that bad luck is due to poor planning', 'ar' => 'أعتقد أن سوء الحظ يعود إلى سوء التخطيط'],
            30 => ['en' => 'I have a love of adventure and a desire to explore my surroundings', 'ar' => 'لدي حب المغامرة والرغبة في استكشاف ما يحيط بي'],
            31 => ['en' => 'I take the initiative to do anything I believe serves my family or community', 'ar' => 'أبادر بعمل أي شيء أعتقد أنه يخدم أسرتي أو مجتمعي'],
            32 => ['en' => 'I believe my influence on events that happen to me is weak', 'ar' => 'أعتقد أن تأثيري ضعيف على الأحداث التي تقع لي'],
            33 => ['en' => 'I take the initiative to face problems because I trust my ability to solve them', 'ar' => 'أبادر في مواجهة المشكلات لأنني أثق في قدرتي على حلها'],
            34 => ['en' => 'I care greatly about issues and events happening around me', 'ar' => 'أهتم كثيراً بما يجري من حولي من قضايا وأحداث'],
            35 => ['en' => 'I believe that people\'s lives are affected by external forces beyond their control', 'ar' => 'أعتقد أن حياة الأفراد تتأثر بقوى خارجية لا سيطرة لهم عليها'],
            36 => ['en' => 'A stable and calm life is the enjoyable life for me', 'ar' => 'الحياة الثابتة والساكنة هي الحياة الممتعة بالنسبة لي'],
            37 => ['en' => 'Life with all it contains is not worth living', 'ar' => 'الحياة بكل ما فيها لا تستحق أن نحياها'],
            38 => ['en' => 'I believe in the saying "a bit of luck is better than a lot of skill"', 'ar' => 'أؤمن بالمثل الشعبي "قيراط حظ ولا فدان شطارة"'],
            39 => ['en' => 'I believe that a life without change is a boring and routine life', 'ar' => 'أعتقد أن الحياة التي لا تنطوي على تغيير هي حياة مملة وروتينية'],
            40 => ['en' => 'I feel responsible toward others and take the initiative to help them', 'ar' => 'أشعر بالمسئولية تجاه الآخرين وأبادر بمساعدتهم'],
            41 => ['en' => 'I believe I have a strong influence on events happening around me', 'ar' => 'أعتقد أن لي تأثير قوي على ما يجري حولي من أحداث'],
            42 => ['en' => 'I am apprehensive about life changes as every change may involve a threat to me and my life', 'ar' => 'أتوجس من تغييرات الحياة فكل تغيير قد ينطوي على تهديد لي ولحياتي'],
            43 => ['en' => 'I care about national issues and participate in them whenever possible', 'ar' => 'أهتم بقضايا الوطن وأشارك فيها كلما أمكن'],
            44 => ['en' => 'I plan my life affairs and do not leave them to chance, luck, or external circumstances', 'ar' => 'أخطط لأمور حياتي ولا أتركها تحت رحمة الصدفة والحظ والظروف الخارجية'],
            45 => ['en' => 'Change is the nature of life and what matters is the ability to face it successfully', 'ar' => 'التغير هو سنة الحياة والمهم هو القدرة على مواجهته بنجاح'],
            46 => ['en' => 'I change my values and principles if circumstances require it', 'ar' => 'أغير قيمي ومبادئي إذا دعت الظروف لذلك'],
            47 => ['en' => 'I feel afraid of facing problems even before they occur', 'ar' => 'أشعر بالخوف من مواجهة المشكلات حتى قبل أن تحدث'],
        ];

        foreach ($questions as $qNum => $q) {
            Question::create([
                'test_id' => $test->id,
                'text' => ['en' => $q['en'], 'ar' => $q['ar']],
                'sort_order' => $qNum,
                'is_reverse_scored' => in_array($qNum, $reverseItems),
                'is_required' => true,
                'category_key' => $categoryMap[$qNum],
                'weight' => 1.00,
            ]);
        }

        $this->command->info("Created Psychological Hardiness Scale with {$test->questions()->count()} questions (" . count($reverseItems) . " reverse-scored) across 3 categories.");
    }
}
