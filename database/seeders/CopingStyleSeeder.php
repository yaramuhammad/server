<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class CopingStyleSeeder extends Seeder
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
                'en' => 'Work Stress Coping Styles',
                'ar' => 'أساليب مواجهة ضغوط العمل',
            ],
            'description' => [
                'en' => 'A questionnaire measuring three coping styles: Task-Focused Coping, Emotion-Focused Coping, and Avoidant Coping.',
                'ar' => 'استبيان يقيس ثلاثة أساليب للمواجهة: المواجهة المركزة على المهمة، المواجهة المركزة على الانفعال، والمواجهة التجنبية.',
            ],
            'instructions' => [
                'en' => 'Below are statements describing how people cope with stressful situations. Rate the extent to which each statement applies to you: 1 = Does not apply at all, 5 = Applies completely. There are no right or wrong answers.',
                'ar' => 'فيما يلي مجموعة من العبارات التي تصف كيفية تعامل الأشخاص مع المواقف الضاغطة. حدد مدى انطباق كل عبارة عليك: 1 = لا تنطبق مطلقاً، 5 = تنطبق تماماً. لا توجد إجابات صحيحة أو خاطئة.',
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
                ['key' => 'task_focused', 'label' => ['en' => 'Task-Focused Coping', 'ar' => 'المواجهة المركزة على المهمة'], 'interpretation' => $interp],
                ['key' => 'emotion_focused', 'label' => ['en' => 'Emotion-Focused Coping', 'ar' => 'المواجهة المركزة على الانفعال'], 'interpretation' => $interp],
                ['key' => 'avoidant', 'label' => ['en' => 'Avoidant Coping', 'ar' => 'المواجهة التجنبية'], 'interpretation' => $interp],
            ]],
            'randomize_questions' => false,
            'chart_type' => 'pie',
        ]);

        $questions = [
            // ===== Task-Focused Coping (المواجهة المركزة على المهمة) =====
            ['en' => 'I concentrate my efforts on doing something about it', 'ar' => 'أركز جهودي على فعل شيء حيال المشكلة', 'cat' => 'task_focused'],
            ['en' => 'I take additional action to try to get rid of the problem', 'ar' => 'أتخذ إجراءات إضافية لمحاولة التخلص من المشكلة', 'cat' => 'task_focused'],
            ['en' => 'I make a plan of action', 'ar' => 'أضع خطة عمل', 'cat' => 'task_focused'],
            ['en' => 'I try to come up with a strategy about what to do', 'ar' => 'أحاول وضع استراتيجية لما يجب فعله', 'cat' => 'task_focused'],
            ['en' => 'I focus on dealing with this problem, and if necessary let other things slide a little', 'ar' => 'أركز على التعامل مع هذه المشكلة، وإذا لزم الأمر أترك الأمور الأخرى تتراجع قليلاً', 'cat' => 'task_focused'],
            ['en' => 'I put aside other activities in order to concentrate on this', 'ar' => 'أضع الأنشطة الأخرى جانباً من أجل التركيز على هذا الأمر', 'cat' => 'task_focused'],
            ['en' => 'I hold off doing anything about it until the situation permits', 'ar' => 'أؤجل فعل أي شيء حيال الأمر حتى يسمح الوضع بذلك', 'cat' => 'task_focused'],
            ['en' => 'I force myself to wait for the right time to do something', 'ar' => 'أجبر نفسي على انتظار الوقت المناسب للقيام بشيء ما', 'cat' => 'task_focused'],
            ['en' => 'I try to get advice from someone about what to do', 'ar' => 'أحاول الحصول على نصيحة من شخص ما حول ما يجب فعله', 'cat' => 'task_focused'],
            ['en' => 'I ask people who have had similar experiences what they did', 'ar' => 'أسأل أشخاصاً مروا بتجارب مماثلة عما فعلوه', 'cat' => 'task_focused'],

            // ===== Emotion-Focused Coping (المواجهة المركزة على الانفعال) =====
            ['en' => 'I put my trust in God', 'ar' => 'أضع ثقتي في الله', 'cat' => 'emotion_focused'],
            ['en' => 'I seek God\'s help', 'ar' => 'أطلب العون من الله', 'cat' => 'emotion_focused'],
            ['en' => 'I get upset and let my emotions out', 'ar' => 'أنزعج وأفرغ مشاعري', 'cat' => 'emotion_focused'],
            ['en' => 'I let my feelings out', 'ar' => 'أعبّر عن مشاعري', 'cat' => 'emotion_focused'],
            ['en' => 'I make jokes about it', 'ar' => 'أطلق النكات حول الموضوع', 'cat' => 'emotion_focused'],
            ['en' => 'I try to get emotional support from friends and relatives', 'ar' => 'أحاول الحصول على دعم عاطفي من الأصدقاء والأقارب', 'cat' => 'emotion_focused'],
            ['en' => 'I talk to someone about how I feel', 'ar' => 'أتحدث مع شخص ما عن شعوري', 'cat' => 'emotion_focused'],
            ['en' => 'I try to see it in a different light, to make it seem more positive', 'ar' => 'أحاول رؤية الأمر من زاوية مختلفة لجعله يبدو أكثر إيجابية', 'cat' => 'emotion_focused'],
            ['en' => 'I look for something good in what is happening', 'ar' => 'أبحث عن شيء جيد فيما يحدث', 'cat' => 'emotion_focused'],
            ['en' => 'I learn to live with it', 'ar' => 'أتعلم التعايش مع الأمر', 'cat' => 'emotion_focused'],

            // ===== Avoidant Coping (المواجهة التجنبية) =====
            ['en' => 'I refuse to believe that it happened', 'ar' => 'أرفض تصديق أن ذلك قد حدث', 'cat' => 'avoidant'],
            ['en' => 'I pretend that it hasn\'t really happened', 'ar' => 'أتظاهر بأن ذلك لم يحدث فعلاً', 'cat' => 'avoidant'],
            ['en' => 'I use alcohol or drugs to make myself feel better', 'ar' => 'أستخدم الكحول أو المخدرات لأشعر بتحسن', 'cat' => 'avoidant'],
            ['en' => 'I try to lose myself for a while by drinking alcohol or taking drugs', 'ar' => 'أحاول أن أنسى نفسي لفترة بتناول الكحول أو المخدرات', 'cat' => 'avoidant'],
            ['en' => 'I keep others from knowing how bad things are', 'ar' => 'أمنع الآخرين من معرفة مدى سوء الأمور', 'cat' => 'avoidant'],
            ['en' => 'I wish that the situation would go away or somehow be over with', 'ar' => 'أتمنى أن يختفي الموقف أو ينتهي بطريقة ما', 'cat' => 'avoidant'],
            ['en' => 'I make light of the situation, I refuse to get too serious about it', 'ar' => 'أستخف بالموقف وأرفض أن آخذه بجدية مفرطة', 'cat' => 'avoidant'],
            ['en' => 'I go on as if nothing had happened', 'ar' => 'أستمر كما لو أن شيئاً لم يحدث', 'cat' => 'avoidant'],
            ['en' => 'I turn to work or other substitute activities to take my mind off things', 'ar' => 'ألجأ إلى العمل أو أنشطة بديلة لإلهاء ذهني عن الأمور', 'cat' => 'avoidant'],
            ['en' => 'I try to keep my feelings to myself', 'ar' => 'أحاول الاحتفاظ بمشاعري لنفسي', 'cat' => 'avoidant'],
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

        $this->command->info("Created 30-Item Coping Style Questionnaire with {$test->questions()->count()} questions across 3 categories.");
    }
}
