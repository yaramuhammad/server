<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * "مقاييس المتحدث الرسمي" — Official Spokesperson Scales.
 *
 * Adds seven self-report scales (15 items each, all positively worded, Likert 1-5),
 * every scale split into three equal 5-item sub-dimensions. Each scale uses
 * `category` scoring (per-dimension percentage with Low / Moderate / High bands)
 * and a `column` chart — the clearest supported view for comparing three balanced
 * sub-competencies of one construct.
 *
 * Then bundles the seven into an assessment titled "مقاييس المتحدث الرسمي" with a
 * shareable link that stays open for one month and admits up to 50 participants.
 */
class OfficialSpokespersonScalesSeeder extends Seeder
{
    /** Shared 0-39 / 40-69 / 70-100 interpretation bands. */
    private array $interp;

    /** Shared 1-5 "Strongly Disagree → Strongly Agree" scale config. */
    private array $scaleConfig;

    public function run(): void
    {
        $admin = User::first();

        if (! $admin) {
            $this->command->error('No user found. Run UserSeeder first.');

            return;
        }

        $this->interp = [
            ['min' => 0,  'max' => 39,  'label' => ['en' => 'Low',      'ar' => 'منخفض']],
            ['min' => 40, 'max' => 69,  'label' => ['en' => 'Moderate', 'ar' => 'متوسط']],
            ['min' => 70, 'max' => 100, 'label' => ['en' => 'High',     'ar' => 'مرتفع']],
        ];

        $this->scaleConfig = [
            'min' => 1,
            'max' => 5,
            'labels' => [
                '1' => ['en' => 'Strongly Disagree', 'ar' => 'لا أوافق بشدة'],
                '2' => ['en' => 'Disagree',          'ar' => 'لا أوافق'],
                '3' => ['en' => 'Neutral',           'ar' => 'محايد'],
                '4' => ['en' => 'Agree',             'ar' => 'أوافق'],
                '5' => ['en' => 'Strongly Agree',    'ar' => 'أوافق بشدة'],
            ],
        ];

        $tests = [
            $this->spokespersonCredibility($admin),
            $this->publicSpeakingSelfEfficacy($admin),
            $this->publicSpeakingSkills($admin),
            $this->mediaHandlingCompetence($admin),
            $this->audienceOrientation($admin),
            $this->nonverbalCommunicationCompetence($admin),
            $this->communicationFlexibility($admin),
        ];

        $this->buildAssessment($admin, $tests);
    }

    // ------------------------------------------------------------------
    // Shared test builder
    // ------------------------------------------------------------------

    /**
     * @param  array<string,array{en:string,ar:string}>  $categories  key => label
     * @param  array<int,array{text:array{en:string,ar:string},cat:string}>  $questions  1-indexed
     */
    private function makeTest(
        User $admin,
        array $title,
        array $description,
        array $instructions,
        array $categories,
        array $questions,
    ): Test {
        $test = Test::create([
            'user_id' => $admin->id,
            'title' => $title,
            'description' => $description,
            'instructions' => $instructions,
            'status' => 'published',
            'scale_config' => $this->scaleConfig,
            'scoring_type' => 'category',
            'scoring_config' => [
                'categories' => array_map(
                    fn ($key, $label) => [
                        'key' => $key,
                        'label' => $label,
                        'interpretation' => $this->interp,
                    ],
                    array_keys($categories),
                    array_values($categories),
                ),
            ],
            'chart_type' => 'column',
            'randomize_questions' => false,
        ]);

        foreach ($questions as $sortOrder => $q) {
            Question::create([
                'test_id' => $test->id,
                'text' => $q['text'],
                'sort_order' => $sortOrder,
                'is_reverse_scored' => false,
                'is_required' => true,
                'category_key' => $q['cat'],
                'weight' => 1.00,
            ]);
        }

        $this->command->info("  ✓ {$title['en']} — {$test->questions()->count()} questions, 3 dimensions, column chart");

        return $test;
    }

    private function stdInstructions(string $arLead): array
    {
        return [
            'en' => 'Below are statements about your abilities. Indicate how much each statement applies to you: '
                . '1 = Strongly Disagree, 2 = Disagree, 3 = Neutral, 4 = Agree, 5 = Strongly Agree.',
            'ar' => $arLead . ' يرجى تحديد مدى انطباق كل عبارة عليك باستخدام المقياس الآتي: '
                . '1 = لا أوافق بشدة، 2 = لا أوافق، 3 = محايد، 4 = أوافق، 5 = أوافق بشدة.',
        ];
    }

