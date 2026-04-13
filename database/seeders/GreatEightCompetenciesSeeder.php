<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class GreatEightCompetenciesSeeder extends Seeder
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
                'en' => 'The Great Eight Competencies Scale',
                'ar' => 'مقياس الجدارات الثمانية الكبرى',
            ],
            'description' => [
                'en' => 'An integrative competency model by Dave Bartram measuring 8 key behavioral dimensions related to effective job performance across professions.',
                'ar' => 'نموذج تكاملي للجدارات المهنية من إعداد ديف بارترام يقيس 8 أبعاد سلوكية رئيسية مرتبطة بالأداء الوظيفي الفعّال.',
            ],
            'instructions' => [
                'en' => 'Below are statements describing how you behave in work situations. Indicate how much each statement applies to you honestly: 1 = Does not apply at all, 2 = Applies to a limited extent, 3 = Applies moderately, 4 = Applies to a large extent, 5 = Applies completely.',
                'ar' => 'فيما يلي مجموعة من العبارات التي تصف طريقة تصرفك في مواقف العمل. حدد مدى انطباق كل عبارة عليك بمصداقية: 1 = لا تنطبق مطلقًا، 2 = تنطبق بدرجة محدودة، 3 = تنطبق بدرجة متوسطة، 4 = تنطبق بدرجة كبيرة، 5 = تنطبق تمامًا.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 1,
                'max' => 5,
                'labels' => [
                    '1' => ['en' => 'Does not apply at all', 'ar' => 'لا تنطبق مطلقًا'],
                    '2' => ['en' => 'Applies to a limited extent', 'ar' => 'تنطبق بدرجة محدودة'],
                    '3' => ['en' => 'Applies moderately', 'ar' => 'تنطبق بدرجة متوسطة'],
                    '4' => ['en' => 'Applies to a large extent', 'ar' => 'تنطبق بدرجة كبيرة'],
                    '5' => ['en' => 'Applies completely', 'ar' => 'تنطبق تمامًا'],
                ],
            ],
            'scoring_type' => 'category',
            'scoring_config' => ['categories' => [
                ['key' => 'leading', 'label' => ['en' => 'Leading & Deciding', 'ar' => 'القيادة واتخاذ القرار'], 'interpretation' => $interp],
                ['key' => 'supporting', 'label' => ['en' => 'Supporting & Cooperating', 'ar' => 'الدعم والتعاون'], 'interpretation' => $interp],
                ['key' => 'interacting', 'label' => ['en' => 'Interacting & Presenting', 'ar' => 'التفاعل والتواصل'], 'interpretation' => $interp],
                ['key' => 'analysing', 'label' => ['en' => 'Analysing & Interpreting', 'ar' => 'التحليل والتفسير'], 'interpretation' => $interp],
                ['key' => 'creating', 'label' => ['en' => 'Creating & Conceptualizing', 'ar' => 'الإبداع والتصور'], 'interpretation' => $interp],
                ['key' => 'organizing', 'label' => ['en' => 'Organizing & Executing', 'ar' => 'التنظيم والتنفيذ'], 'interpretation' => $interp],
                ['key' => 'adapting', 'label' => ['en' => 'Adapting & Coping', 'ar' => 'التكيف والتعامل مع الضغوط'], 'interpretation' => $interp],
                ['key' => 'enterprising', 'label' => ['en' => 'Enterprising & Performing', 'ar' => 'المبادرة والإنجاز'], 'interpretation' => $interp],
            ]],
            'randomize_questions' => false,
            'chart_type' => 'line',
        ]);

        $reverseItems = [5, 6, 12, 14, 20, 22, 24, 28, 30, 36, 38, 44, 46, 52, 54, 60, 62];

        $questions = [
            // 1. القيادة واتخاذ القرار (Leading & Deciding) Q1-8
            ['en' => 'I make important decisions even with insufficient information if delay would affect work', 'ar' => 'أتخذ قرارًا مهمًا حتى مع نقص المعلومات إذا كان التأخير سيؤثر على العمل', 'cat' => 'leading'],
            ['en' => 'I accept the consequences of my decisions even when they are not in my favor', 'ar' => 'أتحمل نتائج قراراتي حتى عندما لا تكون في صالحي', 'cat' => 'leading'],
            ['en' => 'I intervene to resolve conflicts when they disrupt workflow', 'ar' => 'أتدخل لحسم الخلافات عندما تعطل سير العمل', 'cat' => 'leading'],
            ['en' => 'I direct others toward the goal even if it requires correcting their mistakes', 'ar' => 'أوجه الآخرين نحو الهدف حتى لو تطلب ذلك تصحيح أخطائهم', 'cat' => 'leading'],
            ['en' => 'I postpone some decisions to avoid bearing their consequences', 'ar' => 'أؤجل بعض القرارات لتجنب تحمل نتائجها', 'cat' => 'leading'],
            ['en' => 'I find it difficult to impose a decision that is not supported by everyone', 'ar' => 'أجد صعوبة في فرض قرار لا يحظى بتأييد الجميع', 'cat' => 'leading'],
            ['en' => 'I handle the pressure of decision-making in critical situations', 'ar' => 'أتحمل ضغط اتخاذ القرار في المواقف الحرجة', 'cat' => 'leading'],
            ['en' => 'I review my previous decisions to avoid repeating mistakes', 'ar' => 'أراجع قراراتي السابقة لتجنب تكرار الأخطاء', 'cat' => 'leading'],

            // 2. الدعم والتعاون (Supporting & Cooperating) Q9-16
            ['en' => 'I help my colleagues even when I have heavy workload', 'ar' => 'أساعد زملائي حتى عندما يكون لدي ضغط عمل كبير', 'cat' => 'supporting'],
            ['en' => 'I share information with others even without being asked', 'ar' => 'أشارك المعلومات مع الآخرين حتى دون طلب مباشر', 'cat' => 'supporting'],
            ['en' => 'I listen to colleagues\' problems even if I cannot solve them', 'ar' => 'أستمع لمشكلات الزمالء حتى لو لم أستطع حلها', 'cat' => 'supporting'],
            ['en' => 'I prefer focusing on my tasks without concern for others when work pressure increases', 'ar' => 'أفضّل التركيز على مهامي دون اهتمام بالآخرين عندما يزيد ضغط العمل', 'cat' => 'supporting'],
            ['en' => 'I handle differences of opinion without escalating conflict', 'ar' => 'أتعامل مع اختلاف الآراء دون تصعيد الخلاف', 'cat' => 'supporting'],
            ['en' => 'I avoid interfering in others\' problems to reduce burden', 'ar' => 'أتجنب التدخل في مشكلات الآخرين لتقليل الأعباء', 'cat' => 'supporting'],
            ['en' => 'I contribute to team success even without direct recognition', 'ar' => 'أساهم في نجاح الفريق حتى دون تقدير مباشر', 'cat' => 'supporting'],
            ['en' => 'I consider others\' feelings when providing criticism', 'ar' => 'أراعي مشاعر الآخرين عند تقديم النقد', 'cat' => 'supporting'],

            // 3. التفاعل والتواصل (Interacting & Presenting) Q17-24
            ['en' => 'I clarify my ideas even when they are complex', 'ar' => 'أوضح أفكاري حتى عندما تكون معقدة', 'cat' => 'interacting'],
            ['en' => 'I listen to others before responding even when disagreeing', 'ar' => 'أستمع للآخرين قبل الرد حتى عند الاختلاف في الرأي', 'cat' => 'interacting'],
            ['en' => 'I present my ideas in a way that suits the listener\'s background', 'ar' => 'أقدم أفكاري بطريقة تناسب خلفية المستمع', 'cat' => 'interacting'],
            ['en' => 'I find it difficult to express my opinion in formal situations', 'ar' => 'أجد صعوبة في التعبير عن رأيي في المواقف الرسمية', 'cat' => 'interacting'],
            ['en' => 'I summarize important information without losing meaning', 'ar' => 'أختصر المعلومات المهمة دون الإخلال بالمعنى', 'cat' => 'interacting'],
            ['en' => 'I hold on to my opinion when I believe my idea is clearer', 'ar' => 'أتمسك برأيي عندما أعتقد أن فكرتي أوضح', 'cat' => 'interacting'],
            ['en' => 'I adjust my style of presenting ideas according to the situation', 'ar' => 'أعدل أسلوبي في عرض الأفكار حسب الموقف', 'cat' => 'interacting'],
            ['en' => 'I avoid speaking in group discussions to avoid making mistakes', 'ar' => 'أتجنب الحديث في النقاشات الجماعية لتفادي الخطأ', 'cat' => 'interacting'],

            // 4. التحليل والتفسير (Analysing & Interpreting) Q25-32
            ['en' => 'I review data before making decisions even with tight deadlines', 'ar' => 'أراجع البيانات قبل اتخاذ القرار حتى مع ضيق الوقت', 'cat' => 'analysing'],
            ['en' => 'I connect information from different sources', 'ar' => 'أربط بين معلومات من مصادر مختلفة', 'cat' => 'analysing'],
            ['en' => 'I use evidence when justifying my opinions', 'ar' => 'أستخدم الأدلة عند تبرير آرائي', 'cat' => 'analysing'],
            ['en' => 'I sometimes make quick decisions based on past experience without considering new developments', 'ar' => 'أتخذ أحيانًا قرارات بسرعة معتمدًا على خبرتي السابقة دون النظر للمستجدات الطارئة', 'cat' => 'analysing'],
            ['en' => 'I verify the accuracy of information before using it', 'ar' => 'أتحقق من دقة المعلومات قبل استخدامها', 'cat' => 'analysing'],
            ['en' => 'I ignore some details to speed up decision-making', 'ar' => 'أتجاهل بعض التفاصيل لتسريع اتخاذ القرار', 'cat' => 'analysing'],
            ['en' => 'I interpret results based on clear data', 'ar' => 'أفسر النتائج بناءً على معطيات واضحة', 'cat' => 'analysing'],
            ['en' => 'I change my conclusion if new information appears', 'ar' => 'أغير استنتاجي إذا ظهرت معلومات جديدة', 'cat' => 'analysing'],

            // 5. الإبداع والتصور (Creating & Conceptualizing) Q33-40
            ['en' => 'I propose new ideas even if they haven\'t been tried before', 'ar' => 'أطرح أفكارًا جديدة حتى لو لم تُجرب من قبل', 'cat' => 'creating'],
            ['en' => 'I search for unconventional alternatives to solutions', 'ar' => 'أبحث عن بدائل غير تقليدية للحلول', 'cat' => 'creating'],
            ['en' => 'I combine different ideas to produce a new solution', 'ar' => 'أدمج أفكارًا مختلفة لإنتاج حل جديد', 'cat' => 'creating'],
            ['en' => 'I prefer using proven methods rather than trying new ideas', 'ar' => 'أفضّل استخدام طرق مجربة بدلًا من تجربة أفكار جديدة', 'cat' => 'creating'],
            ['en' => 'I envision future outcomes before implementing an idea', 'ar' => 'أتصور النتائج المستقبلية قبل تنفيذ الفكرة', 'cat' => 'creating'],
            ['en' => 'I hesitate to propose unfamiliar ideas in front of others', 'ar' => 'أتردد في طرح أفكار غير مألوفة أمام الآخرين', 'cat' => 'creating'],
            ['en' => 'I develop previously proposed ideas even if it takes extra time', 'ar' => 'أقوم بتطوير الأفكار المطروحة من قبل حتى لو تأخرت بعض الوقت', 'cat' => 'creating'],
            ['en' => 'I look for opportunities to improve work in innovative ways', 'ar' => 'أبحث عن فرص لتحسين العمل بطرق مبتكرة', 'cat' => 'creating'],

            // 6. التنظيم والتنفيذ (Organizing & Executing) Q41-48
            ['en' => 'I stick to the work plan even when distractions appear', 'ar' => 'ألتزم بخطة العمل حتى مع ظهور مشتتات', 'cat' => 'organizing'],
            ['en' => 'I set my priorities when tasks overlap', 'ar' => 'أحدد أولوياتي عند تداخل المهام', 'cat' => 'organizing'],
            ['en' => 'I follow up on task execution until completion', 'ar' => 'أتابع تنفيذ المهام حتى الانتهاء منها', 'cat' => 'organizing'],
            ['en' => 'I postpone some important tasks due to work pressure', 'ar' => 'أؤجل بعض المهام رغم أهميتها بسبب ضغط العمل', 'cat' => 'organizing'],
            ['en' => 'I adjust my plan if new variables emerge', 'ar' => 'أعدل خطتي إذا ظهرت متغيرات جديدة', 'cat' => 'organizing'],
            ['en' => 'I sometimes work without a clear plan when time is tight', 'ar' => 'أعمل أحيانًا بدون خطة واضحة عندما يكون الوقت ضيقًا', 'cat' => 'organizing'],
            ['en' => 'I ensure quality of execution even with tight deadlines', 'ar' => 'أحرص على جودة التنفيذ حتى مع ضيق الوقت', 'cat' => 'organizing'],
            ['en' => 'I balance speed and accuracy in work', 'ar' => 'أوازن بين السرعة والدقة في العمل', 'cat' => 'organizing'],

            // 7. التكيف والتعامل مع الضغوط (Adapting & Coping) Q49-56
            ['en' => 'I continue working despite time pressure', 'ar' => 'أواصل العمل رغم ضغط الوقت', 'cat' => 'adapting'],
            ['en' => 'I adapt to changes in work requirements', 'ar' => 'أتكيف مع التغيرات في متطلبات العمل', 'cat' => 'adapting'],
            ['en' => 'I control my emotions in stressful situations', 'ar' => 'أتحكم في انفعالاتي في المواقف العصيبة', 'cat' => 'adapting'],
            ['en' => 'I lose focus when pressure increases', 'ar' => 'أفقد تركيزي عند زيادة الضغوط', 'cat' => 'adapting'],
            ['en' => 'I learn from difficult situations', 'ar' => 'أتعلم من المواقف الصعبة', 'cat' => 'adapting'],
            ['en' => 'I get stressed when plans change suddenly', 'ar' => 'أتوتر عندما تتغير الخطط فجأة', 'cat' => 'adapting'],
            ['en' => 'I maintain performance even in uncomfortable conditions', 'ar' => 'أستمر في الأداء حتى في ظروف غير مريحة', 'cat' => 'adapting'],
            ['en' => 'I treat challenges as opportunities for learning', 'ar' => 'أتعامل مع التحديات كفرص للتعلم', 'cat' => 'adapting'],

            // 8. المبادرة والإنجاز (Enterprising & Performing) Q57-64
            ['en' => 'I start executing tasks without waiting for full direction', 'ar' => 'أبدأ في تنفيذ المهام دون انتظار توجيه كامل', 'cat' => 'enterprising'],
            ['en' => 'I strive to achieve results that exceed what is required', 'ar' => 'أسعى لتحقيق نتائج تتجاوز المطلوب', 'cat' => 'enterprising'],
            ['en' => 'I continue working even when there is no follow-up', 'ar' => 'أستمر في العمل حتى عند غياب المتابعة', 'cat' => 'enterprising'],
            ['en' => 'I delay starting tasks until all details are clear', 'ar' => 'أؤجل البدء في المهام حتى تتضح كل التفاصيل', 'cat' => 'enterprising'],
            ['en' => 'I look for opportunities to improve my performance', 'ar' => 'أبحث عن فرص لتحسين أدائي', 'cat' => 'enterprising'],
            ['en' => 'I need external motivation to start working', 'ar' => 'أحتاج إلى دافع خارجي للبدء في العمل', 'cat' => 'enterprising'],
            ['en' => 'I take responsibility for completing work from start to finish', 'ar' => 'أتحمل مسؤولية إنجاز العمل من البداية للنهاية', 'cat' => 'enterprising'],
            ['en' => 'I focus on achieving results even with obstacles', 'ar' => 'أركز على تحقيق النتائج حتى مع وجود عوائق', 'cat' => 'enterprising'],
        ];

        foreach ($questions as $index => $q) {
            $qNum = $index + 1;
            Question::create([
                'test_id' => $test->id,
                'text' => ['en' => $q['en'], 'ar' => $q['ar']],
                'sort_order' => $qNum,
                'is_reverse_scored' => in_array($qNum, $reverseItems),
                'is_required' => true,
                'category_key' => $q['cat'],
                'weight' => 1.00,
            ]);
        }

        $this->command->info("Created Great Eight Competencies test with {$test->questions()->count()} questions (" . count($reverseItems) . " reverse-scored).");
    }
}
