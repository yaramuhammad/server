<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfessionalCompetenciesSeeder extends Seeder
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
                'en' => 'Professional Competencies Assessment',
                'ar' => 'تقييم الجدارات المهنية',
            ],
            'description' => [
                'en' => 'A comprehensive assessment of professional competencies across 8 key dimensions. Rate the extent to which you personally possess each competency.',
                'ar' => 'تقييم شامل للجدارات المهنية عبر 8 أبعاد رئيسية. حدد مدى إمتلاكك لكل جدارة بشكل شخصي.',
            ],
            'instructions' => [
                'en' => 'Below is a set of professional competencies. For each competency, rate the extent to which you personally possess this competency: 1 = Very Low, 5 = Very High. There are no right or wrong answers. These results will be used for planning and implementing appropriate training and development programs, so please answer all statements accurately.',
                'ar' => 'فيما يلي مجموعة من القدرات والمهارات التي قد تكون مطلوبة في عملك. حدد إلى أي مدى تمتلك أنت شخصياً هذه الجدارة: الدرجة 1 تعبر عن احتياجك لتطوير هذه الجدارة بدرجة كبيرة جداً، والدرجة 5 تعبر عن تمكنك من هذه الجدارة بشكل كبير جداً. لا توجد إجابات صحيحة أو خاطئة، وسوف تستخدم هذه النتائج لتخطيط وتنفيذ برامج التدريب والتطوير المناسبة، لذا نرجو منك الإجابة بشكل دقيق على كافة العبارات.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 1,
                'max' => 5,
                'labels' => [
                    '1' => ['en' => 'Very Low', 'ar' => 'ضعيفة جداً'],
                    '2' => ['en' => 'Low', 'ar' => 'ضعيفة'],
                    '3' => ['en' => 'Moderate', 'ar' => 'متوسطة'],
                    '4' => ['en' => 'High', 'ar' => 'عالية'],
                    '5' => ['en' => 'Very High', 'ar' => 'عالية جداً'],
                ],
            ],
            'scoring_type' => 'category',
            'scoring_config' => ['categories' => [
                ['key' => 'leading', 'label' => ['en' => 'Leading and Deciding', 'ar' => 'القيادة واتخاذ القرار'], 'interpretation' => $interp],
                ['key' => 'supporting', 'label' => ['en' => 'Supporting and Cooperating', 'ar' => 'الدعم والتعاون'], 'interpretation' => $interp],
                ['key' => 'interacting', 'label' => ['en' => 'Interacting and Presenting', 'ar' => 'التفاعل والتواصل'], 'interpretation' => $interp],
                ['key' => 'analysing', 'label' => ['en' => 'Analyzing and Interpreting', 'ar' => 'التحليل والتفسير'], 'interpretation' => $interp],
                ['key' => 'creating', 'label' => ['en' => 'Creating and Conceptualizing', 'ar' => 'الإبداع والتصور'], 'interpretation' => $interp],
                ['key' => 'organizing', 'label' => ['en' => 'Organizing and Executing', 'ar' => 'التنظيم والتنفيذ'], 'interpretation' => $interp],
                ['key' => 'adapting', 'label' => ['en' => 'Adapting and Coping', 'ar' => 'التكيف والتعامل مع الضغوط'], 'interpretation' => $interp],
                ['key' => 'enterprising', 'label' => ['en' => 'Enterprising and Performing', 'ar' => 'المبادرة والإنجاز'], 'interpretation' => $interp],
            ]],
            'randomize_questions' => false,
        ]);

        $questions = [
            // ===== Category 1: Leading and Deciding (القيادة واتخاذ القرار) =====
            // Subcategory 11
            ['en' => 'Decision making', 'ar' => 'صنع القرارات', 'cat' => 'leading'],
            ['en' => 'Taking responsibility', 'ar' => 'تحمل المسئوليات', 'cat' => 'leading'],
            ['en' => 'Acting with confidence', 'ar' => 'القدرة على التصرف بثقة', 'cat' => 'leading'],
            ['en' => 'Taking initiative', 'ar' => 'القدرة على المبادرة', 'cat' => 'leading'],
            ['en' => 'Handling difficult situations', 'ar' => 'القدرة على التصرف فى المواقف الصعبة', 'cat' => 'leading'],
            ['en' => 'Calculated risk-taking', 'ar' => 'المخاطرة المحسوبة', 'cat' => 'leading'],
            // Subcategory 12
            ['en' => 'Directing and coordinating efforts', 'ar' => 'القدرة على التوجيه وتنسيق الجهود', 'cat' => 'leading'],
            ['en' => 'Supervising and monitoring', 'ar' => 'القدرة على الاشراف والمراقبة', 'cat' => 'leading'],
            ['en' => 'Mentoring', 'ar' => 'الإرشاد', 'cat' => 'leading'],
            ['en' => 'Delegating', 'ar' => 'التفويض', 'cat' => 'leading'],
            ['en' => 'Empowering others', 'ar' => 'تمكين الأخرين', 'cat' => 'leading'],
            ['en' => 'Motivating and encouraging others', 'ar' => 'تحفيز وتشجيع الآخرين', 'cat' => 'leading'],
            ['en' => 'Developing others', 'ar' => 'تطوير وتنمية الآخرين', 'cat' => 'leading'],
            ['en' => 'Identifying and attracting talent', 'ar' => 'تحديد وجذب واستقطاب المواهب', 'cat' => 'leading'],

            // ===== Category 2: Supporting and Cooperating (الدعم والتعاون) =====
            // Subcategory 21
            ['en' => 'Understanding others', 'ar' => 'فهم الآخرين', 'cat' => 'supporting'],
            ['en' => 'Team compatibility', 'ar' => 'التوافق مع فريق العمل', 'cat' => 'supporting'],
            ['en' => 'Building team spirit', 'ar' => 'بناء روح الفريق', 'cat' => 'supporting'],
            ['en' => 'Recognizing and rewarding others\' achievements', 'ar' => 'الاعتراف بإنجازات الآخرين ومكافأتهم عليها', 'cat' => 'supporting'],
            ['en' => 'Listening to others', 'ar' => 'الاستماع للآخرين', 'cat' => 'supporting'],
            ['en' => 'Consultation', 'ar' => 'المشورة', 'cat' => 'supporting'],
            ['en' => 'Proactive communication', 'ar' => 'التواصل بشكل استباقى (قبل حدوث المشكلة)', 'cat' => 'supporting'],
            ['en' => 'Tolerance and consideration of others', 'ar' => 'التسامح ومراعاة الآخرين', 'cat' => 'supporting'],
            ['en' => 'Empathizing with others', 'ar' => 'القدرة على تفهم الآخرين', 'cat' => 'supporting'],
            ['en' => 'Supporting and backing others', 'ar' => 'دعم ومساندة الآخرين', 'cat' => 'supporting'],
            ['en' => 'Caring for and looking after others', 'ar' => 'الاهتمام بالآخرين ورعايتهم', 'cat' => 'supporting'],
            // Subcategory 22
            ['en' => 'Developing insight and self-awareness', 'ar' => 'تطوير البصيرة ومعرفة الذات', 'cat' => 'supporting'],
            ['en' => 'Promoting ethics and values', 'ar' => 'تعزيز الأخلاق والقيم', 'cat' => 'supporting'],
            ['en' => 'Acting with integrity', 'ar' => 'التصرف بنزاهة', 'cat' => 'supporting'],
            ['en' => 'Leveraging individual diversity', 'ar' => 'استغلال التنوع بين الأفراد', 'cat' => 'supporting'],
            ['en' => 'Demonstrating social and environmental responsibility', 'ar' => 'إظهار المسئولية الاجتماعية والبيئية', 'cat' => 'supporting'],

            // ===== Category 3: Interacting and Presenting (التفاعل والتواصل) =====
            // Subcategory 31
            ['en' => 'Building positive social relationships', 'ar' => 'بناء علاقات اجتماعية إيجابية', 'cat' => 'interacting'],
            ['en' => 'Professional networking', 'ar' => 'التشبيك والتواصل المهنى', 'cat' => 'interacting'],
            ['en' => 'Building relationships with different management levels', 'ar' => 'إقامة العلاقات مع المستويات الإدارية المختلفة', 'cat' => 'interacting'],
            ['en' => 'Conflict management', 'ar' => 'إدارة الصراعات', 'cat' => 'interacting'],
            ['en' => 'Using humor when necessary', 'ar' => 'استخدام حس الفكاهة عند الضرورة', 'cat' => 'interacting'],
            // Subcategory 32
            ['en' => 'Influencing others', 'ar' => 'القدرة على إحداث تأثير فى الآخرين', 'cat' => 'interacting'],
            ['en' => 'Shaping and diversifying conversations', 'ar' => 'تشكيل وتنويع المحادثات', 'cat' => 'interacting'],
            ['en' => 'Evoking emotions', 'ar' => 'استثارة العواطف', 'cat' => 'interacting'],
            ['en' => 'Promoting constructive ideas', 'ar' => 'ترويج الأفكار البناءة', 'cat' => 'interacting'],
            ['en' => 'Negotiation', 'ar' => 'التفاوض', 'cat' => 'interacting'],
            ['en' => 'Reaching mutual agreements', 'ar' => 'الوصول إلى اتفاقات مشتركة', 'cat' => 'interacting'],
            ['en' => 'Handling sensitive issues', 'ar' => 'التعامل مع المشكلات الحساسة', 'cat' => 'interacting'],
            // Subcategory 33
            ['en' => 'Speaking fluently', 'ar' => 'القدرة على التحدث بطلاقة', 'cat' => 'interacting'],
            ['en' => 'Explaining concepts and opinions', 'ar' => 'القدرة على شرح المفاهيم والآراء', 'cat' => 'interacting'],
            ['en' => 'Formulating key discussion points', 'ar' => 'صياغة النقاط الرئيسية فى المناقشات', 'cat' => 'interacting'],
            ['en' => 'Making presentations and public speaking', 'ar' => 'القدرة على عمل العروض والتحدث أمام جمهور', 'cat' => 'interacting'],
            ['en' => 'Demonstrating credibility', 'ar' => 'ابراز المصداقية', 'cat' => 'interacting'],
            ['en' => 'Responding to audience questions', 'ar' => 'الرد على تساؤلات المستمعين', 'cat' => 'interacting'],

            // ===== Category 4: Analyzing and Interpreting (التحليل والتفسير) =====
            // Subcategory 41
            ['en' => 'Professional writing skills', 'ar' => 'القدرة على الكتابة المهنية بشكل صحيح', 'cat' => 'analysing'],
            ['en' => 'Writing clearly and fluently', 'ar' => 'القدرة على الكتابة بوضوح وطلاقة', 'cat' => 'analysing'],
            ['en' => 'Writing in an expressive and engaging style', 'ar' => 'الكتابة بأسلوب معبر وجذاب', 'cat' => 'analysing'],
            ['en' => 'Establishing purposeful communication', 'ar' => 'القدرة على إقامة اتصالات هادفة', 'cat' => 'analysing'],
            // Subcategory 42
            ['en' => 'Applying technical expertise', 'ar' => 'تطبيق الخبرات الفنية', 'cat' => 'analysing'],
            ['en' => 'Developing technical expertise', 'ar' => 'القدرة على تطوير الخبرات الفنية', 'cat' => 'analysing'],
            ['en' => 'Sharing technical and managerial expertise', 'ar' => 'مشاركة الخبرات الفنية والإدارية', 'cat' => 'analysing'],
            ['en' => 'Using technological resources', 'ar' => 'استخدام الموارد التكنولوجية', 'cat' => 'analysing'],
            ['en' => 'Physical and manual skills', 'ar' => 'المهارات البدنية واليدوية', 'cat' => 'analysing'],
            ['en' => 'Cross-functional awareness', 'ar' => 'الوعى بالتكامل بين الوظائف', 'cat' => 'analysing'],
            ['en' => 'Spatial awareness', 'ar' => 'الوعى المكاني', 'cat' => 'analysing'],
            // Subcategory 43
            ['en' => 'Analyzing and evaluating information', 'ar' => 'تحليل وتقييم المعلومات', 'cat' => 'analysing'],
            ['en' => 'Testing and verifying assumptions', 'ar' => 'اختبار الافتراضات والتحقق منها', 'cat' => 'analysing'],
            ['en' => 'Producing solutions to problems', 'ar' => 'إنتاج حلول للمشكلات', 'cat' => 'analysing'],
            ['en' => 'Making sound judgments', 'ar' => 'القدرة على إصدار أحكام صائبة', 'cat' => 'analysing'],
            ['en' => 'Systematic thinking', 'ar' => 'القدرة على التفكير المنظم', 'cat' => 'analysing'],

            // ===== Category 5: Creating and Conceptualizing (الإبداع والتصور) =====
            // Subcategory 51
            ['en' => 'Rapid learning ability', 'ar' => 'القدرة على التعلم السريع', 'cat' => 'creating'],
            ['en' => 'Information gathering ability', 'ar' => 'القدرة على جمع المعلومات', 'cat' => 'creating'],
            ['en' => 'Quick thinking', 'ar' => 'التفكير السريع', 'cat' => 'creating'],
            ['en' => 'Encouraging and supporting organizational learning', 'ar' => 'تشجيع ودعم التعلم التنظيمى', 'cat' => 'creating'],
            ['en' => 'Managing information and knowledge', 'ar' => 'إدارة المعلومات والمعارف', 'cat' => 'creating'],
            // Subcategory 52
            ['en' => 'Creativity and innovation', 'ar' => 'الإبداع والابتكار', 'cat' => 'creating'],
            ['en' => 'Seeking possible changes and improvements', 'ar' => 'البحث عن التغييرات والتحسينات الممكنة', 'cat' => 'creating'],
            // Subcategory 53
            ['en' => 'Big-picture thinking', 'ar' => 'التفكير فى الصورة الكبرى (على نطاق واسع)', 'cat' => 'creating'],
            ['en' => 'Strategic task management', 'ar' => 'التعامل مع المهام بطريقة استراتيجية', 'cat' => 'creating'],
            ['en' => 'Planning and developing business strategies', 'ar' => 'تخطيط وتطوير استراتيجيات الأعمال', 'cat' => 'creating'],
            ['en' => 'Developing and articulating vision', 'ar' => 'وضع وصياغة الرؤية', 'cat' => 'creating'],

            // ===== Category 6: Organizing and Executing (التنظيم والتنفيذ) =====
            // Subcategory 61
            ['en' => 'Setting objectives', 'ar' => 'وضع الأهداف', 'cat' => 'organizing'],
            ['en' => 'Planning', 'ar' => 'التخطيط', 'cat' => 'organizing'],
            ['en' => 'Managing work teams', 'ar' => 'إدارة فرق العمل', 'cat' => 'organizing'],
            ['en' => 'Managing organizational resources', 'ar' => 'إدارة الموارد التنظيمية', 'cat' => 'organizing'],
            ['en' => 'Monitoring progress toward objectives', 'ar' => 'مراقبة التقدم فى تحقيق الأهداف', 'cat' => 'organizing'],
            // Subcategory 62
            ['en' => 'Focusing on identifying and meeting customer needs', 'ar' => 'التركيز على تحديد وإشباع احتياجات العملاء', 'cat' => 'organizing'],
            ['en' => 'Setting high quality standards', 'ar' => 'وضع معايير عالية للجودة', 'cat' => 'organizing'],
            ['en' => 'Monitoring quality processes', 'ar' => 'مراقبة عمليات الجودة', 'cat' => 'organizing'],
            ['en' => 'Working in an organized manner', 'ar' => 'العمل بشكل منظم', 'cat' => 'organizing'],
            ['en' => 'Maintaining quality processes', 'ar' => 'الحفاظ على عمليات الجودة', 'cat' => 'organizing'],
            ['en' => 'Maintaining high productivity levels', 'ar' => 'الحفاظ على مستويات إنتاجية عالية', 'cat' => 'organizing'],
            ['en' => 'Achieving project results and objectives', 'ar' => 'تحقيق نتائج وأهداف المشروعات', 'cat' => 'organizing'],
            // Subcategory 63
            ['en' => 'Following instructions', 'ar' => 'القدرة على إتباع التعليمات', 'cat' => 'organizing'],
            ['en' => 'Following and implementing policies and procedures', 'ar' => 'القدرة على إتباع وتنفيذ السياسات والإجراءات', 'cat' => 'organizing'],
            ['en' => 'Discipline and time management', 'ar' => 'القدرة على الانضباط وضبط الوقت', 'cat' => 'organizing'],
            ['en' => 'Commitment', 'ar' => 'القدرة على الالتزام', 'cat' => 'organizing'],
            ['en' => 'Occupational health and safety awareness', 'ar' => 'الوعى بقضايا الصحة والسلامة المهنية', 'cat' => 'organizing'],
            ['en' => 'Compliance with legal duties and responsibilities', 'ar' => 'الإلتزام بالواجبات والمسئوليات القانونية', 'cat' => 'organizing'],

            // ===== Category 7: Adapting and Coping (التكيف والتعامل مع الضغوط) =====
            // Subcategory 71
            ['en' => 'Adapting to changing reality', 'ar' => 'التكيف مع الواقع المتغير', 'cat' => 'adapting'],
            ['en' => 'Accepting new ideas', 'ar' => 'تقبل الأفكار الجديدة', 'cat' => 'adapting'],
            ['en' => 'Adapting to different interpersonal styles', 'ar' => 'التكيف مع أساليب التعامل الشخصي المختلفة', 'cat' => 'adapting'],
            ['en' => 'Cultural differences awareness', 'ar' => 'الوعى بالاختلافات بين الثقافات', 'cat' => 'adapting'],
            ['en' => 'Handling ambiguous situations', 'ar' => 'القدرة على التعامل مع المواقف الغامضة', 'cat' => 'adapting'],
            // Subcategory 72
            ['en' => 'Coping with pressure', 'ar' => 'القدرة على مواجهة الضغوط', 'cat' => 'adapting'],
            ['en' => 'Self-control and emotional regulation', 'ar' => 'ضبط النفس والانفعالات', 'cat' => 'adapting'],
            ['en' => 'Achieving work-life balance', 'ar' => 'تحقيق التوازن بين العمل والحياة الشخصية', 'cat' => 'adapting'],
            ['en' => 'Maintaining a positive outlook on work and life', 'ar' => 'الحفاظ على منظور إيجابى للعمل والحياة', 'cat' => 'adapting'],
            ['en' => 'Handling criticism', 'ar' => 'القدرة على التعامل مع الانتقادات', 'cat' => 'adapting'],

            // ===== Category 8: Enterprising and Performing (المبادرة والإنجاز) =====
            // Subcategory 81
            ['en' => 'Achieving objectives', 'ar' => 'القدرة على تحقيق الأهداف', 'cat' => 'enterprising'],
            ['en' => 'Working with energy and enthusiasm', 'ar' => 'العمل بنشاط وحماس', 'cat' => 'enterprising'],
            ['en' => 'Continuous self-development', 'ar' => 'السعى الدائم لتطوير الذات', 'cat' => 'enterprising'],
            ['en' => 'Continuous ambition', 'ar' => 'الطموح المستمر', 'cat' => 'enterprising'],
            // Subcategory 82
            ['en' => 'Monitoring markets and competitors', 'ar' => 'مراقبة الأسواق والمنافسين', 'cat' => 'enterprising'],
            ['en' => 'Identifying and leveraging opportunities for improvement', 'ar' => 'تحديد واستغلال الفرص المتاحة لتحسين العمل', 'cat' => 'enterprising'],
            ['en' => 'Financial awareness in business', 'ar' => 'الوعى بالجوانب المالية للأعمال', 'cat' => 'enterprising'],
            ['en' => 'Expense control', 'ar' => 'القدرة على ضبط النفقات', 'cat' => 'enterprising'],
            ['en' => 'Awareness of organizational issues and challenges', 'ar' => 'الدراية بالقضايا والمشكلات التنظيمية', 'cat' => 'enterprising'],
        ];

        foreach ($questions as $index => $q) {
            Question::create([
                'test_id' => $test->id,
                'text' => ['en' => $q['en'], 'ar' => $q['ar']],
                'sort_order' => $index + 1,
                'is_reverse_scored' => false,
                'is_required' => true,
                'category_key' => $q['cat'],
                'weight' => 1.00,
            ]);
        }

        $this->command->info("Created Professional Competencies Assessment with {$test->questions()->count()} questions across 8 categories.");
    }
}