    // ------------------------------------------------------------------
    // 1. مقياس مصداقية المتحدث الرسمي
    // ------------------------------------------------------------------
    private function spokespersonCredibility(User $admin): Test
    {
        return $this->makeTest(
            $admin,
            ['en' => 'Official Spokesperson Credibility Scale', 'ar' => 'مقياس مصداقية المتحدث الرسمي'],
            [
                'en' => 'A 15-item self-report scale measuring the perceived credibility of an official spokesperson across three dimensions: Expertise, Trustworthiness, and Professional Attractiveness/Presence.',
                'ar' => 'مقياس ذاتي التقرير من 15 بنداً يقيس مصداقية المتحدث الرسمي عبر ثلاثة أبعاد: الخبرة والكفاءة، الموثوقية والأمانة، والجاذبية والحضور المهني.',
            ],
            $this->stdInstructions('فيما يلي مجموعة من العبارات المتعلقة بقدراتك وصفاتك الشخصية بوصفك متحدثاً رسمياً باسم المنظمة.'),
            [
                'expertise' => ['en' => 'Expertise', 'ar' => 'الخبرة والكفاءة'],
                'trustworthiness' => ['en' => 'Trustworthiness', 'ar' => 'الموثوقية والأمانة'],
                'presence' => ['en' => 'Professional Attractiveness / Presence', 'ar' => 'الجاذبية والحضور المهني'],
            ],
            [
                1  => ['cat' => 'expertise', 'text' => ['en' => 'I have broad knowledge of the field I speak about.', 'ar' => 'أمتلك معرفة واسعة بالمجال الذي أتحدث عنه.']],
                2  => ['cat' => 'expertise', 'text' => ['en' => 'I have a high level of professional competence in the field I represent.', 'ar' => 'أتمتع بمستوى مرتفع من الكفاءة المهنية في المجال الذي أمثله.']],
                3  => ['cat' => 'expertise', 'text' => ['en' => 'I have enough experience in the topics I speak about.', 'ar' => 'أمتلك خبرة كافية في الموضوعات التي أتحدث عنها.']],
                4  => ['cat' => 'expertise', 'text' => ['en' => 'I can provide accurate and reliable information about the topics I address.', 'ar' => 'أستطيع تقديم معلومات دقيقة وموثوقة حول الموضوعات التي أتناولها.']],
                5  => ['cat' => 'expertise', 'text' => ['en' => 'I have the knowledge and experience needed to represent the organization before different audiences.', 'ar' => 'أمتلك المعرفة والخبرة اللازمتين لتمثيل المنظمة أمام مختلف الجماهير.']],
                6  => ['cat' => 'trustworthiness', 'text' => ['en' => 'I am keen to provide truthful information when speaking on behalf of the organization.', 'ar' => 'أحرص على تقديم معلومات صادقة عند الحديث باسم المنظمة.']],
                7  => ['cat' => 'trustworthiness', 'text' => ['en' => 'I commit to accuracy and objectivity when presenting information to the public.', 'ar' => 'ألتزم بالدقة والموضوعية عند عرض المعلومات للجمهور.']],
                8  => ['cat' => 'trustworthiness', 'text' => ['en' => 'I avoid providing information that could mislead the public.', 'ar' => 'أتجنب تقديم معلومات قد تؤدي إلى تضليل الجمهور.']],
                9  => ['cat' => 'trustworthiness', 'text' => ['en' => 'I handle sensitive information about the organization responsibly and honestly.', 'ar' => 'أتعامل مع المعلومات الحساسة المتعلقة بالمنظمة بمسؤولية وأمانة.']],
                10 => ['cat' => 'trustworthiness', 'text' => ['en' => 'I am keen to be trustworthy when representing the organization.', 'ar' => 'أحرص على أن أكون جديراً بالثقة عند تمثيل المنظمة.']],
                11 => ['cat' => 'presence', 'text' => ['en' => 'I have a professional presence suitable for representing the organization.', 'ar' => 'أتمتع بحضور مهني مناسب لتمثيل المنظمة.']],
                12 => ['cat' => 'presence', 'text' => ['en' => 'I can appear before the public in a way that reflects the organization\'s professionalism.', 'ar' => 'أستطيع الظهور أمام الجمهور بصورة تعكس احترافية المنظمة.']],
                13 => ['cat' => 'presence', 'text' => ['en' => 'I can capture the audience\'s attention while speaking.', 'ar' => 'أستطيع جذب انتباه الجمهور أثناء حديثي.']],
                14 => ['cat' => 'presence', 'text' => ['en' => 'I leave a positive impression on the audience when speaking on behalf of the organization.', 'ar' => 'أترك انطباعاً إيجابياً لدى الجمهور عند التحدث باسم المنظمة.']],
                15 => ['cat' => 'presence', 'text' => ['en' => 'I have a personal presence that enhances the organization\'s positive image.', 'ar' => 'أتمتع بحضور شخصي يعزز الصورة الإيجابية للمنظمة.']],
            ],
        );
    }

