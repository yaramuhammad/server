<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class BigFivePersonalitySeeder extends Seeder
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
                'en' => 'Big Five Personality Test',
                'ar' => 'مقياس العوامل الخمسة للشخصية',
            ],
            'description' => [
                'en' => 'A 40-item personality test measuring five key personality dimensions: Extraversion, Neuroticism, Openness to Experience, Agreeableness, and Conscientiousness.',
                'ar' => 'اختبار شخصية من 40 بنداً يقيس خمسة أبعاد رئيسية للشخصية: الانبساطية، العصابية، الانفتاح على الخبرة، الطيبة والمودة، ويقظة الضمير.',
            ],
            'instructions' => [
                'en' => 'Below are statements describing personality traits. Read each statement and indicate how much you agree with it: 1 = Strongly Agree, 2 = Agree, 3 = Neutral, 4 = Disagree, 5 = Strongly Disagree.',
                'ar' => 'فيما يلي عدد من العبارات التي تصف بعض سمات الشخصية. اقرأ كل عبارة جيداً وبيّن إلى أي مدى توافق على كل منها: 1 = موافق تماماً، 2 = موافق، 3 = بين بين، 4 = أرفض، 5 = أرفض تماماً.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 1,
                'max' => 5,
                'labels' => [
                    '1' => ['en' => 'Strongly Agree', 'ar' => 'موافق تماماً'],
                    '2' => ['en' => 'Agree', 'ar' => 'موافق'],
                    '3' => ['en' => 'Neutral', 'ar' => 'بين بين'],
                    '4' => ['en' => 'Disagree', 'ar' => 'أرفض'],
                    '5' => ['en' => 'Strongly Disagree', 'ar' => 'أرفض تماماً'],
                ],
            ],
            'scoring_type' => 'category',
            'scoring_config' => ['categories' => [
                ['key' => 'extraversion', 'label' => ['en' => 'Extraversion', 'ar' => 'الانبساطية'], 'interpretation' => $interp],
                ['key' => 'neuroticism', 'label' => ['en' => 'Neuroticism', 'ar' => 'العصابية'], 'interpretation' => $interp],
                ['key' => 'openness', 'label' => ['en' => 'Openness to Experience', 'ar' => 'الانفتاح على الخبرة'], 'interpretation' => $interp],
                ['key' => 'agreeableness', 'label' => ['en' => 'Agreeableness', 'ar' => 'الطيبة والمودة'], 'interpretation' => $interp],
                ['key' => 'conscientiousness', 'label' => ['en' => 'Conscientiousness', 'ar' => 'يقظة الضمير'], 'interpretation' => $interp],
            ]],
            'randomize_questions' => false,
            'chart_type' => 'pie',
        ]);

        // Category assignments from page 3:
        // Extraversion: 1,8,11,16,23,27,31,36
        // Neuroticism: 4,9,14,19,24,29,34,38
        // Openness: 5,10,15,20,25,30,35,40
        // Agreeableness: 2,6,12,17,21,26,33,39
        // Conscientiousness: 3,7,13,18,22,28,32,37
        $categoryMap = [];
        foreach ([1,8,11,16,23,27,31,36] as $n) $categoryMap[$n] = 'extraversion';
        foreach ([4,9,14,19,24,29,34,38] as $n) $categoryMap[$n] = 'neuroticism';
        foreach ([5,10,15,20,25,30,35,40] as $n) $categoryMap[$n] = 'openness';
        foreach ([2,6,12,17,21,26,33,39] as $n) $categoryMap[$n] = 'agreeableness';
        foreach ([3,7,13,18,22,28,32,37] as $n) $categoryMap[$n] = 'conscientiousness';

        // Reverse-scored items from page 3:
        $reverseItems = [2, 6, 9, 12, 18, 23, 27, 31, 34, 35, 39];

        $questions = [
            1  => ['en' => 'Talkative', 'ar' => 'ثرثاراً'],
            2  => ['en' => 'Tend to find fault with others', 'ar' => 'أميل لاكتشاف عيوب الآخرين'],
            3  => ['en' => 'Do my job to the fullest', 'ar' => 'أقوم بوظيفتى على الوجه الأكمل'],
            4  => ['en' => 'Depressed', 'ar' => 'مكتئب'],
            5  => ['en' => 'Creative and think of new distinctive ideas', 'ar' => 'مبدعاً وأفكر فى أفكار جديدة متميزة'],
            6  => ['en' => 'Reserved', 'ar' => 'متحفظ'],
            7  => ['en' => 'Cooperative and unselfish with others', 'ar' => 'متعاون وغير أنانى مع الآخرين'],
            8  => ['en' => 'Somewhat reckless', 'ar' => 'متهور نوعاً ما'],
            9  => ['en' => 'Calm, composed, and handle stress well', 'ar' => 'هادئ ورزين وأتعامل مع الضغوط بشكل جيد'],
            10 => ['en' => 'Curious and love to explore', 'ar' => 'شغوف وأحب الاستطلاع'],
            11 => ['en' => 'Full of energy and vitality', 'ar' => 'ممتلئ بالطاقة والحيوية'],
            12 => ['en' => 'Start arguments and disputes with others', 'ar' => 'أبدأ الجدال والنزاع مع الآخرين'],
            13 => ['en' => 'Dependable', 'ar' => 'يمكن الاعتماد علي'],
            14 => ['en' => 'Can get tense easily', 'ar' => 'يمكن أن أتوتر بسهولة'],
            15 => ['en' => 'Genius and distinguished thinker', 'ar' => 'عبقرى ومفكر متميز'],
            16 => ['en' => 'Generate energy and enthusiasm in those around me', 'ar' => 'أستثير الطاقة والحماس فيمن حولى'],
            17 => ['en' => 'Forgiving by nature', 'ar' => 'ذو طبيعة متسامحة'],
            18 => ['en' => 'Not organized enough', 'ar' => 'غير منظم بدرجة كافية'],
            19 => ['en' => 'Worry a lot', 'ar' => 'أقلق كثيراً'],
            20 => ['en' => 'Have a rich imagination', 'ar' => 'لدى مخيلة خصبة'],
            21 => ['en' => 'Tend to be quiet', 'ar' => 'أميل للهدوء'],
            22 => ['en' => 'Generally trust others', 'ar' => 'أثق فى الآخرين بشكل عام'],
            23 => ['en' => 'Relatively lazy', 'ar' => 'كسول نسبياً'],
            24 => ['en' => 'Emotionally stable and do not get angry easily', 'ar' => 'منضبط انفعالياً ولا أغضب بسهولة'],
            25 => ['en' => 'Innovative', 'ar' => 'مبتكر'],
            26 => ['en' => 'Have a strong personality', 'ar' => 'لدى شخصية قوية'],
            27 => ['en' => 'Can be introverted and isolated', 'ar' => 'يمكن أن أكون منطوى ومنعزل'],
            28 => ['en' => 'Persevere until tasks are completed', 'ar' => 'أثابر حتى انتهاء المهام'],
            29 => ['en' => 'Sometimes moody', 'ar' => 'متقلب المزاج أحياناً'],
            30 => ['en' => 'Lover of art and beauty', 'ar' => 'عاشق للفن والجمال'],
            31 => ['en' => 'Relatively shy', 'ar' => 'خجول نسبياً'],
            32 => ['en' => 'Do things with a high degree of efficiency', 'ar' => 'أقوم بالأشياء بدرجة عالية من الكفاءة'],
            33 => ['en' => 'Kind and compassionate with everyone', 'ar' => 'عطوف وحنون مع كل الناس'],
            34 => ['en' => 'Stay calm in stressful situations', 'ar' => 'أبقى هادئ فى المواقف الضاغطة'],
            35 => ['en' => 'Prefer routine work', 'ar' => 'أفضل الأعمال الروتينية'],
            36 => ['en' => 'Cheerful and sociable', 'ar' => 'مرح واجتماعى'],
            37 => ['en' => 'Make plans and execute them precisely', 'ar' => 'أضع خطط وأنفذها بدقة'],
            38 => ['en' => 'Get nervous easily', 'ar' => 'أتنرفز بسهولة'],
            39 => ['en' => 'Act rudely with some people', 'ar' => 'أتعامل بوقاحة مع بعض الناس'],
            40 => ['en' => 'Love contemplation and playing with ideas', 'ar' => 'أحب التأمل واللعب بالأفكار'],
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

        $this->command->info("Created Big Five Personality Test with {$test->questions()->count()} questions (" . count($reverseItems) . " reverse-scored) across 5 categories.");
    }
}
