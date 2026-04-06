<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'title' => ['en' => 'Occupational Burnout', 'ar' => 'الإحتراق الوظيفي'],
            'description' => ['en' => 'Assessments measuring workplace burnout, emotional exhaustion, and professional fatigue.', 'ar' => 'تقييمات تقيس الإحتراق الوظيفي والإرهاق العاطفي والإعياء المهني.'],
            'sort_order' => 1,
        ]);

        Category::create([
            'title' => ['en' => 'Psychological Stress', 'ar' => 'الضغط النفسي'],
            'description' => ['en' => 'Assessments measuring perceived stress levels and psychological pressure.', 'ar' => 'تقييمات تقيس مستويات الضغط النفسي المُدرك والضغوط النفسية.'],
            'sort_order' => 2,
        ]);

        Category::create([
            'title' => ['en' => 'Job Satisfaction', 'ar' => 'الرضا الوظيفي'],
            'description' => ['en' => 'Assessments measuring employee satisfaction with work environment, compensation, and growth.', 'ar' => 'تقييمات تقيس رضا الموظفين عن بيئة العمل والتعويضات وفرص النمو.'],
            'sort_order' => 3,
        ]);
    }
}