    // ------------------------------------------------------------------
    // 2. مقياس الفاعلية الذاتية في التحدث أمام الجمهور
    // ------------------------------------------------------------------
    private function publicSpeakingSelfEfficacy(User $admin): Test
    {
        return $this->makeTest(
            $admin,
            ['en' => 'Public Speaking Self-Efficacy Scale', 'ar' => 'مقياس الفاعلية الذاتية في التحدث أمام الجمهور'],
            [
                'en' => 'A 15-item self-report scale measuring confidence in public speaking across three dimensions: Preparation & Planning, Performance & Delivery, and Managing Difficult Situations.',
                'ar' => 'مقياس ذاتي التقرير من 15 بنداً يقيس الفاعلية الذاتية في التحدث أمام الجمهور عبر ثلاثة أبعاد: الاستعداد والتخطيط للتحدث، الأداء والتقديم أمام الجمهور، وإدارة المواقف الصعبة أثناء التحدث.',
            ],
            $this->stdInstructions('فيما يلي مجموعة من العبارات المتعلقة بقدرتك على التحدث أمام الجمهور.'),
            [
                'preparation' => ['en' => 'Preparation & Planning', 'ar' => 'الاستعداد والتخطيط للتحدث'],
                'delivery' => ['en' => 'Performance & Delivery', 'ar' => 'الأداء والتقديم أمام الجمهور'],
                'difficult_situations' => ['en' => 'Managing Difficult Situations', 'ar' => 'إدارة المواقف الصعبة أثناء التحدث'],
            ],
            [
                1  => ['cat' => 'preparation', 'text' => ['en' => 'I can prepare an organized oral presentation on a specific topic.', 'ar' => 'أستطيع إعداد عرض شفهي منظم حول موضوع محدد.']],
                2  => ['cat' => 'preparation', 'text' => ['en' => 'I can identify the key ideas to be covered during my talk.', 'ar' => 'أستطيع تحديد الأفكار الأساسية التي ينبغي تناولها أثناء حديثي.']],
                3  => ['cat' => 'preparation', 'text' => ['en' => 'I can organize my ideas so that my message is clear to the audience.', 'ar' => 'أستطيع تنظيم أفكاري بطريقة تجعل رسالتي واضحة للجمهور.']],
                4  => ['cat' => 'preparation', 'text' => ['en' => 'I can select the appropriate information for a specific audience.', 'ar' => 'أستطيع اختيار المعلومات المناسبة لجمهور محدد.']],
                5  => ['cat' => 'preparation', 'text' => ['en' => 'I can prepare well to speak in public even when the time available for preparation is limited.', 'ar' => 'أستطيع الاستعداد جيداً للتحدث أمام الجمهور حتى عندما يكون الوقت المتاح للتحضير محدوداً.']],
                6  => ['cat' => 'delivery', 'text' => ['en' => 'I can speak in front of a large audience with confidence.', 'ar' => 'أستطيع التحدث أمام جمهور كبير بثقة.']],
                7  => ['cat' => 'delivery', 'text' => ['en' => 'I can express my ideas clearly while speaking in public.', 'ar' => 'أستطيع التعبير عن أفكاري بوضوح أثناء التحدث أمام الجمهور.']],
                8  => ['cat' => 'delivery', 'text' => ['en' => 'I can keep my talk coherent while delivering an oral presentation.', 'ar' => 'أستطيع الحفاظ على تماسك حديثي أثناء تقديم عرض شفهي.']],
                9  => ['cat' => 'delivery', 'text' => ['en' => 'I can use an appropriate tone of voice and pace while speaking.', 'ar' => 'أستطيع استخدام نبرة صوت وإيقاع مناسبين أثناء التحدث.']],
                10 => ['cat' => 'delivery', 'text' => ['en' => 'I can hold the audience\'s attention during my talk.', 'ar' => 'أستطيع الحفاظ على انتباه الجمهور أثناء حديثي.']],
                11 => ['cat' => 'difficult_situations', 'text' => ['en' => 'I can answer unexpected questions during my talk.', 'ar' => 'أستطيع الإجابة عن الأسئلة غير المتوقعة أثناء حديثي.']],
                12 => ['cat' => 'difficult_situations', 'text' => ['en' => 'I can continue speaking effectively if I am interrupted.', 'ar' => 'أستطيع مواصلة الحديث بصورة فعالة إذا تعرضت للمقاطعة.']],
                13 => ['cat' => 'difficult_situations', 'text' => ['en' => 'I can calmly handle critical questions or comments from the audience.', 'ar' => 'أستطيع التعامل بهدوء مع الأسئلة أو التعليقات النقدية من الجمهور.']],
                14 => ['cat' => 'difficult_situations', 'text' => ['en' => 'I can regain my focus if I get flustered while speaking.', 'ar' => 'أستطيع استعادة تركيزي إذا ارتبكت أثناء الحديث.']],
                15 => ['cat' => 'difficult_situations', 'text' => ['en' => 'I can speak effectively in public even when I am under pressure or stress.', 'ar' => 'أستطيع التحدث بفعالية أمام الجمهور حتى عندما أتعرض لضغط أو توتر.']],
            ],
        );
    }

