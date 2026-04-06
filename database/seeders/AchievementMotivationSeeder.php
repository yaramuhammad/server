<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class AchievementMotivationSeeder extends Seeder
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
                'en' => 'Achievement Motivation Test',
                'ar' => 'اختبار الدافعية للإنجاز',
            ],
            'description' => [
                'en' => 'A 50-item test measuring achievement motivation across five dimensions: Sense of Responsibility, Pursuit of Excellence, Perseverance, Importance of Time, and Future Planning.',
                'ar' => 'اختبار من 50 بنداً يقيس الدافعية للإنجاز عبر خمسة أبعاد: الشعور بالمسئولية، السعي للتفوق وتحقيق الطموحات، المثابرة، أهمية الزمن، والتخطيط للمستقبل.',
            ],
            'instructions' => [
                'en' => 'Below are statements that describe some people. Read each statement and decide the extent to which it applies to you: 1 = Does not apply at all, 2 = Applies slightly, 3 = Applies moderately, 4 = Applies to a large extent, 5 = Applies completely. There are no right or wrong answers.',
                'ar' => 'فيما يلي مجموعة من البنود التي تصف بعض الأشخاص، والمطلوب منك أن تقرأ كل بند وتقرر مدى انطباقه عليك: 1 = لا ينطبق على البند، 2 = ينطبق بدرجة قليلة، 3 = ينطبق بدرجة متوسطة، 4 = ينطبق بدرجة كبيرة، 5 = ينطبق تماماً. لا توجد إجابات صحيحة أو خاطئة.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 1,
                'max' => 5,
                'labels' => [
                    '1' => ['en' => 'Does not apply at all', 'ar' => 'لا ينطبق على البند'],
                    '2' => ['en' => 'Applies slightly', 'ar' => 'ينطبق بدرجة قليلة'],
                    '3' => ['en' => 'Applies moderately', 'ar' => 'ينطبق بدرجة متوسطة'],
                    '4' => ['en' => 'Applies to a large extent', 'ar' => 'ينطبق بدرجة كبيرة'],
                    '5' => ['en' => 'Applies completely', 'ar' => 'ينطبق تماماً'],
                ],
            ],
            'scoring_type' => 'category',
            'scoring_config' => ['categories' => [
                ['key' => 'responsibility', 'label' => ['en' => 'Sense of Responsibility', 'ar' => 'الشعور بالمسئولية'], 'interpretation' => $interp],
                ['key' => 'excellence', 'label' => ['en' => 'Pursuit of Excellence', 'ar' => 'السعي للتفوق وتحقيق الطموحات'], 'interpretation' => $interp],
                ['key' => 'perseverance', 'label' => ['en' => 'Perseverance', 'ar' => 'المثابرة'], 'interpretation' => $interp],
                ['key' => 'time', 'label' => ['en' => 'Importance of Time', 'ar' => 'أهمية الزمن'], 'interpretation' => $interp],
                ['key' => 'planning', 'label' => ['en' => 'Future Planning', 'ar' => 'التخطيط للمستقبل'], 'interpretation' => $interp],
            ]],
            'randomize_questions' => false,
        ]);

        // Reverse-scored items (by question number 1-based): 7,10,11,12,16,18,19,33,35,36,41,42
        $reverseItems = [7, 10, 11, 12, 16, 18, 19, 33, 35, 36, 41, 42];

        // Category assignments by question number (from PDF page 3):
        // 1) Responsibility: 1,6,11,16,21,26,31,36,41,46
        // 2) Excellence:     2,7,12,17,22,27,32,37,42,47
        // 3) Perseverance:   3,8,13,18,23,28,33,38,43,48
        // 4) Time:           4,9,14,19,24,29,34,39,44,49
        // 5) Planning:       5,10,15,20,25,30,35,40,45,50
        $categoryMap = [];
        foreach ([1,6,11,16,21,26,31,36,41,46] as $n) $categoryMap[$n] = 'responsibility';
        foreach ([2,7,12,17,22,27,32,37,42,47] as $n) $categoryMap[$n] = 'excellence';
        foreach ([3,8,13,18,23,28,33,38,43,48] as $n) $categoryMap[$n] = 'perseverance';
        foreach ([4,9,14,19,24,29,34,39,44,49] as $n) $categoryMap[$n] = 'time';
        foreach ([5,10,15,20,25,30,35,40,45,50] as $n) $categoryMap[$n] = 'planning';

        $questions = [
            // 1
            ['en' => 'I prefer to do the tasks assigned to me to the best of my ability', 'ar' => 'أفضل القيام بما أكلف به من أعمال على أكمل وجه'],
            // 2
            ['en' => 'I feel that excellence is an end in itself', 'ar' => 'أشعر أن التفوق غاية فى حد ذاته'],
            // 3
            ['en' => 'I exert great effort to reach what I want', 'ar' => 'أبذل جهداً كبيراً حتى أصل إلى ما أريد'],
            // 4
            ['en' => 'I make sure to complete duties on time', 'ar' => 'أحرص على تأدية الواجبات فى مواعيدها'],
            // 5
            ['en' => 'I think a lot about the future rather than the past or present', 'ar' => 'أفكر كثيراً فى المستقبل عن الماضى أو الحاضر'],
            // 6
            ['en' => 'I enjoy doing tasks that are challenging and difficult', 'ar' => 'أحب أداء الأعمال التى تتسم بالتحدى والصعوبة'],
            // 7 (reverse)
            ['en' => 'It is not necessary for me to get the highest grades', 'ar' => 'ليس من الضرورى أن أحصل على أعلى التقديرات'],
            // 8
            ['en' => 'Perseverance is essential in my performance of any task', 'ar' => 'المثابرة شئ هام فى أدائى لأى عمل من الأعمال'],
            // 9
            ['en' => 'I determine what I do based on a time schedule', 'ar' => 'أحدد ما أفعله فى ضوء جدول زمنى'],
            // 10 (reverse)
            ['en' => 'I think about past achievements more than the future', 'ar' => 'أفكر فى إنجازات الماضى عن المستقبل'],
            // 11 (reverse)
            ['en' => 'I do not care if I fail in performing a task', 'ar' => 'لا يهمنى أن أفشل فى أداء عمل ما'],
            // 12 (reverse)
            ['en' => 'I reject tasks that require a lot of thinking and research', 'ar' => 'أرفض الأعمال التى تتطلب الكثير من التفكير والبحث'],
            // 13
            ['en' => 'When I start a task, it is necessary to finish it', 'ar' => 'عندما أبدأ فى عمل من الضرورى الانتهاء منه'],
            // 14
            ['en' => 'I make sure to honor the appointments I have with others', 'ar' => 'أحرص على الالتزام بالمواعيد التى ارتبط لها مع الآخرين'],
            // 15
            ['en' => 'I feel that planning for the future is one of the best ways to avoid problems', 'ar' => 'أشعر أن التخطيط للمستقبل من أفضل الطرق لتفادى الوقوع فى المشكلات'],
            // 16 (reverse)
            ['en' => 'I feel that rest is the most important thing in life', 'ar' => 'أشعر أن الراحة هى أهم شئ فى الحياة'],
            // 17
            ['en' => 'I feel happy when I learn new things', 'ar' => 'أشعر بالسعادة عند معرفتى لأشياء جديدة'],
            // 18 (reverse)
            ['en' => 'When I fail at a task, I leave it and move to another', 'ar' => 'عندما أفشل فى عمل ما أتركه وأتجه لغيره'],
            // 19 (reverse)
            ['en' => 'Preoccupations and circumstances often prevent me from keeping my scheduled appointments', 'ar' => 'كثيراً ما تحول المشاغل والظروف بينى وبين مواعيد حددتها'],
            // 20
            ['en' => 'Preparation and advance planning for future tasks is essential', 'ar' => 'من الضرورى الإعداد والتخطيط المسبق لما سنقوم به من أعمال فى المستقبل'],
            // 21
            ['en' => 'I am committed to accuracy in performing any task', 'ar' => 'ألتزم بالدقة فى أدائى لأى عمل من الأعمال'],
            // 22
            ['en' => 'I always try to read and review references', 'ar' => 'أحاول دائماً الإطلاع وقراءة المراجع'],
            // 23
            ['en' => 'I feel happy when I think about solving a problem for long periods', 'ar' => 'أشعر بالسعادة عندما أفكر فى حل مشكلة ما لفترات طويلة'],
            // 24
            ['en' => 'Keeping appointments is sacred to me', 'ar' => 'المحافظة على المواعيد شئ مقدس بالنسبة لى'],
            // 25
            ['en' => 'I fail in performing tasks that are not preceded by good preparation', 'ar' => 'أفشل فى أدائى للأعمال التى لا يسبقها إعداد جيد'],
            // 26
            ['en' => 'I get upset if I do something poorly', 'ar' => 'أتضايق إذا فعلت شئ بطريقة رديئة'],
            // 27
            ['en' => 'I always feel the need to develop my knowledge', 'ar' => 'أشعر دائماً بالحاجة إلى تنمية معارفى'],
            // 28
            ['en' => 'I devote myself to solving difficult problems no matter how long it takes', 'ar' => 'أتفانى فى حل المشكلات الصعبة مهما أخذت من وقت'],
            // 29
            ['en' => 'When I set an appointment, I arrive at the exact specified time', 'ar' => 'عندما أحدد موعد فإنى آجى فى الوقت المحدد بالضبط'],
            // 30
            ['en' => 'I prefer thinking about long-term things', 'ar' => 'أفضل التفكير فى أشياء بعيدة المدى'],
            // 31
            ['en' => 'I give high attention and focus to the tasks I perform', 'ar' => 'أعطى اهتماماً وتركيزاً عالياً للأعمال التى أقوم بها'],
            // 32
            ['en' => 'I continuously strive to improve my performance level', 'ar' => 'أسعى باستمرار لتحسين مستوى أدائى'],
            // 33 (reverse)
            ['en' => 'I feel that continuing to exert effort to solve difficult problems is a waste of time', 'ar' => 'أشعر أن الاستمرار فى بذل الجهد لحل المشكلات الصعبة مضيعة للوقت'],
            // 34
            ['en' => 'I deal with time very seriously', 'ar' => 'أتعامل مع الوقت بجدية تامة'],
            // 35 (reverse)
            ['en' => 'I do not care about the past and its events', 'ar' => 'لا اهتم بالماضى وما يشتمل عليه من أحداث'],
            // 36 (reverse)
            ['en' => 'I prefer tasks that do not require great effort', 'ar' => 'أفضل الأعمال التى لا تحتاج لجهود كبيرة'],
            // 37
            ['en' => 'The need to learn new things is the best way to progress', 'ar' => 'الحاجة لمعرفة الجديد هى أفضل الطرق لتقدمى'],
            // 38
            ['en' => 'Persistence and perseverance are the best ways to solve difficult problems', 'ar' => 'الاستمرار والمثابرة من أنسب الطرق لحل المشكلات الصعبة'],
            // 39
            ['en' => 'I do not allow one task to be done at the expense of another task\'s time', 'ar' => 'لا أسمح لعمل من الأعمال أن يتم على حساب وقت عمل آخر'],
            // 40
            ['en' => 'I am annoyed by people who do not care about their future', 'ar' => 'يزعجنى الأشخاص الذين لا يهتمون بمستقبلهم'],
            // 41 (reverse)
            ['en' => 'Performing duties and tasks is a burden for me', 'ar' => 'أداء الواجبات والأعمال يمثل عبئاً بالنسبة لى'],
            // 42 (reverse)
            ['en' => 'I am satisfied with the information available to me on a topic', 'ar' => 'أكتفى بالمعلومات المتاحة لى حول موضوع ما'],
            // 43
            ['en' => 'I feel satisfied when I continue working for a long time to solve a difficult problem', 'ar' => 'أشعر بالرضا عند مواصلة العمل لفترة طويلة فى حل مشكلة صعبة'],
            // 44
            ['en' => 'It annoys me when someone is late for an appointment with me', 'ar' => 'يزعجنى أن يتأخر أحد عن موعده معى'],
            // 45
            ['en' => 'I feel happy when I plan for the tasks I intend to do', 'ar' => 'أشعر بالسعادة عندما أخطط للأعمال التى أنوى القيام بها'],
            // 46
            ['en' => 'I like spending my free time developing my skills and abilities', 'ar' => 'أحب قضاء وقت الفراغ فى تنمية مهاراتى وقدراتى'],
            // 47
            ['en' => 'I enjoy topics and tasks that require innovative new solutions', 'ar' => 'أستمتع بالموضوعات والأعمال التى تتطلب ابتكار حلول جديدة'],
            // 48
            ['en' => 'I can think seriously for long hours', 'ar' => 'أستطيع التفكير بجدية لساعات طويلة'],
            // 49
            ['en' => 'It is difficult for me to visit someone without a prior appointment', 'ar' => 'من الصعب أن أزور أحد بدون موعد سابق'],
            // 50
            ['en' => 'Planning for the future is one of the best ways to save time and effort', 'ar' => 'التخطيط للمستقبل من أفضل السبل لتوفير الوقت والجهد'],
        ];

        foreach ($questions as $index => $q) {
            $qNum = $index + 1;
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

        $this->command->info("Created Achievement Motivation Test with {$test->questions()->count()} questions (". count($reverseItems) ." reverse-scored) across 5 categories.");
    }
}
