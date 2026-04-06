<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class MultipleIntelligencesSeeder extends Seeder
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
                'en' => 'Multiple Intelligences Scale',
                'ar' => 'مقياس الذكاءات المتعددة',
            ],
            'description' => [
                'en' => 'A personal abilities scale measuring seven types of intelligence: Kinetic, Visio-Spatial, Interpersonal, Musical, Linguistic, Logical-Mathematical, and Intrapersonal.',
                'ar' => 'مقياس القدرات الشخصية يقيس سبعة أنواع من الذكاء: الحركي، البصري المكاني، الاجتماعي، الموسيقي، اللغوي، الرياضي والمنطقي، والشخصي.',
            ],
            'instructions' => [
                'en' => 'Below are statements describing personal abilities. Read each statement carefully and determine how much it applies to you: 1 = Does not apply at all, 5 = Applies completely.',
                'ar' => 'فيما يلي مجموعة من العبارات التي تصف بعض القدرات الشخصية، والمطلوب منك أن تقرأ كل عبارة جيداً وتحدد مدى انطباقها عليك: 1 = لا تنطبق على الإطلاق، 5 = تنطبق تماماً.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 1,
                'max' => 5,
                'labels' => [
                    '1' => ['en' => 'Does not apply at all', 'ar' => 'لا تنطبق'],
                    '2' => ['en' => 'Applies slightly', 'ar' => 'بدرجة قليلة'],
                    '3' => ['en' => 'Applies moderately', 'ar' => 'بدرجة متوسطة'],
                    '4' => ['en' => 'Applies to a large extent', 'ar' => 'بدرجة كبيرة'],
                    '5' => ['en' => 'Applies completely', 'ar' => 'تماماً'],
                ],
            ],
            'scoring_type' => 'category',
            'scoring_config' => ['categories' => [
                ['key' => 'kinetic', 'label' => ['en' => 'Kinetic Intelligence', 'ar' => 'الذكاء والمهارات الحركية'], 'interpretation' => $interp],
                ['key' => 'visio_spatial', 'label' => ['en' => 'Visio-Spatial Intelligence', 'ar' => 'الذكاء البصري المكاني'], 'interpretation' => $interp],
                ['key' => 'interpersonal', 'label' => ['en' => 'Interpersonal Intelligence', 'ar' => 'الذكاء الاجتماعي'], 'interpretation' => $interp],
                ['key' => 'musical', 'label' => ['en' => 'Musical Intelligence', 'ar' => 'الذكاء الموسيقي'], 'interpretation' => $interp],
                ['key' => 'linguistic', 'label' => ['en' => 'Linguistic Intelligence', 'ar' => 'الذكاء اللغوي'], 'interpretation' => $interp],
                ['key' => 'logical', 'label' => ['en' => 'Logical-Mathematical Intelligence', 'ar' => 'الذكاء الرياضي والمنطقي'], 'interpretation' => $interp],
                ['key' => 'intrapersonal', 'label' => ['en' => 'Intrapersonal Intelligence', 'ar' => 'الذكاء الشخصي'], 'interpretation' => $interp],
            ]],
            'randomize_questions' => false,
        ]);

        // Category assignments from page 3:
        // Kinetic: 1,14,21,25,34
        // Visio-Spatial: 2,12,18,24,33
        // Interpersonal: 3,8,13,23,31
        // Musical: 4,10,17,27,30
        // Linguistic: 5,9,19,20,29
        // Logical-Mathematical: 6,11,22,28,35
        // Intrapersonal: 7,15,16,26,32
        $categoryMap = [];
        foreach ([1,14,21,25,34] as $n) $categoryMap[$n] = 'kinetic';
        foreach ([2,12,18,24,33] as $n) $categoryMap[$n] = 'visio_spatial';
        foreach ([3,8,13,23,31] as $n) $categoryMap[$n] = 'interpersonal';
        foreach ([4,10,17,27,30] as $n) $categoryMap[$n] = 'musical';
        foreach ([5,9,19,20,29] as $n) $categoryMap[$n] = 'linguistic';
        foreach ([6,11,22,28,35] as $n) $categoryMap[$n] = 'logical';
        foreach ([7,15,16,26,32] as $n) $categoryMap[$n] = 'intrapersonal';

        $questions = [
            1  => ['en' => 'I am skilled at using tools and equipment', 'ar' => 'أنا ماهر فى استخدام الأدوات والعدد'],
            2  => ['en' => 'I can determine my position and direction accurately', 'ar' => 'أستطيع تحديد موقعى واتجاهى بدقة'],
            3  => ['en' => 'I have a high ability to resolve conflicts among colleagues', 'ar' => 'لدى قدرة عالية على حل الخلافات بين الزملاء'],
            4  => ['en' => 'I can easily recall musical passages', 'ar' => 'أستطيع تذكر المقاطع الموسيقية بسهولة'],
            5  => ['en' => 'I can explain and clarify difficult topics', 'ar' => 'أستطيع شرح وتوضيح الموضوعات الصعبة'],
            6  => ['en' => 'I always do things step by step', 'ar' => 'أقوم دائماً بعمل الأشياء خطوة خطوة'],
            7  => ['en' => 'I know myself well and understand the reasons for my actions', 'ar' => 'أعرف نفسى جيداً وأفهم أسباب تصرفاتى'],
            8  => ['en' => 'I enjoy participating in social activities and events', 'ar' => 'أستمتع بالمشاركة فى الأنشطة والأحداث الاجتماعية'],
            9  => ['en' => 'I learn well through speaking and listening to others', 'ar' => 'أتعلم جيداً من خلال التحدث والاستماع إلى الآخرين'],
            10 => ['en' => 'My mood changes when listening to music', 'ar' => 'يتغير مزاجى عند الاستماع إلى الموسيقى'],
            11 => ['en' => 'I enjoy solving mazes, crossword puzzles, and logical problems', 'ar' => 'أستمتع بحل المتاهات والكلمات المتقاطعة والمشكلات المنطقية'],
            12 => ['en' => 'Maps, charts, and visual presentations are important for me to learn well', 'ar' => 'تعد الخرائط والأشكال البيانية والعروض البصرية مهمة لى كى أتعلم جيداً'],
            13 => ['en' => 'I am very sensitive to the emotions and feelings of others around me', 'ar' => 'أنا حساس للغاية فيما يتعلق بانفعالات ومشاعر الآخرين المحيطين بى'],
            14 => ['en' => 'I learn best through hands-on practice', 'ar' => 'أتعلم بشكل متميز من خلال الممارسة الفعلية'],
            15 => ['en' => 'To learn a skill, I need to know what it will add for me personally', 'ar' => 'لكى أتعلم مهارة معينة يجب أن أعرف ما الذى سوف تضيفه بالنسبة لى'],
            16 => ['en' => 'I like quiet and privacy while working and thinking', 'ar' => 'أحب الهدوء والخصوصية أثناء العمل والتفكير'],
            17 => ['en' => 'I can distinguish instrument sounds in complex musical compositions', 'ar' => 'أستطيع تمييز أصوات الآلات فى المقطوعات الموسيقية المركبة'],
            18 => ['en' => 'I can easily create visual images of scenes I have seen before', 'ar' => 'يمكننى بسهولة عمل صور بصرية للمشاهد التى رأيتها من قبل'],
            19 => ['en' => 'I can easily express verbally what I want', 'ar' => 'لدى سهولة فى التعبير اللفظى عما أريد'],
            20 => ['en' => 'I enjoy writing notes about a topic', 'ar' => 'أستمتع بكتابة بعض الملاحظات عن موضوع ما'],
            21 => ['en' => 'I have a high ability for motor balance', 'ar' => 'أتمتع بقدرة عالية على التوازن الحركى'],
            22 => ['en' => 'I can identify patterns and relationships between things I observe', 'ar' => 'يمكننى التعرف على الأنماط والعلاقات بين الأشياء التى ألاحظها'],
            23 => ['en' => 'I cooperate with others in the work team and benefit from their ideas and experiences', 'ar' => 'أتعاون مع الآخرين فى فريق العمل وأستفيد من أفكارهم وخبراتهم'],
            24 => ['en' => 'I have high observational accuracy, seeing what others do not see', 'ar' => 'لدى دقة ملاحظة عالية بحيث أرى ما لا يراه البعض'],
            25 => ['en' => 'I move quickly and in many directions', 'ar' => 'أتحرك بسرعة وفى اتجاهات كثيرة'],
            26 => ['en' => 'I enjoy working or learning away from others', 'ar' => 'أستمتع بالعمل أو التعلم بعيداً عن الآخرين'],
            27 => ['en' => 'I enjoy composing some musical passages', 'ar' => 'أستمتع بتلحين بعض المقطوعات الموسيقية'],
            28 => ['en' => 'I deal easily with numbers and arithmetic problems', 'ar' => 'أتعامل بسهولة مع الأرقام والمشكلات الحسابية'],
            29 => ['en' => 'I enjoy word games (crosswords and anagrams)', 'ar' => 'أستمتع بألعاب الكلمات (الكلمات المتقاطعة والكلمات المتجانسة)'],
            30 => ['en' => 'I can skillfully play a musical instrument', 'ar' => 'أعزف بمهارة على إحدى الآلات الموسيقية'],
            31 => ['en' => 'People often come to me seeking advice and opinions', 'ar' => 'يلجأ لى الناس طلباً للمشورة والرأى'],
            32 => ['en' => 'I like spending time contemplating, thinking, and searching for answers to important life questions', 'ar' => 'أحب قضاء وقت فى التأمل والتفكير والبحث عن إجابات حول الأسئلة الهامة فى الحياة'],
            33 => ['en' => 'I can easily find my way in an area I do not know well', 'ar' => 'يمكننى بسهولة إيجاد طريقى فى منطقة لا أعرفها جيداً'],
            34 => ['en' => 'I actively participate in physical sports', 'ar' => 'أشارك بفعالية فى إحدى الرياضات البدنية'],
            35 => ['en' => 'I feel comfortable dealing with things that can be measured or analyzed logically', 'ar' => 'أشعر بالراحة فى التعامل مع أشياء يمكن قياسها أو تحليلها بشكل منطقى'],
        ];

        foreach ($questions as $sortOrder => $q) {
            Question::create([
                'test_id' => $test->id,
                'text' => ['en' => $q['en'], 'ar' => $q['ar']],
                'sort_order' => $sortOrder,
                'is_reverse_scored' => false,
                'is_required' => true,
                'category_key' => $categoryMap[$sortOrder],
                'weight' => 1.00,
            ]);
        }

        $this->command->info("Created Multiple Intelligences Scale with {$test->questions()->count()} questions across 7 categories.");
    }
}