    // ------------------------------------------------------------------
    // 3. مقياس مهارات التحدث أمام الجمهور
    // ------------------------------------------------------------------
    private function publicSpeakingSkills(User $admin): Test
    {
        return $this->makeTest(
            $admin,
            ['en' => 'Public Speaking Skills Scale', 'ar' => 'مقياس مهارات التحدث أمام الجمهور'],
            [
                'en' => 'A 15-item self-report scale measuring public speaking skills across three dimensions: Organizing & Preparing the Talk, Presentation & Delivery Skills, and Audience Interaction.',
                'ar' => 'مقياس ذاتي التقرير من 15 بنداً يقيس مهارات التحدث أمام الجمهور عبر ثلاثة أبعاد: تنظيم وإعداد الحديث، مهارات التقديم والإلقاء، والتفاعل مع الجمهور.',
            ],
            $this->stdInstructions('فيما يلي مجموعة من العبارات المتعلقة بمهاراتك في التحدث أمام الجمهور.'),
            [
                'organizing' => ['en' => 'Organizing & Preparing the Talk', 'ar' => 'تنظيم وإعداد الحديث'],
                'presentation' => ['en' => 'Presentation & Delivery Skills', 'ar' => 'مهارات التقديم والإلقاء'],
                'interaction' => ['en' => 'Audience Interaction', 'ar' => 'التفاعل مع الجمهور'],
            ],
            [
                1  => ['cat' => 'organizing', 'text' => ['en' => 'I can structure my talk into a clear introduction, body, and conclusion.', 'ar' => 'أستطيع تنظيم حديثي في مقدمة ومضمون وخاتمة واضحة.']],
                2  => ['cat' => 'organizing', 'text' => ['en' => 'I present my ideas in a coherent and logical way.', 'ar' => 'أقدم أفكاري بطريقة مترابطة ومنطقية.']],
                3  => ['cat' => 'organizing', 'text' => ['en' => 'I focus my talk on the key ideas without distracting the audience with unnecessary details.', 'ar' => 'أركز حديثي على الأفكار الأساسية دون تشتيت الجمهور بتفاصيل غير ضرورية.']],
                4  => ['cat' => 'organizing', 'text' => ['en' => 'I can simplify complex information in a way that is easy for the audience to understand.', 'ar' => 'أستطيع تبسيط المعلومات المعقدة بطريقة يسهل على الجمهور فهمها.']],
                5  => ['cat' => 'organizing', 'text' => ['en' => 'I adjust the content of my talk to suit the nature of the audience I am addressing.', 'ar' => 'أعدل محتوى حديثي بما يتناسب مع طبيعة الجمهور الذي أخاطبه.']],
                6  => ['cat' => 'presentation', 'text' => ['en' => 'I use clear and direct language when speaking in public.', 'ar' => 'أستخدم لغة واضحة ومباشرة عند التحدث أمام الجمهور.']],
                7  => ['cat' => 'presentation', 'text' => ['en' => 'I use an appropriate tone of voice that helps convey my message effectively.', 'ar' => 'أستخدم نبرة صوت مناسبة تساعد على إيصال رسالتي بفاعلية.']],
                8  => ['cat' => 'presentation', 'text' => ['en' => 'I control the pace of my speech to match the content of the message and the audience.', 'ar' => 'أتحكم في سرعة حديثي بما يتناسب مع محتوى الرسالة والجمهور.']],
                9  => ['cat' => 'presentation', 'text' => ['en' => 'I use pauses and verbal emphasis in a way that reinforces the important points of my talk.', 'ar' => 'أستخدم الوقفات والتأكيد اللفظي بطريقة تعزز النقاط المهمة في حديثي.']],
                10 => ['cat' => 'presentation', 'text' => ['en' => 'I maintain appropriate eye contact with members of the audience during my talk.', 'ar' => 'أحافظ على تواصل بصري مناسب مع أفراد الجمهور أثناء حديثي.']],
                11 => ['cat' => 'interaction', 'text' => ['en' => 'I can capture and hold the audience\'s attention during my talk.', 'ar' => 'أستطيع جذب انتباه الجمهور والمحافظة عليه أثناء حديثي.']],
                12 => ['cat' => 'interaction', 'text' => ['en' => 'I can notice the audience\'s reactions and adjust my speaking style accordingly.', 'ar' => 'أستطيع ملاحظة استجابات الجمهور وتعديل أسلوب حديثي وفقاً لها.']],
                13 => ['cat' => 'interaction', 'text' => ['en' => 'I can involve the audience in the talk in an appropriate way.', 'ar' => 'أستطيع إشراك الجمهور في الحديث بطريقة مناسبة.']],
                14 => ['cat' => 'interaction', 'text' => ['en' => 'I can answer the audience\'s questions in a clear and direct way.', 'ar' => 'أستطيع الإجابة عن أسئلة الجمهور بطريقة واضحة ومباشرة.']],
                15 => ['cat' => 'interaction', 'text' => ['en' => 'I can handle difficult or critical questions without losing my focus.', 'ar' => 'أستطيع التعامل مع الأسئلة الصعبة أو النقدية دون أن أفقد تركيزي.']],
            ],
        );
    }

