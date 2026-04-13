<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $sarah = User::where('email', 'sarah@edrak.com')->first();
        $omar = User::where('email', 'omar@edrak.com')->first();

        $burnout = Test::whereRaw("title->>'en' = ?", ['Maslach Burnout Inventory'])->first();
        $pss = Test::whereRaw("title->>'en' = ?", ['Perceived Stress Scale (PSS-10)'])->first();
        $jobSat = Test::whereRaw("title->>'en' = ?", ['Job Satisfaction Survey'])->first();

        // Assessment 1: Employee Well-being (Burnout + PSS) — published
        $wellbeing = Assessment::create([
            'user_id' => $sarah->id,
            'title' => [
                'en' => 'Employee Well-being Assessment',
                'ar' => 'تقييم رفاهية الموظفين',
            ],
            'description' => [
                'en' => 'A comprehensive assessment measuring burnout levels and perceived stress among employees.',
                'ar' => 'تقييم شامل يقيس مستويات الإحتراق الوظيفي والضغط النفسي المُدرك لدى الموظفين.',
            ],
            'instructions' => [
                'en' => 'This assessment contains two tests. Please complete both tests honestly. There are no right or wrong answers.',
                'ar' => 'يحتوي هذا التقييم على اختبارين. يرجى إكمال كلا الاختبارين بصدق. لا توجد إجابات صحيحة أو خاطئة.',
            ],
            'status' => 'published',
            'show_results_to_participant' => true,
        ]);
        $wellbeing->tests()->attach([
            $burnout->id => ['sort_order' => 1],
            $pss->id => ['sort_order' => 2],
        ]);

        // Assessment 2: HR Quarterly Review (Burnout + Job Satisfaction) — draft
        $hrReview = Assessment::create([
            'user_id' => $omar->id,
            'title' => [
                'en' => 'HR Quarterly Review',
                'ar' => 'المراجعة الربعية للموارد البشرية',
            ],
            'description' => [
                'en' => 'Quarterly assessment for HR departments combining burnout screening with job satisfaction measurement.',
                'ar' => 'تقييم ربعي لإدارات الموارد البشرية يجمع بين فحص الإحتراق الوظيفي وقياس الرضا الوظيفي.',
            ],
            'instructions' => [
                'en' => 'Please complete all sections of this assessment. Your responses will remain confidential.',
                'ar' => 'يرجى إكمال جميع أقسام هذا التقييم. ستبقى إجاباتك سرية.',
            ],
            'status' => 'draft',
            'show_results_to_participant' => false,
        ]);
        $hrReview->tests()->attach([
            $burnout->id => ['sort_order' => 1],
            $jobSat->id => ['sort_order' => 2],
        ]);

        // Link 1: Active well-being assessment for HR department
        AssessmentLink::create([
            'assessment_id' => $wellbeing->id,
            'created_by' => $sarah->id,
            'title' => 'HR Department - Q1 2026',
            'starts_at' => now()->subDays(10),
            'expires_at' => now()->addDays(20),
            'max_participants' => 50,
            'is_active' => true,
            'password' => null,
            'collect_name' => true,
            'collect_email' => true,
            'collect_company' => true,
            'collect_job_title' => true,
            'collect_age' => true,
            'collect_gender' => true,
            'collect_phone' => false,
            'welcome_message' => [
                'en' => 'Welcome to the employee well-being assessment. Your responses are confidential and will be used to improve workplace well-being.',
                'ar' => 'مرحباً بك في تقييم رفاهية الموظفين. ردودك سرية وستُستخدم لتحسين الرفاهية في مكان العمل.',
            ],
            'completion_message' => [
                'en' => 'Thank you for completing the assessment. Your input helps us create a better workplace.',
                'ar' => 'شكراً لإكمالك التقييم. مساهمتك تساعدنا في خلق بيئة عمل أفضل.',
            ],
        ]);

        // Link 2: Password-protected well-being assessment for research
        AssessmentLink::create([
            'assessment_id' => $wellbeing->id,
            'created_by' => $omar->id,
            'title' => 'Research Study - Group A',
            'starts_at' => now()->subDays(5),
            'expires_at' => now()->addDays(30),
            'max_participants' => 100,
            'is_active' => true,
            'password' => 'study2026',
            'collect_name' => false,
            'collect_email' => false,
            'collect_company' => false,
            'collect_job_title' => false,
            'collect_age' => true,
            'collect_gender' => true,
            'collect_phone' => false,
            'custom_fields' => [
                [
                    'key' => 'education_level',
                    'label' => ['en' => 'Education Level', 'ar' => 'المستوى التعليمي'],
                    'type' => 'select',
                    'required' => true,
                ],
            ],
            'welcome_message' => [
                'en' => 'Thank you for participating in our research study. All responses are anonymous.',
                'ar' => 'شكراً لمشاركتك في دراستنا البحثية. جميع الردود مجهولة.',
            ],
            'completion_message' => [
                'en' => 'Thank you for your participation. Your contribution to this research is invaluable.',
                'ar' => 'شكراً على مشاركتك. مساهمتك في هذا البحث لا تقدر بثمن.',
            ],
        ]);

        // Link 3: Expired well-being link
        AssessmentLink::create([
            'assessment_id' => $wellbeing->id,
            'created_by' => $sarah->id,
            'title' => 'Engineering Team - Dec 2025',
            'starts_at' => now()->subMonths(3),
            'expires_at' => now()->subMonth(),
            'max_participants' => 30,
            'is_active' => true,
            'collect_name' => true,
            'collect_email' => true,
            'collect_company' => false,
            'collect_job_title' => false,
            'collect_age' => false,
            'collect_gender' => false,
            'collect_phone' => false,
        ]);

        // Link 4: Deactivated link
        AssessmentLink::create([
            'assessment_id' => $wellbeing->id,
            'created_by' => $omar->id,
            'title' => 'Pilot Test - Cancelled',
            'starts_at' => null,
            'expires_at' => null,
            'max_participants' => null,
            'is_active' => false,
            'collect_name' => true,
            'collect_email' => false,
            'collect_company' => false,
            'collect_job_title' => false,
            'collect_age' => false,
            'collect_gender' => false,
            'collect_phone' => false,
        ]);
    }
}
