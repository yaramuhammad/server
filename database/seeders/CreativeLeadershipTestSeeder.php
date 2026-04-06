<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class CreativeLeadershipTestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $test = Test::create([
            'user_id' => $admin->id,
            'title' => [
                'en' => 'Situational Creative Leadership Scale',
                'ar' => 'مقياس القيادة الإبداعية الموقفي',
            ],
            'description' => [
                'en' => 'A situational judgment test measuring creative leadership traits across 8 dimensions.',
                'ar' => 'مقياس خصال القائد المبدع - اختبار حكم موقفي يقيس سمات القيادة الإبداعية عبر 8 أبعاد.',
            ],
            'instructions' => [
                'en' => 'Read each situation carefully, then choose: "What action is closest to what you would actually do in this situation?" All options are realistic — choose the closest to you, not the "theoretically best".',
                'ar' => 'اقرأ كل موقف جيدًا، ثم اختر: "ما التصرف الأقرب لما ستقوم به فعليًا في هذا الموقف؟" جميع البدائل واقعية — اختر الأقرب لك وليس "الأفضل نظريًا".',
            ],
            'status' => 'published',
            'scale_config' => ['min' => 1, 'max' => 4],
            'scoring_type' => 'category',
            'scoring_config' => $this->getScoringConfig(),
            'randomize_questions' => false,
        ]);

        foreach ($this->getQuestions() as $index => $q) {
            Question::create([
                'test_id' => $test->id,
                'text' => $q['text'],
                'sort_order' => $index + 1,
                'is_reverse_scored' => false,
                'is_required' => true,
                'category_key' => $q['category_key'],
                'weight' => 1.00,
                'scale_override' => [
                    'min' => 1,
                    'max' => 4,
                    'labels' => $q['labels'],
                    'score_map' => $q['score_map'],
                ],
            ]);
        }

        $this->command->info("Created Creative Leadership SJT test with {$test->questions()->count()} questions.");
    }

    private function getScoringConfig(): array
    {
        $interp = [
            ['min' => 0, 'max' => 49, 'label' => ['en' => 'Low', 'ar' => 'منخفض']],
            ['min' => 50, 'max' => 74, 'label' => ['en' => 'Moderate', 'ar' => 'متوسط']],
            ['min' => 75, 'max' => 100, 'label' => ['en' => 'High', 'ar' => 'مرتفع']],
        ];

        return ['categories' => [
            ['key' => 'fluency', 'label' => ['en' => 'Intellectual Fluency', 'ar' => 'الطلاقة الفكرية'], 'interpretation' => $interp],
            ['key' => 'flexibility', 'label' => ['en' => 'Intellectual Flexibility', 'ar' => 'المرونة الفكرية'], 'interpretation' => $interp],
            ['key' => 'originality', 'label' => ['en' => 'Originality', 'ar' => 'الأصالة'], 'interpretation' => $interp],
            ['key' => 'problem_sensitivity', 'label' => ['en' => 'Problem Sensitivity', 'ar' => 'الحساسية للمشكلات'], 'interpretation' => $interp],
            ['key' => 'elaboration', 'label' => ['en' => 'Elaboration', 'ar' => 'الإثراء والتفصيل'], 'interpretation' => $interp],
            ['key' => 'initiative', 'label' => ['en' => 'Initiative & Risk-taking', 'ar' => 'المبادرة والمخاطرة'], 'interpretation' => $interp],
            ['key' => 'creativity_support', 'label' => ['en' => 'Supporting Creativity', 'ar' => 'دعم الإبداع لدى الآخرين'], 'interpretation' => $interp],
            ['key' => 'creative_problem_solving', 'label' => ['en' => 'Creative Problem Solving', 'ar' => 'حل المشكلات الإبداعي'], 'interpretation' => $interp],
        ]];
    }

    /**
     * Helper: create bilingual label
     */
    private static function bl(string $en, string $ar): array
    {
        return ['en' => $en, 'ar' => $ar];
    }

    private function getQuestions(): array
    {
        return [
            // === الطلاقة الفكرية (Intellectual Fluency) Q1-5 ===
            [
                'text' => self::bl('In a meeting to solve a recurring production problem, time is limited and the team awaits your direction:', 'في اجتماع لحل مشكلة إنتاجية متكررة، الوقت محدود والفريق ينتظر توجيهك:'),
                'category_key' => 'fluency',
                'labels' => [
                    '1' => self::bl('Present a general framework and ask the team to complete it', 'أطرح إطارًا عامًا للحل وأطلب من الفريق استكماله'),
                    '2' => self::bl('Offer several quick alternatives then ask for evaluation', 'أقدم عدة بدائل سريعة ثم أطلب تقييمها'),
                    '3' => self::bl('Focus on analyzing the root cause first', 'أركز على تحليل سبب المشكلة أولًا'),
                    '4' => self::bl('Try a previously used solution with modifications', 'أطلب تجربة حل تم استخدامه سابقًا مع تعديلات'),
                ],
                'score_map' => ['1' => 2, '2' => 4, '3' => 1, '4' => 3],
            ],
            [
                'text' => self::bl('You are asked to present ideas for developing a service in a short time:', 'طُلب منك تقديم أفكار لتطوير خدمة خلال وقت قصير:'),
                'category_key' => 'fluency',
                'labels' => [
                    '1' => self::bl('Choose one idea and work on improving it', 'أختار فكرة واحدة وأعمل على تحسينها'),
                    '2' => self::bl('Gather team ideas and reorganize them', 'أستدعي أفكار الفريق وأعيد تنظيمها'),
                    '3' => self::bl('Propose diverse ideas even if incomplete', 'أطرح مجموعة أفكار متنوعة حتى لو لم تكتمل'),
                    '4' => self::bl('Start by analyzing customer needs first', 'أبدأ بتحليل احتياجات العملاء أولًا'),
                ],
                'score_map' => ['1' => 2, '2' => 1, '3' => 4, '4' => 3],
            ],
            [
                'text' => self::bl('In a brainstorming session, you noticed the discussion narrowing around one idea:', 'في جلسة عصف ذهني، لاحظت أن النقاش بدأ يضيق حول فكرة واحدة:'),
                'category_key' => 'fluency',
                'labels' => [
                    '1' => self::bl('Redirect the discussion to generate additional alternatives', 'أعيد توجيه النقاش لتوليد بدائل إضافية'),
                    '2' => self::bl('Allow the team to develop the current idea', 'أسمح للفريق بتطوير الفكرة الحالية'),
                    '3' => self::bl('Ask for evaluation of the proposed idea', 'أطلب تقييم الفكرة المطروحة'),
                    '4' => self::bl('Postpone discussion until more data is available', 'أؤجل النقاش لحين توفر بيانات أكثر'),
                ],
                'score_map' => ['1' => 4, '2' => 1, '3' => 3, '4' => 2],
            ],
            [
                'text' => self::bl('When facing a new situation you haven\'t encountered before:', 'عند مواجهة موقف جديد لم يمر عليك من قبل:'),
                'category_key' => 'fluency',
                'labels' => [
                    '1' => self::bl('Search for similar situations to learn from', 'أبحث عن مواقف مشابهة للاستفادة منها'),
                    '2' => self::bl('Start proposing multiple initial solutions', 'أبدأ بطرح أكثر من تصور مبدئي للحل'),
                    '3' => self::bl('Ask an expert for their opinion', 'أطلب رأي خبير في المجال'),
                    '4' => self::bl('Focus on understanding the situation before proposing solutions', 'أركز على فهم الموقف قبل اقتراح حلول'),
                ],
                'score_map' => ['1' => 1, '2' => 4, '3' => 2, '4' => 3],
            ],
            [
                'text' => self::bl('When presenting ideas to management:', 'في عرض أفكار أمام الإدارة:'),
                'category_key' => 'fluency',
                'labels' => [
                    '1' => self::bl('Present one well-supported option', 'أقدم خيارًا واحدًا مدعومًا جيدًا'),
                    '2' => self::bl('Present multiple options with pros and cons of each', 'أقدم عدة خيارات مع مميزات وقيود كل منها'),
                    '3' => self::bl('Focus on the safest option', 'أركز على الخيار الأكثر أمانًا'),
                    '4' => self::bl('Ask for management guidance before presenting', 'أطلب توجيه الإدارة قبل العرض'),
                ],
                'score_map' => ['1' => 2, '2' => 4, '3' => 1, '4' => 3],
            ],
            // === المرونة الفكرية (Intellectual Flexibility) Q6-10 ===
            [
                'text' => self::bl('You presented a plan, then new data appeared changing the picture:', 'قدمت خطة، ثم ظهرت بيانات جديدة تغير الصورة:'),
                'category_key' => 'flexibility',
                'labels' => [
                    '1' => self::bl('Stick to the plan with minor adjustments', 'أتمسك بالخطة مع بعض التعديلات الطفيفة'),
                    '2' => self::bl('Rebuild the plan based on new data', 'أعيد بناء الخطة وفق المعطيات الجديدة'),
                    '3' => self::bl('Wait for more data before modifying', 'أنتظر مزيدًا من البيانات قبل التعديل'),
                    '4' => self::bl('Ask the team for input on modifying the plan', 'أطلب رأي الفريق في تعديل الخطة'),
                ],
                'score_map' => ['1' => 1, '2' => 4, '3' => 2, '4' => 3],
            ],
            [
                'text' => self::bl('In a discussion, a member proposed a contrary but logical idea:', 'في نقاش، طرح أحد الأعضاء فكرة مخالفة لكنها منطقية:'),
                'category_key' => 'flexibility',
                'labels' => [
                    '1' => self::bl('Integrate the idea with my current vision', 'أدمج الفكرة مع رؤيتي الحالية'),
                    '2' => self::bl('Listen then return to my point of view', 'أستمع ثم أعود لوجهة نظري'),
                    '3' => self::bl('Ask the team to evaluate the idea', 'أطلب تقييم الفكرة من الفريق'),
                    '4' => self::bl('Postpone the decision', 'أؤجل الحسم'),
                ],
                'score_map' => ['1' => 4, '2' => 1, '3' => 2, '4' => 3],
            ],
            [
                'text' => self::bl('Work priorities changed suddenly:', 'تغيرت أولويات العمل بشكل مفاجئ:'),
                'category_key' => 'flexibility',
                'labels' => [
                    '1' => self::bl('Quickly re-prioritize', 'أعيد ترتيب الأولويات سريعًا'),
                    '2' => self::bl('Continue with current plan until things clarify', 'أستمر في الخطة الحالية حتى تتضح الصورة'),
                    '3' => self::bl('Ask for management guidance', 'أطلب توجيهًا إداريًا'),
                    '4' => self::bl('Focus only on current tasks', 'أركز على المهام الحالية فقط'),
                ],
                'score_map' => ['1' => 4, '2' => 2, '3' => 3, '4' => 1],
            ],
            [
                'text' => self::bl('Your team is used to a certain approach, but you see a better alternative:', 'فريقك معتاد على أسلوب معين، لكنك ترى بديلًا أفضل:'),
                'category_key' => 'flexibility',
                'labels' => [
                    '1' => self::bl('Apply the new approach gradually', 'أطبق الأسلوب الجديد تدريجيًا'),
                    '2' => self::bl('Stick with the familiar approach', 'ألتزم بالأسلوب المعتاد'),
                    '3' => self::bl('Open the topic for discussion', 'أطرح الموضوع للنقاش'),
                    '4' => self::bl('Postpone the change', 'أؤجل التغيير'),
                ],
                'score_map' => ['1' => 4, '2' => 1, '3' => 2, '4' => 3],
            ],
            [
                'text' => self::bl('In a situation requiring a change in work method:', 'في موقف يتطلب تغيير طريقة العمل:'),
                'category_key' => 'flexibility',
                'labels' => [
                    '1' => self::bl('Adjust my approach according to the situation', 'أعدل أسلوبي حسب الموقف'),
                    '2' => self::bl('Stick with my usual approach', 'ألتزم بأسلوبي المعتاد'),
                    '3' => self::bl('Seek external support', 'أطلب دعم خارجي'),
                    '4' => self::bl('Focus on minimizing risks', 'أركز على تقليل المخاطر'),
                ],
                'score_map' => ['1' => 4, '2' => 1, '3' => 3, '4' => 2],
            ],
            // === الأصالة (Originality) Q11-15 ===
            [
                'text' => self::bl('You are asked to solve a traditional problem:', 'طُلب منك حل لمشكلة تقليدية:'),
                'category_key' => 'originality',
                'labels' => [
                    '1' => self::bl('Use a proven solution', 'أستخدم حلًا مجربًا'),
                    '2' => self::bl('Offer an unconventional approach', 'أقدم مقاربة غير معتادة'),
                    '3' => self::bl('Develop the traditional solution', 'أطور الحل التقليدي'),
                    '4' => self::bl('Consult the team', 'أستشير الفريق'),
                ],
                'score_map' => ['1' => 2, '2' => 4, '3' => 3, '4' => 1],
            ],
            [
                'text' => self::bl('In a new project:', 'في مشروع جديد:'),
                'category_key' => 'originality',
                'labels' => [
                    '1' => self::bl('Rely on previous models', 'أستند إلى نماذج سابقة'),
                    '2' => self::bl('Propose a different idea from the norm', 'أطرح فكرة مختلفة عن المعتاد'),
                    '3' => self::bl('Modify an existing template', 'أعدل نموذجًا جاهزًا'),
                    '4' => self::bl('Ask for successful examples', 'أطلب أمثلة ناجحة'),
                ],
                'score_map' => ['1' => 2, '2' => 4, '3' => 1, '4' => 3],
            ],
            [
                'text' => self::bl('When presenting an idea to management:', 'عند تقديم فكرة للإدارة:'),
                'category_key' => 'originality',
                'labels' => [
                    '1' => self::bl('Present a familiar idea to ensure acceptance', 'أقدم فكرة مألوفة لضمان القبول'),
                    '2' => self::bl('Present a distinctive idea with feasibility explanation', 'أطرح فكرة مميزة مع توضيح جدواها'),
                    '3' => self::bl('Combine multiple traditional ideas', 'أدمج أكثر من فكرة تقليدية'),
                    '4' => self::bl('Postpone the presentation', 'أؤجل الطرح'),
                ],
                'score_map' => ['1' => 2, '2' => 4, '3' => 3, '4' => 1],
            ],
            [
                'text' => self::bl('In solving a recurring problem:', 'في حل مشكلة متكررة:'),
                'category_key' => 'originality',
                'labels' => [
                    '1' => self::bl('Repeat the previous solution', 'أكرر الحل السابق'),
                    '2' => self::bl('Offer a new approach', 'أقدم معالجة جديدة'),
                    '3' => self::bl('Modify the previous solution', 'أعدل الحل السابق'),
                    '4' => self::bl('Request external intervention', 'أطلب تدخل خارجي'),
                ],
                'score_map' => ['1' => 1, '2' => 4, '3' => 3, '4' => 2],
            ],
            [
                'text' => self::bl('When evaluating a new idea:', 'عند تقييم فكرة جديدة:'),
                'category_key' => 'originality',
                'labels' => [
                    '1' => self::bl('Compare it to current standards', 'أقارنها بالمعايير الحالية'),
                    '2' => self::bl('Look for its uniqueness and difference', 'أبحث عن تميزها واختلافها'),
                    '3' => self::bl('Ask the team for their opinion', 'أطلب رأي الفريق'),
                    '4' => self::bl('Focus on the risks', 'أركز على المخاطر'),
                ],
                'score_map' => ['1' => 2, '2' => 4, '3' => 3, '4' => 1],
            ],
            // === الحساسية للمشكلات (Problem Sensitivity) Q16-20 ===
            [
                'text' => self::bl('You noticed a small indicator that may signal an upcoming problem:', 'لاحظت مؤشرًا بسيطًا قد يدل على مشكلة قادمة:'),
                'category_key' => 'problem_sensitivity',
                'labels' => [
                    '1' => self::bl('Monitor the situation for a while', 'أراقب الوضع لفترة'),
                    '2' => self::bl('Start analyzing the cause early', 'أبدأ تحليل السبب مبكرًا'),
                    '3' => self::bl('Inform management', 'أبلغ الإدارة'),
                    '4' => self::bl('Focus on current work', 'أركز على العمل الحالي'),
                ],
                'score_map' => ['1' => 3, '2' => 4, '3' => 2, '4' => 1],
            ],
            [
                'text' => self::bl('Unclear indicators appeared in the performance report:', 'في تقرير الأداء ظهرت إشارات غير واضحة:'),
                'category_key' => 'problem_sensitivity',
                'labels' => [
                    '1' => self::bl('Request additional data', 'أطلب بيانات إضافية'),
                    '2' => self::bl('Analyze current indicators deeply', 'أحلل المؤشرات الحالية بعمق'),
                    '3' => self::bl('Follow up later', 'أتابع لاحقًا'),
                    '4' => self::bl('Focus on clear indicators', 'أركز على المؤشرات الواضحة'),
                ],
                'score_map' => ['1' => 2, '2' => 4, '3' => 1, '4' => 3],
            ],
            [
                'text' => self::bl('One of the processes appears to be running normally:', 'أحد العمليات تسير بشكل طبيعي ظاهريًا:'),
                'category_key' => 'problem_sensitivity',
                'labels' => [
                    '1' => self::bl('Continue with regular monitoring', 'أستمر في المتابعة المعتادة'),
                    '2' => self::bl('Search for hidden potential issues', 'أبحث عن احتمالات خلل خفي'),
                    '3' => self::bl('Request an external review', 'أطلب مراجعة خارجية'),
                    '4' => self::bl('Focus on results', 'أركز على النتائج'),
                ],
                'score_map' => ['1' => 2, '2' => 4, '3' => 1, '4' => 3],
            ],
            [
                'text' => self::bl('In a long-term project:', 'في مشروع طويل:'),
                'category_key' => 'problem_sensitivity',
                'labels' => [
                    '1' => self::bl('Review final results', 'أراجع النتائج النهائية'),
                    '2' => self::bl('Continuously monitor early indicators', 'أراقب المؤشرات المبكرة باستمرار'),
                    '3' => self::bl('Request periodic reports', 'أطلب تقارير دورية'),
                    '4' => self::bl('Focus on execution', 'أركز على التنفيذ'),
                ],
                'score_map' => ['1' => 1, '2' => 4, '3' => 2, '4' => 3],
            ],
            [
                'text' => self::bl('When analyzing a problem:', 'عند تحليل مشكلة:'),
                'category_key' => 'problem_sensitivity',
                'labels' => [
                    '1' => self::bl('Deal with symptoms', 'أتعامل مع الأعراض'),
                    '2' => self::bl('Search for root causes', 'أبحث عن الأسباب الجذرية'),
                    '3' => self::bl('Apply a quick fix', 'أطبق حلًا سريعًا'),
                    '4' => self::bl('Request support', 'أطلب دعمًا'),
                ],
                'score_map' => ['1' => 1, '2' => 4, '3' => 2, '4' => 3],
            ],
            // === الإثراء والتفصيل (Elaboration) Q21-25 ===
            [
                'text' => self::bl('A good but general idea was presented that needs an execution plan:', 'عُرضت فكرة جيدة لكنها ما زالت عامة وتحتاج تحويلها لخطة تنفيذ:'),
                'category_key' => 'elaboration',
                'labels' => [
                    '1' => self::bl('Start executing and adjust along the way', 'أبدأ التنفيذ مباشرة وأعدل أثناء العمل'),
                    '2' => self::bl('Add clear details and steps before executing', 'أضيف تفاصيل وخطوات واضحة قبل التنفيذ'),
                    '3' => self::bl('Let the team determine the details', 'أترك الفريق يحدد التفاصيل'),
                    '4' => self::bl('Focus only on the overall goal', 'أركز على الهدف العام فقط'),
                ],
                'score_map' => ['1' => 2, '2' => 4, '3' => 3, '4' => 1],
            ],
            [
                'text' => self::bl('When presenting a project to management:', 'في عرض مشروع أمام الإدارة:'),
                'category_key' => 'elaboration',
                'labels' => [
                    '1' => self::bl('Present the idea briefly', 'أقدم الفكرة بشكل مختصر'),
                    '2' => self::bl('Present the idea with practical examples and details', 'أقدم الفكرة مع أمثلة تطبيقية وتفاصيل'),
                    '3' => self::bl('Focus on expected results', 'أركز على النتائج المتوقعة'),
                    '4' => self::bl('Let questions determine the details', 'أترك الأسئلة تحدد التفاصيل'),
                ],
                'score_map' => ['1' => 2, '2' => 4, '3' => 3, '4' => 1],
            ],
            [
                'text' => self::bl('When developing an innovative idea:', 'عند تطوير فكرة مبتكرة:'),
                'category_key' => 'elaboration',
                'labels' => [
                    '1' => self::bl('Explain it generally', 'أشرحها بشكل عام'),
                    '2' => self::bl('Turn it into a clear action plan', 'أحولها إلى خطة عمل واضحة'),
                    '3' => self::bl('Ask the team to develop it', 'أطلب من الفريق تطويرها'),
                    '4' => self::bl('Start experimenting directly', 'أبدأ بتجربتها مباشرة'),
                ],
                'score_map' => ['1' => 2, '2' => 4, '3' => 3, '4' => 1],
            ],
            [
                'text' => self::bl('When following up on idea execution:', 'في متابعة تنفيذ فكرة:'),
                'category_key' => 'elaboration',
                'labels' => [
                    '1' => self::bl('Review results only', 'أراجع النتائج فقط'),
                    '2' => self::bl('Follow precise details of each phase', 'أتابع التفاصيل الدقيقة لكل مرحلة'),
                    '3' => self::bl('Leave execution to the team', 'أترك التنفيذ للفريق'),
                    '4' => self::bl('Focus on timeline compliance', 'أركز على الالتزام بالوقت'),
                ],
                'score_map' => ['1' => 1, '2' => 4, '3' => 2, '4' => 3],
            ],
            [
                'text' => self::bl('When improving an existing work procedure:', 'عند تحسين إجراء عمل قائم:'),
                'category_key' => 'elaboration',
                'labels' => [
                    '1' => self::bl('Offer a general suggestion', 'أقدم اقتراحًا عامًا'),
                    '2' => self::bl('Add specific, actionable improvements', 'أضيف تحسينات محددة وقابلة للتطبيق'),
                    '3' => self::bl('Leave it as is', 'أتركه كما هو'),
                    '4' => self::bl('Request management evaluation', 'أطلب تقييم الإدارة'),
                ],
                'score_map' => ['1' => 2, '2' => 4, '3' => 3, '4' => 1],
            ],
            // === المبادرة والمخاطرة (Initiative & Risk-taking) Q26-30 ===
            [
                'text' => self::bl('An opportunity appeared to try a new, not fully guaranteed approach:', 'ظهرت فرصة لتجربة أسلوب جديد غير مضمون بالكامل:'),
                'category_key' => 'initiative',
                'labels' => [
                    '1' => self::bl('Start the experiment in a calculated way', 'أبدأ التجربة بشكل مدروس'),
                    '2' => self::bl('Wait for others\' results', 'أنتظر نتائج تجارب الآخرين'),
                    '3' => self::bl('Apply the usual approach', 'أطبق الأسلوب المعتاد'),
                    '4' => self::bl('Request detailed approval before any step', 'أطلب موافقة تفصيلية قبل أي خطوة'),
                ],
                'score_map' => ['1' => 4, '2' => 2, '3' => 1, '4' => 3],
            ],
            [
                'text' => self::bl('In a situation requiring quick action:', 'في موقف يتطلب تحركًا سريعًا:'),
                'category_key' => 'initiative',
                'labels' => [
                    '1' => self::bl('Take appropriate action proactively', 'أبادر باتخاذ إجراء مناسب'),
                    '2' => self::bl('Wait for official guidance', 'أنتظر توجيهًا رسميًا'),
                    '3' => self::bl('Gather more information', 'أجمع معلومات أكثر'),
                    '4' => self::bl('Postpone the decision', 'أؤجل القرار'),
                ],
                'score_map' => ['1' => 4, '2' => 2, '3' => 3, '4' => 1],
            ],
            [
                'text' => self::bl('When a new idea is proposed by your team:', 'عند اقتراح فكرة جديدة من فريقك:'),
                'category_key' => 'initiative',
                'labels' => [
                    '1' => self::bl('Encourage trying the idea in an organized way', 'أشجع تجربة الفكرة بشكل منظم'),
                    '2' => self::bl('Request a full feasibility study first', 'أطلب دراسة جدوى كاملة أولًا'),
                    '3' => self::bl('Postpone the experiment', 'أؤجل التجربة'),
                    '4' => self::bl('Stick with current methods', 'ألتزم بالأساليب الحالية'),
                ],
                'score_map' => ['1' => 4, '2' => 3, '3' => 2, '4' => 1],
            ],
            [
                'text' => self::bl('In a new project with unclear results:', 'في مشروع جديد غير واضح النتائج:'),
                'category_key' => 'initiative',
                'labels' => [
                    '1' => self::bl('Start with small experimental steps', 'أبدأ بخطوات صغيرة تجريبية'),
                    '2' => self::bl('Wait for clarity', 'أنتظر وضوح الصورة'),
                    '3' => self::bl('Request guidance', 'أطلب توجيهًا'),
                    '4' => self::bl('Focus on current projects', 'أركز على المشاريع الحالية'),
                ],
                'score_map' => ['1' => 4, '2' => 2, '3' => 3, '4' => 1],
            ],
            [
                'text' => self::bl('When needing to make an unconventional decision:', 'عند الحاجة لاتخاذ قرار غير تقليدي:'),
                'category_key' => 'initiative',
                'labels' => [
                    '1' => self::bl('Make the decision and accept consequences', 'أتخذ القرار مع تحمل نتائجه'),
                    '2' => self::bl('Look for conventional alternatives', 'أبحث عن بدائل تقليدية'),
                    '3' => self::bl('Postpone the decision', 'أؤجل القرار'),
                    '4' => self::bl('Request support', 'أطلب دعمًا'),
                ],
                'score_map' => ['1' => 4, '2' => 3, '3' => 2, '4' => 1],
            ],
            // === دعم الإبداع (Supporting Creativity) Q31-35 ===
            [
                'text' => self::bl('An employee proposed an unusual idea:', 'طرح أحد الموظفين فكرة غير مألوفة:'),
                'category_key' => 'creativity_support',
                'labels' => [
                    '1' => self::bl('Explore and develop the idea with them', 'أستكشف الفكرة معه وأطورها'),
                    '2' => self::bl('Ask the team to evaluate it', 'أطلب تقييمها من الفريق'),
                    '3' => self::bl('Focus on realistic ideas', 'أركز على الأفكار الواقعية'),
                    '4' => self::bl('Postpone discussing it', 'أؤجل مناقشتها'),
                ],
                'score_map' => ['1' => 4, '2' => 3, '3' => 2, '4' => 1],
            ],
            [
                'text' => self::bl('In a meeting, a member is hesitant to share ideas:', 'في اجتماع، أحد الأعضاء متردد في طرح أفكاره:'),
                'category_key' => 'creativity_support',
                'labels' => [
                    '1' => self::bl('Encourage them to participate', 'أشجعه على المشاركة'),
                    '2' => self::bl('Continue the discussion', 'أتابع النقاش'),
                    '3' => self::bl('Ask them later', 'أطلب منه لاحقًا'),
                    '4' => self::bl('Focus on time', 'أركز على الوقت'),
                ],
                'score_map' => ['1' => 4, '2' => 2, '3' => 3, '4' => 1],
            ],
            [
                'text' => self::bl('The team follows a traditional work approach:', 'الفريق يلتزم بأسلوب تقليدي في العمل:'),
                'category_key' => 'creativity_support',
                'labels' => [
                    '1' => self::bl('Open space for new ideas', 'أفتح المجال لأفكار جديدة'),
                    '2' => self::bl('Stick with the current approach', 'ألتزم بالأسلوب الحالي'),
                    '3' => self::bl('Request performance evaluation', 'أطلب تقييم الأداء'),
                    '4' => self::bl('Focus on results', 'أركز على النتائج'),
                ],
                'score_map' => ['1' => 4, '2' => 2, '3' => 3, '4' => 1],
            ],
            [
                'text' => self::bl('A good idea was submitted by a team member:', 'تم تقديم فكرة جيدة من أحد الأفراد:'),
                'category_key' => 'creativity_support',
                'labels' => [
                    '1' => self::bl('Provide constructive feedback and encourage them', 'أقدم تغذية راجعة بناءة وأشجعه'),
                    '2' => self::bl('Just approve it', 'أكتفي بالموافقة'),
                    '3' => self::bl('Ask for further development', 'أطلب تطويرها'),
                    '4' => self::bl('Postpone the decision', 'أؤجل القرار'),
                ],
                'score_map' => ['1' => 4, '2' => 3, '3' => 2, '4' => 1],
            ],
            [
                'text' => self::bl('In solving a group problem:', 'في حل مشكلة جماعية:'),
                'category_key' => 'creativity_support',
                'labels' => [
                    '1' => self::bl('Involve the team in generating solutions', 'أشرك الفريق في توليد الحلول'),
                    '2' => self::bl('Determine the solution myself', 'أحدد الحل بنفسي'),
                    '3' => self::bl('Request reports', 'أطلب تقارير'),
                    '4' => self::bl('Focus on execution', 'أركز على التنفيذ'),
                ],
                'score_map' => ['1' => 4, '2' => 2, '3' => 3, '4' => 1],
            ],
            // === حل المشكلات الإبداعي (Creative Problem Solving) Q36-40 ===
            [
                'text' => self::bl('In a complex multi-cause problem:', 'في مشكلة معقدة متعددة الأسباب:'),
                'category_key' => 'creative_problem_solving',
                'labels' => [
                    '1' => self::bl('Connect multiple ideas to reach a solution', 'أربط بين عدة أفكار للوصول لحل'),
                    '2' => self::bl('Use a ready-made solution', 'أستخدم حلًا جاهزًا'),
                    '3' => self::bl('Ask for help', 'أطلب مساعدة'),
                    '4' => self::bl('Postpone the decision', 'أؤجل القرار'),
                ],
                'score_map' => ['1' => 4, '2' => 3, '3' => 2, '4' => 1],
            ],
            [
                'text' => self::bl('When analyzing a new situation:', 'عند تحليل موقف جديد:'),
                'category_key' => 'creative_problem_solving',
                'labels' => [
                    '1' => self::bl('Integrate multiple information sources to build a solution', 'أدمج معلومات متعددة لبناء حل'),
                    '2' => self::bl('Focus on one factor', 'أركز على عامل واحد'),
                    '3' => self::bl('Ask an expert', 'أطلب رأي خبير'),
                    '4' => self::bl('Postpone', 'أؤجل'),
                ],
                'score_map' => ['1' => 4, '2' => 2, '3' => 3, '4' => 1],
            ],
            [
                'text' => self::bl('When making an important decision:', 'في اتخاذ قرار مهم:'),
                'category_key' => 'creative_problem_solving',
                'labels' => [
                    '1' => self::bl('Balance multiple innovative alternatives', 'أوازن بين عدة بدائل مبتكرة'),
                    '2' => self::bl('Choose the easiest solution', 'أختار الحل الأسهل'),
                    '3' => self::bl('Ask management for their opinion', 'أطلب رأي الإدارة'),
                    '4' => self::bl('Postpone', 'أؤجل'),
                ],
                'score_map' => ['1' => 4, '2' => 2, '3' => 3, '4' => 1],
            ],
            [
                'text' => self::bl('After trying a solution that did not succeed:', 'بعد تجربة حل لم ينجح:'),
                'category_key' => 'creative_problem_solving',
                'labels' => [
                    '1' => self::bl('Modify and develop the solution', 'أعدل وأطور الحل'),
                    '2' => self::bl('Repeat the same attempt', 'أكرر نفس المحاولة'),
                    '3' => self::bl('Change direction completely', 'أغير الاتجاه بالكامل'),
                    '4' => self::bl('Postpone', 'أؤجل'),
                ],
                'score_map' => ['1' => 4, '2' => 3, '3' => 2, '4' => 1],
            ],
            [
                'text' => self::bl('When facing a new challenge:', 'عند مواجهة تحدٍ جديد:'),
                'category_key' => 'creative_problem_solving',
                'labels' => [
                    '1' => self::bl('Innovate a new approach', 'أبتكر مقاربة جديدة'),
                    '2' => self::bl('Use my previous experience', 'أستخدم خبرتي السابقة'),
                    '3' => self::bl('Request support', 'أطلب دعمًا'),
                    '4' => self::bl('Postpone', 'أؤجل'),
                ],
                'score_map' => ['1' => 4, '2' => 2, '3' => 3, '4' => 1],
            ],
        ];
    }
}