    // ------------------------------------------------------------------
    // 4. مقياس الكفاءة في التعامل مع وسائل الإعلام
    // ------------------------------------------------------------------
    private function mediaHandlingCompetence(User $admin): Test
    {
        return $this->makeTest(
            $admin,
            ['en' => 'Media Handling Competence Scale', 'ar' => 'مقياس الكفاءة في التعامل مع وسائل الإعلام'],
            [
                'en' => 'A 15-item self-report scale measuring competence in dealing with the media across three dimensions: Handling Media Interviews, Handling Difficult Questions & Situations, and Managing Messages & Information.',
                'ar' => 'مقياس ذاتي التقرير من 15 بنداً يقيس الكفاءة في التعامل مع وسائل الإعلام عبر ثلاثة أبعاد: التعامل مع المقابلات الإعلامية، التعامل مع الأسئلة والمواقف الصعبة، وإدارة الرسائل والمعلومات.',
            ],
            $this->stdInstructions('فيما يلي مجموعة من العبارات المتعلقة بقدرتك على التعامل مع وسائل الإعلام وتمثيل المنظمة أمامها.'),
            [
                'interviews' => ['en' => 'Handling Media Interviews', 'ar' => 'التعامل مع المقابلات الإعلامية'],
                'difficult_questions' => ['en' => 'Handling Difficult Questions & Situations', 'ar' => 'التعامل مع الأسئلة والمواقف الصعبة'],
                'message_management' => ['en' => 'Managing Messages & Information', 'ar' => 'إدارة الرسائل والمعلومات'],
            ],
            [
                1  => ['cat' => 'interviews', 'text' => ['en' => 'I can present the organization\'s messages clearly during media interviews.', 'ar' => 'أستطيع تقديم رسائل المنظمة بوضوح أثناء المقابلات الإعلامية.']],
                2  => ['cat' => 'interviews', 'text' => ['en' => 'I can adapt my way of speaking to suit the nature of the media outlet.', 'ar' => 'أستطيع تكييف طريقة حديثي بما يتناسب مع طبيعة الوسيلة الإعلامية.']],
                3  => ['cat' => 'interviews', 'text' => ['en' => 'I can maintain my focus during media interviews.', 'ar' => 'أستطيع الحفاظ على تركيزي أثناء المقابلات الإعلامية.']],
                4  => ['cat' => 'interviews', 'text' => ['en' => 'I can convey the organization\'s key messages even when interview time is limited.', 'ar' => 'أستطيع إيصال الرسائل الأساسية للمنظمة حتى عندما يكون وقت المقابلة محدوداً.']],
                5  => ['cat' => 'interviews', 'text' => ['en' => 'I can translate specialized information into messages the general public understands.', 'ar' => 'أستطيع تحويل المعلومات المتخصصة إلى رسائل يفهمها الجمهور العام.']],
                6  => ['cat' => 'difficult_questions', 'text' => ['en' => 'I can answer unexpected questions during media interviews.', 'ar' => 'أستطيع الإجابة عن الأسئلة غير المتوقعة أثناء المقابلات الإعلامية.']],
                7  => ['cat' => 'difficult_questions', 'text' => ['en' => 'I can deal with provocative questions without losing my composure.', 'ar' => 'أستطيع التعامل مع الأسئلة الاستفزازية دون أن أفقد هدوئي.']],
                8  => ['cat' => 'difficult_questions', 'text' => ['en' => 'I can handle questions that include criticism of the organization in a professional way.', 'ar' => 'أستطيع التعامل مع الأسئلة التي تتضمن انتقادات للمنظمة بطريقة مهنية.']],
                9  => ['cat' => 'difficult_questions', 'text' => ['en' => 'I can avoid being drawn into answers that could harm the organization\'s image.', 'ar' => 'أستطيع تجنب الانجرار إلى إجابات قد تضر بصورة المنظمة.']],
                10 => ['cat' => 'difficult_questions', 'text' => ['en' => 'I can keep to my key message when the interviewer tries to change the direction of the conversation.', 'ar' => 'أستطيع الحفاظ على رسالتي الأساسية عندما يحاول المحاور تغيير اتجاه الحديث.']],
                11 => ['cat' => 'message_management', 'text' => ['en' => 'I can distinguish between information that may be disclosed and information that must be kept confidential.', 'ar' => 'أستطيع التمييز بين المعلومات التي يمكن الإفصاح عنها والمعلومات التي يجب الحفاظ على سريتها.']],
                12 => ['cat' => 'message_management', 'text' => ['en' => 'I can stick to the messages and information approved by the organization when dealing with the media.', 'ar' => 'أستطيع الالتزام بالرسائل والمعلومات المعتمدة من المنظمة عند التعامل مع وسائل الإعلام.']],
                13 => ['cat' => 'message_management', 'text' => ['en' => 'I can correct inaccurate information in a professional, non-confrontational way.', 'ar' => 'أستطيع تصحيح المعلومات غير الدقيقة بطريقة مهنية وغير تصادمية.']],
                14 => ['cat' => 'message_management', 'text' => ['en' => 'I can present sensitive information in a way that preserves the organization\'s credibility.', 'ar' => 'أستطيع تقديم المعلومات الحساسة بطريقة تحافظ على مصداقية المنظمة.']],
                15 => ['cat' => 'message_management', 'text' => ['en' => 'I can represent the organization\'s position clearly and consistently even in critical media situations.', 'ar' => 'أستطيع تمثيل موقف المنظمة بوضوح واتساق حتى في المواقف الإعلامية الحرجة.']],
            ],
        );
    }

    // ------------------------------------------------------------------
    // 5. مقياس التوجه نحو الجمهور
    // ------------------------------------------------------------------
    private function audienceOrientation(User $admin): Test
    {
        return $this->makeTest(
            $admin,
            ['en' => 'Audience Orientation Scale', 'ar' => 'مقياس التوجه نحو الجمهور'],
            [
                'en' => 'A 15-item self-report scale measuring audience orientation in communication across three dimensions: Understanding the Audience, Adapting the Message to the Audience, and Responding to & Interacting with the Audience.',
                'ar' => 'مقياس ذاتي التقرير من 15 بنداً يقيس التوجه نحو الجمهور عند التواصل عبر ثلاثة أبعاد: فهم الجمهور، تكييف الرسالة مع الجمهور، والاستجابة للجمهور والتفاعل معه.',
            ],
            $this->stdInstructions('فيما يلي مجموعة من العبارات المتعلقة بتوجهك نحو الجمهور عند التواصل معه.'),
            [
                'understanding' => ['en' => 'Understanding the Audience', 'ar' => 'فهم الجمهور'],
                'adapting' => ['en' => 'Adapting the Message', 'ar' => 'تكييف الرسالة مع الجمهور'],
                'responding' => ['en' => 'Responding & Interacting', 'ar' => 'الاستجابة للجمهور والتفاعل معه'],
            ],
            [
                1  => ['cat' => 'understanding', 'text' => ['en' => 'I make sure to understand the characteristics of the audience I address before preparing my message.', 'ar' => 'أحرص على فهم خصائص الجمهور الذي أتحدث إليه قبل إعداد رسالتي.']],
                2  => ['cat' => 'understanding', 'text' => ['en' => 'I take into account the audience\'s level of knowledge of the topic I am speaking about.', 'ar' => 'أراعي مستوى معرفة الجمهور بالموضوع الذي أتحدث عنه.']],
                3  => ['cat' => 'understanding', 'text' => ['en' => 'I take an interest in identifying the audience\'s needs and expectations before communicating with them.', 'ar' => 'أهتم بالتعرف على احتياجات الجمهور وتوقعاته قبل التواصل معه.']],
                4  => ['cat' => 'understanding', 'text' => ['en' => 'I keep the audience\'s interests in mind when deciding which points to focus on in my talk.', 'ar' => 'أضع في اعتباري اهتمامات الجمهور عند تحديد النقاط التي أركز عليها في حديثي.']],
                5  => ['cat' => 'understanding', 'text' => ['en' => 'I make sure to understand the audience\'s different viewpoints on the topic I am speaking about.', 'ar' => 'أحرص على فهم وجهات نظر الجمهور المختلفة حول الموضوع الذي أتحدث عنه.']],
                6  => ['cat' => 'adapting', 'text' => ['en' => 'I adjust my speaking style to suit the nature of the audience I am addressing.', 'ar' => 'أعدل أسلوب حديثي بما يتناسب مع طبيعة الجمهور الذي أخاطبه.']],
                7  => ['cat' => 'adapting', 'text' => ['en' => 'I use examples and illustrations that fit the audience\'s experience and level of knowledge.', 'ar' => 'أستخدم أمثلة وتوضيحات تتناسب مع خبرات الجمهور ومستوى معرفته.']],
                8  => ['cat' => 'adapting', 'text' => ['en' => 'I avoid using terms that the audience may find hard to understand.', 'ar' => 'أتجنب استخدام المصطلحات التي قد يصعب على الجمهور فهمها.']],
                9  => ['cat' => 'adapting', 'text' => ['en' => 'I can present the same message in different ways depending on the audience.', 'ar' => 'أستطيع تقديم الرسالة نفسها بطرق مختلفة تبعاً لاختلاف الجمهور.']],
                10 => ['cat' => 'adapting', 'text' => ['en' => 'I make sure my message is relevant to the audience\'s interests and needs.', 'ar' => 'أحرص على أن تكون رسالتي ذات صلة باهتمامات الجمهور واحتياجاته.']],
                11 => ['cat' => 'responding', 'text' => ['en' => 'I monitor the audience\'s reactions during my talk and respond to them appropriately.', 'ar' => 'أراقب ردود أفعال الجمهور أثناء حديثي وأستجيب لها بشكل ملائم.']],
                12 => ['cat' => 'responding', 'text' => ['en' => 'I adjust the way I deliver the message when I notice the audience does not understand it.', 'ar' => 'أعدل طريقة تقديمي للرسالة عندما ألاحظ عدم فهم الجمهور لها.']],
                13 => ['cat' => 'responding', 'text' => ['en' => 'I listen attentively to the audience\'s questions and comments.', 'ar' => 'أستمع باهتمام إلى أسئلة الجمهور وملاحظاته.']],
                14 => ['cat' => 'responding', 'text' => ['en' => 'I deal with viewpoints that differ from mine with respect and professionalism.', 'ar' => 'أتعامل مع وجهات النظر المختلفة عن موقفي باحترام ومهنية.']],
                15 => ['cat' => 'responding', 'text' => ['en' => 'I make sure the audience feels that their questions and concerns are valued.', 'ar' => 'أحرص على أن يشعر الجمهور بأن أسئلته واهتماماته محل تقدير.']],
            ],
        );
    }

