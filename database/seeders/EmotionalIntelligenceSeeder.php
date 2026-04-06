<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmotionalIntelligenceSeeder extends Seeder
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
                'en' => 'Social and Emotional Intelligence Scale',
                'ar' => 'مقياس الذكاء الاجتماعى والوجدانى',
            ],
            'description' => [
                'en' => 'A comprehensive assessment measuring social and emotional intelligence across four key clusters: Self-Awareness, Self-Management, Social Awareness, and Relationship Management.',
                'ar' => 'تقييم شامل يقيس الذكاء الاجتماعي والوجداني عبر أربعة محاور رئيسية: الوعي بالذات، إدارة الذات، الوعي الاجتماعي، وإدارة العلاقات.',
            ],
            'instructions' => [
                'en' => 'Below are statements that describe behaviors related to social and emotional intelligence. Rate the extent to which each statement applies to you: 1 = Does not apply at all, 5 = Applies completely. There are no right or wrong answers.',
                'ar' => 'فيما يلي مجموعة من العبارات التي تصف سلوكيات مرتبطة بالذكاء الاجتماعي والوجداني. حدد مدى انطباق كل عبارة عليك: 1 = لا تنطبق مطلقاً، 5 = تنطبق تماماً. لا توجد إجابات صحيحة أو خاطئة.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 1,
                'max' => 5,
                'labels' => [
                    '1' => ['en' => 'Does not apply at all', 'ar' => 'لا تنطبق مطلقاً'],
                    '2' => ['en' => 'Applies slightly', 'ar' => 'تنطبق بدرجة قليلة'],
                    '3' => ['en' => 'Applies moderately', 'ar' => 'تنطبق بدرجة متوسطة'],
                    '4' => ['en' => 'Applies to a large extent', 'ar' => 'تنطبق بدرجة كبيرة'],
                    '5' => ['en' => 'Applies completely', 'ar' => 'تنطبق تماماً'],
                ],
            ],
            'scoring_type' => 'category',
            'scoring_config' => ['categories' => [
                ['key' => 'self_awareness', 'label' => ['en' => 'Self-Awareness', 'ar' => 'الوعي بالذات'], 'interpretation' => $interp],
                ['key' => 'self_management', 'label' => ['en' => 'Self-Management', 'ar' => 'إدارة الذات'], 'interpretation' => $interp],
                ['key' => 'social_awareness', 'label' => ['en' => 'Social Awareness', 'ar' => 'الوعي الاجتماعي'], 'interpretation' => $interp],
                ['key' => 'relationship_management', 'label' => ['en' => 'Relationship Management', 'ar' => 'إدارة العلاقات'], 'interpretation' => $interp],
            ]],
            'randomize_questions' => false,
        ]);

        // Questions ordered by the last column (sort order) from the images
        // Format: sort_order => [en, ar, cluster, is_reverse]
        $questions = [
            1  => ['en' => 'Anticipates how others will respond when trying to convince them', 'ar' => 'يتوقع كيف سيستجيب الآخرون عند محاولة إقناعهم', 'cat' => 'relationship_management', 'rev' => false],
            2  => ['en' => 'Works well in teams by encouraging cooperation', 'ar' => 'يعمل بشكل جيد ضمن الفرق من خلال تشجيع التعاون', 'cat' => 'relationship_management', 'rev' => false],
            3  => ['en' => 'Convinces others by developing behind the scenes support', 'ar' => 'يقنع الآخرين من خلال بناء الدعم من وراء الكواليس', 'cat' => 'relationship_management', 'rev' => false],
            4  => ['en' => 'Initiates actions to improve own performance', 'ar' => 'يبادر باتخاذ إجراءات لتحسين أدائه', 'cat' => 'self_management', 'rev' => false],
            5  => ['en' => 'Does not cooperate with others', 'ar' => 'لا يتعاون مع الآخرين', 'cat' => 'relationship_management', 'rev' => true],
            6  => ['en' => 'Coaches and mentors others', 'ar' => 'يدرب ويوجه الآخرين', 'cat' => 'relationship_management', 'rev' => false],
            7  => ['en' => 'Loses composure when under stress', 'ar' => 'يفقد رباطة جأشه عند التعرض للضغوط', 'cat' => 'self_management', 'rev' => true],
            8  => ['en' => 'Sees possibilities more than problems', 'ar' => 'يرى الإمكانيات أكثر من المشكلات', 'cat' => 'self_management', 'rev' => false],
            9  => ['en' => 'Shows awareness of own feelings', 'ar' => 'يُظهر وعياً بمشاعره الخاصة', 'cat' => 'self_awareness', 'rev' => false],
            10 => ['en' => 'Remains calm in stressful situations', 'ar' => 'يبقى هادئاً في المواقف العصيبة', 'cat' => 'self_management', 'rev' => false],
            11 => ['en' => 'Understands the informal processes by which work gets done in the team or organization', 'ar' => 'يفهم العمليات غير الرسمية التي يتم من خلالها إنجاز العمل في الفريق أو المنظمة', 'cat' => 'social_awareness', 'rev' => false],
            12 => ['en' => 'Understands the team\'s or organization\'s unspoken rules', 'ar' => 'يفهم القواعد غير المكتوبة للفريق أو المنظمة', 'cat' => 'social_awareness', 'rev' => false],
            13 => ['en' => 'Convinces others by getting support from key people', 'ar' => 'يقنع الآخرين من خلال الحصول على دعم الأشخاص المؤثرين', 'cat' => 'relationship_management', 'rev' => false],
            14 => ['en' => 'Adapts to shifting priorities and rapid change', 'ar' => 'يتكيف مع تغير الأولويات والتغيير السريع', 'cat' => 'self_management', 'rev' => false],
            15 => ['en' => 'Does not try to improve', 'ar' => 'لا يحاول التحسن', 'cat' => 'self_management', 'rev' => true],
            16 => ['en' => 'Convinces others through discussion', 'ar' => 'يقنع الآخرين من خلال النقاش', 'cat' => 'relationship_management', 'rev' => false],
            17 => ['en' => 'Able to describe how own feelings affect own actions', 'ar' => 'قادر على وصف كيف تؤثر مشاعره على تصرفاته', 'cat' => 'self_awareness', 'rev' => false],
            18 => ['en' => 'Seeks to improve own self by setting measurable and challenging goals', 'ar' => 'يسعى لتحسين ذاته من خلال وضع أهداف قابلة للقياس وتحدي', 'cat' => 'self_management', 'rev' => false],
            19 => ['en' => 'Seeks ways to do things better', 'ar' => 'يبحث عن طرق لأداء الأشياء بشكل أفضل', 'cat' => 'self_management', 'rev' => false],
            20 => ['en' => 'Understands the values and culture of the team or organization', 'ar' => 'يفهم قيم وثقافة الفريق أو المنظمة', 'cat' => 'social_awareness', 'rev' => false],
            21 => ['en' => 'Sees the positive in people, situations, and events more than the negative', 'ar' => 'يرى الجانب الإيجابي في الأشخاص والمواقف والأحداث أكثر من السلبي', 'cat' => 'self_management', 'rev' => false],
            22 => ['en' => 'Convinces others by appealing to their self-interest', 'ar' => 'يقنع الآخرين من خلال مخاطبة مصلحتهم الشخصية', 'cat' => 'relationship_management', 'rev' => false],
            23 => ['en' => 'Views the future with hope', 'ar' => 'ينظر إلى المستقبل بأمل', 'cat' => 'self_management', 'rev' => false],
            24 => ['en' => 'Adapts by applying standard procedures flexibly', 'ar' => 'يتكيف من خلال تطبيق الإجراءات المعيارية بمرونة', 'cat' => 'self_management', 'rev' => false],
            25 => ['en' => 'Understands others\' perspectives when they are different from own perspective', 'ar' => 'يفهم وجهات نظر الآخرين عندما تختلف عن وجهة نظره', 'cat' => 'social_awareness', 'rev' => false],
            26 => ['en' => 'Remains composed, even in trying moments', 'ar' => 'يبقى متماسكاً حتى في اللحظات الصعبة', 'cat' => 'self_management', 'rev' => false],
            27 => ['en' => 'Understands social networks', 'ar' => 'يفهم الشبكات الاجتماعية', 'cat' => 'social_awareness', 'rev' => false],
            28 => ['en' => 'Understands others by listening attentively', 'ar' => 'يفهم الآخرين من خلال الاستماع باهتمام', 'cat' => 'social_awareness', 'rev' => false],
            29 => ['en' => 'Acknowledges own strengths and weaknesses', 'ar' => 'يعترف بنقاط قوته وضعفه', 'cat' => 'self_awareness', 'rev' => false],
            30 => ['en' => 'Does not spend time developing others', 'ar' => 'لا يقضي وقتاً في تطوير الآخرين', 'cat' => 'relationship_management', 'rev' => true],
            31 => ['en' => 'Does not inspire followers', 'ar' => 'لا يُلهم الأتباع', 'cat' => 'relationship_management', 'rev' => true],
            32 => ['en' => 'Sees opportunities more than threats', 'ar' => 'يرى الفرص أكثر من التهديدات', 'cat' => 'self_management', 'rev' => false],
            33 => ['en' => 'Works well in teams by being supportive', 'ar' => 'يعمل بشكل جيد ضمن الفرق من خلال تقديم الدعم', 'cat' => 'relationship_management', 'rev' => false],
            34 => ['en' => 'Provides on-going mentoring or coaching', 'ar' => 'يقدم التوجيه والتدريب المستمر', 'cat' => 'relationship_management', 'rev' => false],
            35 => ['en' => 'Sees the positive side of a difficult situation', 'ar' => 'يرى الجانب الإيجابي في الموقف الصعب', 'cat' => 'self_management', 'rev' => false],
            36 => ['en' => 'Tries to resolve conflict instead of allowing it to fester', 'ar' => 'يحاول حل الصراع بدلاً من تركه يتفاقم', 'cat' => 'relationship_management', 'rev' => false],
            37 => ['en' => 'Personally invests time and effort in developing others', 'ar' => 'يستثمر شخصياً الوقت والجهد في تطوير الآخرين', 'cat' => 'relationship_management', 'rev' => false],
            38 => ['en' => 'Cares about others and their development', 'ar' => 'يهتم بالآخرين وتطورهم', 'cat' => 'relationship_management', 'rev' => false],
            39 => ['en' => 'Works well in teams by soliciting others\' input', 'ar' => 'يعمل بشكل جيد ضمن الفرق من خلال طلب مساهمات الآخرين', 'cat' => 'relationship_management', 'rev' => false],
            40 => ['en' => 'Controls impulses appropriately in situations', 'ar' => 'يسيطر على اندفاعاته بشكل مناسب في المواقف', 'cat' => 'self_management', 'rev' => false],
            41 => ['en' => 'Acts appropriately even in emotionally charged situations', 'ar' => 'يتصرف بشكل مناسب حتى في المواقف المشحونة عاطفياً', 'cat' => 'self_management', 'rev' => false],
            42 => ['en' => 'Aware of the connection between what is happening and own feelings', 'ar' => 'يدرك العلاقة بين ما يحدث ومشاعره الخاصة', 'cat' => 'self_awareness', 'rev' => false],
            43 => ['en' => 'Does not strive to improve own performance', 'ar' => 'لا يسعى لتحسين أدائه', 'cat' => 'self_management', 'rev' => true],
            44 => ['en' => 'Has difficulty adapting to uncertain and changing conditions', 'ar' => 'يواجه صعوبة في التكيف مع الظروف غير المؤكدة والمتغيرة', 'cat' => 'self_management', 'rev' => true],
            45 => ['en' => 'Believes the future will be better than the past', 'ar' => 'يؤمن بأن المستقبل سيكون أفضل من الماضي', 'cat' => 'self_management', 'rev' => false],
            46 => ['en' => 'Resolves conflict by bringing it into the open', 'ar' => 'يحل الصراع من خلال طرحه بشكل علني', 'cat' => 'relationship_management', 'rev' => false],
            47 => ['en' => 'Leads by inspiring people', 'ar' => 'يقود من خلال إلهام الناس', 'cat' => 'relationship_management', 'rev' => false],
            48 => ['en' => 'Adapts by smoothly juggling multiple demands', 'ar' => 'يتكيف من خلال التوفيق بسلاسة بين المتطلبات المتعددة', 'cat' => 'self_management', 'rev' => false],
            49 => ['en' => 'Does not understand subtle feelings of others', 'ar' => 'لا يفهم المشاعر الدقيقة للآخرين', 'cat' => 'social_awareness', 'rev' => true],
            50 => ['en' => 'Understands another person\'s motivation', 'ar' => 'يفهم دوافع الشخص الآخر', 'cat' => 'social_awareness', 'rev' => false],
            51 => ['en' => 'Allows conflict to fester', 'ar' => 'يسمح للصراع بالتفاقم', 'cat' => 'relationship_management', 'rev' => true],
            52 => ['en' => 'Understands the informal structure in the team or organization', 'ar' => 'يفهم الهيكل غير الرسمي في الفريق أو المنظمة', 'cat' => 'social_awareness', 'rev' => false],
            53 => ['en' => 'Adapts overall strategy, goals, or projects to fit the situation', 'ar' => 'يكيّف الاستراتيجية العامة أو الأهداف أو المشاريع لتناسب الموقف', 'cat' => 'self_management', 'rev' => false],
            54 => ['en' => 'Resolves conflict by de-escalating the emotions in a situation', 'ar' => 'يحل الصراع من خلال تهدئة المشاعر في الموقف', 'cat' => 'relationship_management', 'rev' => false],
            55 => ['en' => 'Describes underlying reasons for own feelings', 'ar' => 'يصف الأسباب الكامنة وراء مشاعره', 'cat' => 'self_awareness', 'rev' => false],
            56 => ['en' => 'Works well in teams by encouraging participation of everyone present', 'ar' => 'يعمل بشكل جيد ضمن الفرق من خلال تشجيع مشاركة الجميع', 'cat' => 'relationship_management', 'rev' => false],
            57 => ['en' => 'Leads by articulating a compelling vision', 'ar' => 'يقود من خلال صياغة رؤية مقنعة', 'cat' => 'relationship_management', 'rev' => false],
            58 => ['en' => 'Does not describe own feelings', 'ar' => 'لا يصف مشاعره الخاصة', 'cat' => 'self_awareness', 'rev' => true],
            59 => ['en' => 'Tries to resolve conflict by openly talking about disagreements with those involved', 'ar' => 'يحاول حل الصراع من خلال التحدث بصراحة عن الخلافات مع المعنيين', 'cat' => 'relationship_management', 'rev' => false],
            60 => ['en' => 'Understands others by putting self into others\' shoes', 'ar' => 'يفهم الآخرين من خلال وضع نفسه مكانهم', 'cat' => 'social_awareness', 'rev' => false],
            61 => ['en' => 'Works well in teams by being respectful of others', 'ar' => 'يعمل بشكل جيد ضمن الفرق من خلال احترام الآخرين', 'cat' => 'relationship_management', 'rev' => false],
            62 => ['en' => 'Provides feedback others find helpful for their development', 'ar' => 'يقدم ملاحظات يجدها الآخرون مفيدة لتطورهم', 'cat' => 'relationship_management', 'rev' => false],
            63 => ['en' => 'Leads by building pride in the group', 'ar' => 'يقود من خلال بناء الفخر في المجموعة', 'cat' => 'relationship_management', 'rev' => false],
            64 => ['en' => 'Gets impatient or shows frustration inappropriately', 'ar' => 'يفقد صبره أو يُظهر إحباطه بشكل غير لائق', 'cat' => 'self_management', 'rev' => true],
            65 => ['en' => 'Adapts overall strategy, goals, or projects to cope with unexpected events', 'ar' => 'يكيّف الاستراتيجية العامة أو الأهداف أو المشاريع للتعامل مع الأحداث غير المتوقعة', 'cat' => 'self_management', 'rev' => false],
            66 => ['en' => 'Strives to improve own performance', 'ar' => 'يسعى جاهداً لتحسين أدائه', 'cat' => 'self_management', 'rev' => false],
            67 => ['en' => 'Leads by bringing out the best in people', 'ar' => 'يقود من خلال إبراز أفضل ما في الناس', 'cat' => 'relationship_management', 'rev' => false],
            68 => ['en' => 'Convinces others by using multiple strategies', 'ar' => 'يقنع الآخرين باستخدام استراتيجيات متعددة', 'cat' => 'relationship_management', 'rev' => false],
        ];

        foreach ($questions as $sortOrder => $q) {
            Question::create([
                'test_id' => $test->id,
                'text' => ['en' => $q['en'], 'ar' => $q['ar']],
                'sort_order' => $sortOrder,
                'is_reverse_scored' => $q['rev'],
                'is_required' => true,
                'category_key' => $q['cat'],
                'weight' => 1.00,
            ]);
        }

        $reverseCount = count(array_filter($questions, fn($q) => $q['rev']));
        $this->command->info("Created Social and Emotional Intelligence Scale with {$test->questions()->count()} questions ({$reverseCount} reverse-scored) across 4 categories.");
    }
}
