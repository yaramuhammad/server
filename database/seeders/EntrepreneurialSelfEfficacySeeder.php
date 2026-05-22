<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class EntrepreneurialSelfEfficacySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $interp = [
            ['min' => 0,  'max' => 39,  'label' => ['en' => 'Low',       'ar' => 'منخفض']],
            ['min' => 40, 'max' => 59,  'label' => ['en' => 'Moderate',  'ar' => 'متوسط']],
            ['min' => 60, 'max' => 79,  'label' => ['en' => 'High',      'ar' => 'مرتفع']],
            ['min' => 80, 'max' => 100, 'label' => ['en' => 'Very High', 'ar' => 'مرتفع جداً']],
        ];

        $test = Test::create([
            'user_id' => $admin->id,
            'title' => [
                'en' => 'Entrepreneurial Self-Efficacy Scale (ESE)',
                'ar' => 'مقياس الكفاءة الذاتية الريادية (ESE)',
            ],
            'description' => [
                'en' => 'A 23-item scale measuring an entrepreneur\'s degree of certainty in performing key business roles and tasks across five domains: Marketing, Innovation, Management, Risk-taking, and Financial Control.',
                'ar' => 'مقياس من 23 بنداً يقيس درجة يقين رائد الأعمال في أداء الأدوار والمهام التجارية الأساسية عبر خمسة مجالات: التسويق، والابتكار، والإدارة، والمخاطرة، والرقابة المالية.',
            ],
            'instructions' => [
                'en' => 'Indicate your degree of certainty in performing each of the following roles or tasks on a 5-point scale: 1 = Completely Unsure, 5 = Completely Sure.',
                'ar' => 'حدد درجة يقينك في أداء كل من الأدوار أو المهام التالية على مقياس من 5 نقاط: 1 = غير متأكد تماماً، 5 = متأكد تماماً.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 1,
                'max' => 5,
                'labels' => [
                    '1' => ['en' => 'Completely Unsure', 'ar' => 'غير متأكد تماماً'],
                    '2' => ['en' => 'Somewhat Unsure',   'ar' => 'غير متأكد إلى حد ما'],
                    '3' => ['en' => 'Neutral',           'ar' => 'محايد'],
                    '4' => ['en' => 'Somewhat Sure',     'ar' => 'متأكد إلى حد ما'],
                    '5' => ['en' => 'Completely Sure',   'ar' => 'متأكد تماماً'],
                ],
            ],
            'scoring_type' => 'category',
            'scoring_config' => [
                'categories' => [
                    ['key' => 'marketing',         'label' => ['en' => 'Marketing',         'ar' => 'التسويق'],          'interpretation' => $interp],
                    ['key' => 'innovation',        'label' => ['en' => 'Innovation',        'ar' => 'الابتكار'],         'interpretation' => $interp],
                    ['key' => 'management',        'label' => ['en' => 'Management',        'ar' => 'الإدارة'],          'interpretation' => $interp],
                    ['key' => 'risk_taking',       'label' => ['en' => 'Risk-taking',       'ar' => 'المخاطرة'],         'interpretation' => $interp],
                    ['key' => 'financial_control', 'label' => ['en' => 'Financial Control', 'ar' => 'الرقابة المالية'], 'interpretation' => $interp],
                ],
            ],
            'randomize_questions' => false,
            'chart_type' => 'column',
        ]);

        $categoryMap = [
            1 => 'marketing', 2 => 'marketing', 3 => 'marketing', 4 => 'marketing', 5 => 'marketing', 6 => 'marketing',
            7 => 'innovation', 8 => 'innovation', 9 => 'innovation', 10 => 'innovation',
            11 => 'management', 12 => 'management', 13 => 'management', 14 => 'management', 15 => 'management',
            16 => 'risk_taking', 17 => 'risk_taking', 18 => 'risk_taking', 19 => 'risk_taking',
            20 => 'financial_control', 21 => 'financial_control', 22 => 'financial_control',
        ];

        $questions = [
            // Marketing (6)
            1 => ['en' => 'Set and meet market share goals', 'ar' => 'وضع أهداف الحصة السوقية وتحقيقها'],
            2 => ['en' => 'Set and meet sales goals', 'ar' => 'وضع أهداف المبيعات وتحقيقها'],
            3 => ['en' => 'Set and attain profit goals', 'ar' => 'وضع أهداف الربح وتحقيقها'],
            4 => ['en' => 'Establish position in product market', 'ar' => 'ترسيخ مكانة في سوق المنتج'],
            5 => ['en' => 'Conduct market analysis', 'ar' => 'إجراء تحليل السوق'],
            6 => ['en' => 'Expand business', 'ar' => 'توسيع نطاق الأعمال'],

            // Innovation (4)
            7 => ['en' => 'New venturing and new ideas', 'ar' => 'إطلاق المشاريع الجديدة والأفكار الجديدة'],
            8 => ['en' => 'New products and services', 'ar' => 'تطوير منتجات وخدمات جديدة'],
            9 => ['en' => 'New markets and geographic territories', 'ar' => 'دخول أسواق جديدة ومناطق جغرافية جديدة'],
            10 => ['en' => 'New methods of production, marketing, and management', 'ar' => 'اعتماد أساليب جديدة في الإنتاج والتسويق والإدارة'],

            // Management (5)
            11 => ['en' => 'Reduce risk and uncertainty', 'ar' => 'تقليل المخاطرة وعدم اليقين'],
            12 => ['en' => 'Strategic planning and develop information system', 'ar' => 'التخطيط الاستراتيجي وتطوير نظام المعلومات'],
            13 => ['en' => 'Manage time by setting goals', 'ar' => 'إدارة الوقت من خلال وضع الأهداف'],
            14 => ['en' => 'Establish and achieve goals and objectives', 'ar' => 'وضع الأهداف والغايات وتحقيقها'],
            15 => ['en' => 'Define organizational roles, responsibilities, and policies', 'ar' => 'تحديد الأدوار التنظيمية والمسؤوليات والسياسات'],

            // Risk-taking (4)
            16 => ['en' => 'Take calculated risks', 'ar' => 'اتخاذ مخاطر محسوبة'],
            17 => ['en' => 'Make decisions under uncertainty and risk', 'ar' => 'اتخاذ القرارات في ظل عدم اليقين والمخاطرة'],
            18 => ['en' => 'Take responsibility for ideas and decisions', 'ar' => 'تحمل المسؤولية عن الأفكار والقرارات'],
            19 => ['en' => 'Work under pressure and conflict', 'ar' => 'العمل تحت الضغط والصراع'],

            // Financial Control (3)
            20 => ['en' => 'Perform financial analysis', 'ar' => 'إجراء التحليل المالي'],
            21 => ['en' => 'Develop financial system and internal controls', 'ar' => 'تطوير النظام المالي والضوابط الداخلية'],
            22 => ['en' => 'Control cost', 'ar' => 'ضبط التكاليف'],
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

        $this->command->info("Created Entrepreneurial Self-Efficacy Scale (ESE) with {$test->questions()->count()} questions across 5 domains.");
    }
}