    // ------------------------------------------------------------------
    // 6. مقياس الكفاءة في الاتصال غير اللفظي
    // ------------------------------------------------------------------
    private function nonverbalCommunicationCompetence(User $admin): Test
    {
        return $this->makeTest(
            $admin,
            ['en' => 'Nonverbal Communication Competence Scale', 'ar' => 'مقياس الكفاءة في الاتصال غير اللفظي'],
            [
                'en' => 'A 15-item self-report scale measuring competence in nonverbal communication across three dimensions: Using Nonverbal Cues, Awareness of Nonverbal Cues, and Regulating Nonverbal Communication.',
                'ar' => 'مقياس ذاتي التقرير من 15 بنداً يقيس الكفاءة في الاتصال غير اللفظي عبر ثلاثة أبعاد: استخدام الإشارات غير اللفظية، الوعي بالإشارات غير اللفظية، وتنظيم الاتصال غير اللفظي.',
            ],
            $this->stdInstructions('فيما يلي مجموعة من العبارات المتعلقة بقدرتك على استخدام وفهم الاتصال غير اللفظي أثناء التفاعل مع الآخرين.'),
            [
                'using_cues' => ['en' => 'Using Nonverbal Cues', 'ar' => 'استخدام الإشارات غير اللفظية'],
                'awareness' => ['en' => 'Awareness of Nonverbal Cues', 'ar' => 'الوعي بالإشارات غير اللفظية'],
                'regulating' => ['en' => 'Regulating Nonverbal Communication', 'ar' => 'تنظيم الاتصال غير اللفظي'],
            ],
            [
                1  => ['cat' => 'using_cues', 'text' => ['en' => 'I use facial expressions that match the content of my talk.', 'ar' => 'أستخدم تعبيرات وجه تتناسب مع مضمون حديثي.']],
                2  => ['cat' => 'using_cues', 'text' => ['en' => 'I maintain appropriate eye contact while speaking with others.', 'ar' => 'أحافظ على تواصل بصري مناسب أثناء التحدث مع الآخرين.']],
                3  => ['cat' => 'using_cues', 'text' => ['en' => 'I use hand gestures in a way that supports the message I am delivering.', 'ar' => 'أستخدم إيماءات اليد بطريقة تدعم الرسالة التي أقدمها.']],
                4  => ['cat' => 'using_cues', 'text' => ['en' => 'I control my posture to reflect confidence and professionalism while speaking.', 'ar' => 'أتحكم في وضعية جسدي بما يعكس الثقة والاحترافية أثناء الحديث.']],
                5  => ['cat' => 'using_cues', 'text' => ['en' => 'I use a tone of voice that matches the content of the message and the situation I am speaking in.', 'ar' => 'أستخدم نبرة صوت تتناسب مع مضمون الرسالة والموقف الذي أتحدث فيه.']],
                6  => ['cat' => 'awareness', 'text' => ['en' => 'I pay attention to others\' facial expressions while talking with them.', 'ar' => 'أنتبه إلى تعبيرات وجوه الآخرين أثناء حديثي معهم.']],
                7  => ['cat' => 'awareness', 'text' => ['en' => 'I can notice changes in the body language of the person I am speaking to.', 'ar' => 'أستطيع ملاحظة التغيرات في لغة جسد الشخص الذي أتحدث إليه.']],
                8  => ['cat' => 'awareness', 'text' => ['en' => 'I can infer others\' feelings from their nonverbal cues.', 'ar' => 'أستطيع الاستدلال على مشاعر الآخرين من إشاراتهم غير اللفظية.']],
                9  => ['cat' => 'awareness', 'text' => ['en' => 'I notice when the audience\'s body language indicates lack of understanding or interest.', 'ar' => 'ألاحظ عندما تشير لغة جسد الجمهور إلى عدم الفهم أو عدم الاهتمام.']],
                10 => ['cat' => 'awareness', 'text' => ['en' => 'I can distinguish nonverbal cues that reflect interest from those that reflect discomfort.', 'ar' => 'أستطيع التمييز بين الإشارات غير اللفظية التي تعكس الاهتمام وتلك التي تعكس عدم الارتياح.']],
                11 => ['cat' => 'regulating', 'text' => ['en' => 'I can control my facial expressions when dealing with difficult situations.', 'ar' => 'أستطيع التحكم في تعبيرات وجهي عندما أتعامل مع مواقف صعبة.']],
                12 => ['cat' => 'regulating', 'text' => ['en' => 'I can keep my body language calm and professional under pressure.', 'ar' => 'أستطيع الحفاظ على لغة جسد هادئة ومهنية تحت الضغط.']],
                13 => ['cat' => 'regulating', 'text' => ['en' => 'I can adjust my nonverbal behavior to suit the nature of the situation and the audience.', 'ar' => 'أستطيع تعديل سلوكي غير اللفظي بما يتناسب مع طبيعة الموقف والجمهور.']],
                14 => ['cat' => 'regulating', 'text' => ['en' => 'I can keep my nonverbal cues consistent with my verbal message.', 'ar' => 'أستطيع الحفاظ على اتساق إشاراتي غير اللفظية مع رسالتي اللفظية.']],
                15 => ['cat' => 'regulating', 'text' => ['en' => 'I can use nonverbal communication to enhance my credibility while speaking in public.', 'ar' => 'أستطيع استخدام الاتصال غير اللفظي لتعزيز مصداقيتي أثناء التحدث أمام الجمهور.']],
            ],
        );
    }

    // ------------------------------------------------------------------
    // 7. مقياس المرونة الاتصالية
    // ------------------------------------------------------------------
    private function communicationFlexibility(User $admin): Test
    {
        return $this->makeTest(
            $admin,
            ['en' => 'Communication Flexibility Scale', 'ar' => 'مقياس المرونة الاتصالية'],
            [
                'en' => 'A 15-item self-report scale measuring communication flexibility across three dimensions: Adapting to Different Audiences, Adapting to Communication Situations, and Responding to Feedback.',
                'ar' => 'مقياس ذاتي التقرير من 15 بنداً يقيس المرونة الاتصالية عبر ثلاثة أبعاد: التكيف مع اختلاف الجمهور، التكيف مع المواقف الاتصالية، والاستجابة لردود الفعل.',
            ],
            $this->stdInstructions('فيما يلي مجموعة من العبارات المتعلقة بقدرتك على التكيف والمرونة أثناء التواصل مع الآخرين.'),
            [
                'audiences' => ['en' => 'Adapting to Different Audiences', 'ar' => 'التكيف مع اختلاف الجمهور'],
                'situations' => ['en' => 'Adapting to Communication Situations', 'ar' => 'التكيف مع المواقف الاتصالية'],
                'feedback' => ['en' => 'Responding to Feedback', 'ar' => 'الاستجابة لردود الفعل'],
            ],
            [
                1  => ['cat' => 'audiences', 'text' => ['en' => 'I can adjust my communication style to suit the nature of the audience I am addressing.', 'ar' => 'أستطيع تعديل أسلوبي في التواصل بما يتناسب مع طبيعة الجمهور الذي أخاطبه.']],
                2  => ['cat' => 'audiences', 'text' => ['en' => 'I can change the level of detail of information according to the audience\'s knowledge of the topic.', 'ar' => 'أستطيع تغيير مستوى تفصيل المعلومات وفقاً لمستوى معرفة الجمهور بالموضوع.']],
                3  => ['cat' => 'audiences', 'text' => ['en' => 'I can use different methods to deliver the same message to different audiences.', 'ar' => 'أستطيع استخدام أساليب مختلفة لتوصيل الرسالة نفسها إلى جماهير مختلفة.']],
                4  => ['cat' => 'audiences', 'text' => ['en' => 'I can switch between a formal and an informal style according to the nature of the situation and the audience.', 'ar' => 'أستطيع الانتقال بين الأسلوب الرسمي وغير الرسمي وفقاً لطبيعة الموقف والجمهور.']],
                5  => ['cat' => 'audiences', 'text' => ['en' => 'I can adapt the way I present my ideas when dealing with people from different backgrounds.', 'ar' => 'أستطيع تكييف طريقة عرض أفكاري عندما أتعامل مع أشخاص ذوي خلفيات مختلفة.']],
                6  => ['cat' => 'situations', 'text' => ['en' => 'I can quickly adjust my communication style when the circumstances of the situation change.', 'ar' => 'أستطيع تعديل أسلوب تواصلي بسرعة عندما تتغير ظروف الموقف.']],
                7  => ['cat' => 'situations', 'text' => ['en' => 'I can deal effectively with communication situations I did not prepare for in advance.', 'ar' => 'أستطيع التعامل بفاعلية مع المواقف الاتصالية التي لم أستعد لها مسبقاً.']],
                8  => ['cat' => 'situations', 'text' => ['en' => 'I can change the way I deliver the message when the method I am using does not achieve the desired response.', 'ar' => 'أستطيع تغيير طريقة تقديم الرسالة عندما لا تحقق الطريقة المستخدمة الاستجابة المطلوبة.']],
                9  => ['cat' => 'situations', 'text' => ['en' => 'I can keep my communication effective even when sudden changes occur during the conversation.', 'ar' => 'أستطيع الحفاظ على فعالية تواصلي حتى عندما تحدث تغيرات مفاجئة أثناء الحديث.']],
                10 => ['cat' => 'situations', 'text' => ['en' => 'I can move smoothly between different topics while interacting with others.', 'ar' => 'أستطيع الانتقال بسلاسة بين موضوعات مختلفة أثناء التفاعل مع الآخرين.']],
                11 => ['cat' => 'feedback', 'text' => ['en' => 'I can adjust my message in response to the audience\'s reactions.', 'ar' => 'أستطيع تعديل رسالتي استجابةً لردود فعل الجمهور.']],
                12 => ['cat' => 'feedback', 'text' => ['en' => 'I can change the way I explain when I notice that the other party did not understand my message.', 'ar' => 'أستطيع تغيير أسلوب شرحي عندما ألاحظ أن الطرف الآخر لم يفهم رسالتي.']],
                13 => ['cat' => 'feedback', 'text' => ['en' => 'I can deal with opposing viewpoints without sticking to a single communication style.', 'ar' => 'أستطيع التعامل مع وجهات النظر المخالفة دون أن أتمسك بأسلوب تواصلي واحد.']],
                14 => ['cat' => 'feedback', 'text' => ['en' => 'I can adjust the way I answer when I realize the question was understood differently from what I expected.', 'ar' => 'أستطيع تعديل طريقة إجابتي عندما أكتشف أن السؤال قد فُهم بطريقة مختلفة عما توقعت.']],
                15 => ['cat' => 'feedback', 'text' => ['en' => 'I can change my communication style while preserving the core meaning of the message.', 'ar' => 'أستطيع تغيير أسلوبي الاتصالي مع الحفاظ على المعنى الأساسي للرسالة.']],
            ],
        );
    }

    // ------------------------------------------------------------------
    // Assessment + link
    // ------------------------------------------------------------------
    /** @param  array<int,Test>  $tests */
    private function buildAssessment(User $admin, array $tests): void
    {
        $assessment = Assessment::create([
            'user_id' => $admin->id,
            'title' => [
                'en' => 'Official Spokesperson Scales',
                'ar' => 'مقاييس المتحدث الرسمي',
            ],
            'description' => [
                'en' => 'A battery of seven self-report scales assessing the competencies of an official organizational spokesperson: credibility, public-speaking self-efficacy, public-speaking skills, media handling, audience orientation, nonverbal communication, and communication flexibility.',
                'ar' => 'بطارية من سبعة مقاييس ذاتية التقرير تقيس جدارات المتحدث الرسمي باسم المنظمة: المصداقية، والفاعلية الذاتية في التحدث أمام الجمهور، ومهارات التحدث أمام الجمهور، والكفاءة في التعامل مع وسائل الإعلام، والتوجه نحو الجمهور، والاتصال غير اللفظي، والمرونة الاتصالية.',
            ],
            'instructions' => [
                'en' => 'This assessment contains seven short scales. Answer every statement honestly on a 1-5 scale (Strongly Disagree to Strongly Agree). There are no right or wrong answers.',
                'ar' => 'يتضمن هذا التقييم سبعة مقاييس قصيرة. أجب عن كل عبارة بصدق على مقياس من 1 إلى 5 (من لا أوافق بشدة إلى أوافق بشدة). لا توجد إجابات صحيحة أو خاطئة.',
            ],
            'status' => 'published',
            'show_results_to_participant' => true,
        ]);

        foreach ($tests as $index => $test) {
            $assessment->tests()->attach($test->id, ['sort_order' => $index + 1]);
        }

        $link = AssessmentLink::create([
            'assessment_id' => $assessment->id,
            'created_by' => $admin->id,
            'title' => 'مقاييس المتحدث الرسمي — رابط عام',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'max_participants' => 50,
            'is_active' => true,
            'collect_name' => true,
            'collect_email' => true,
            'collect_phone' => false,
            'collect_company' => true,
            'collect_job_title' => true,
            'collect_age' => true,
            'collect_gender' => true,
            'welcome_message' => [
                'en' => 'Welcome. This assessment includes seven short scales about spokesperson competencies and takes roughly 20-25 minutes. Please answer honestly.',
                'ar' => 'مرحباً بك. يتضمن هذا التقييم سبعة مقاييس قصيرة حول جدارات المتحدث الرسمي ويستغرق نحو 20 إلى 25 دقيقة. يرجى الإجابة بصدق.',
            ],
            'completion_message' => [
                'en' => 'Thank you for completing the Official Spokesperson Scales.',
                'ar' => 'شكراً لإكمالك مقاييس المتحدث الرسمي.',
            ],
        ]);

        $this->command->info('');
        $this->command->info('=== مقاييس المتحدث الرسمي / Official Spokesperson Scales ===');
        $this->command->info("Assessment #{$assessment->id} created with " . count($tests) . ' tests.');
        $this->command->info("Link active {$link->starts_at->toDateString()} → {$link->expires_at->toDateString()} (max {$link->max_participants} participants)");
        $this->command->info("Participant URL: {$link->getUrl()}");
    }
}
