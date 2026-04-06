<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        $sarah = User::where('email', 'sarah@edrak.com')->first();
        $omar = User::where('email', 'omar@edrak.com')->first();

        $this->createBurnoutInventory($sarah);
        $this->createPerceivedStressScale($omar);
        $this->createJobSatisfactionSurvey($sarah);
    }

    private function createBurnoutInventory(User $user): void
    {
        $test = Test::create([
            'user_id' => $user->id,
            'title' => [
                'en' => 'Maslach Burnout Inventory',
                'ar' => 'مقياس ماسلاش للإحتراق الوظيفي',
            ],
            'description' => [
                'en' => 'The Maslach Burnout Inventory (MBI) measures burnout as defined by the World Health Organization. It assesses three dimensions: emotional exhaustion, depersonalization, and personal accomplishment.',
                'ar' => 'يقيس مقياس ماسلاش للإحتراق الوظيفي الإحتراق النفسي كما حددته منظمة الصحة العالمية. يقيم ثلاثة أبعاد: الإرهاق العاطفي، وتبدد الشخصية، والإنجاز الشخصي.',
            ],
            'instructions' => [
                'en' => 'Please read each statement carefully and decide how often you feel that way about your job. Rate each item using the scale provided.',
                'ar' => 'يرجى قراءة كل عبارة بعناية وتحديد مدى شعورك بذلك تجاه عملك. قيّم كل عبارة باستخدام المقياس المقدم.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 0,
                'max' => 6,
                'labels' => [
                    'en' => ['Never', 'A few times a year', 'Once a month', 'A few times a month', 'Once a week', 'A few times a week', 'Every day'],
                    'ar' => ['أبداً', 'بضع مرات في السنة', 'مرة في الشهر', 'بضع مرات في الشهر', 'مرة في الأسبوع', 'بضع مرات في الأسبوع', 'كل يوم'],
                ],
            ],
            'time_limit_minutes' => 30,
            'randomize_questions' => false,
        ]);

        // Emotional Exhaustion questions
        $eeQuestions = [
            ['en' => 'I feel emotionally drained from my work.', 'ar' => 'أشعر بالاستنزاف العاطفي من عملي.'],
            ['en' => 'I feel used up at the end of the workday.', 'ar' => 'أشعر بالإنهاك في نهاية يوم العمل.'],
            ['en' => 'I feel fatigued when I get up in the morning and have to face another day on the job.', 'ar' => 'أشعر بالتعب عندما أستيقظ صباحاً وأضطر لمواجهة يوم عمل آخر.'],
            ['en' => 'Working with people all day is really a strain for me.', 'ar' => 'العمل مع الناس طوال اليوم يشكل ضغطاً حقيقياً علي.'],
            ['en' => 'I feel burned out from my work.', 'ar' => 'أشعر بالاحتراق من عملي.'],
            ['en' => 'I feel frustrated by my job.', 'ar' => 'أشعر بالإحباط من وظيفتي.'],
            ['en' => 'I feel I\'m working too hard on my job.', 'ar' => 'أشعر أنني أعمل بجهد مفرط في وظيفتي.'],
            ['en' => 'Working directly with people puts too much stress on me.', 'ar' => 'العمل مباشرة مع الناس يضع ضغطاً كبيراً علي.'],
            ['en' => 'I feel like I\'m at the end of my rope.', 'ar' => 'أشعر أنني وصلت إلى نهاية طاقتي.'],
        ];

        foreach ($eeQuestions as $i => $text) {
            Question::create([
                'test_id' => $test->id,
                'text' => $text,
                'sort_order' => $i + 1,
                'is_reverse_scored' => false,
                'is_required' => true,
            ]);
        }

        // Depersonalization questions
        $dpQuestions = [
            ['en' => 'I feel I treat some recipients as if they were impersonal objects.', 'ar' => 'أشعر أنني أعامل بعض المتلقين كما لو كانوا أشياء غير شخصية.'],
            ['en' => 'I\'ve become more callous toward people since I took this job.', 'ar' => 'أصبحت أكثر قسوة تجاه الناس منذ أن بدأت هذا العمل.'],
            ['en' => 'I worry that this job is hardening me emotionally.', 'ar' => 'أخشى أن هذا العمل يجعلني قاسياً عاطفياً.'],
            ['en' => 'I don\'t really care what happens to some recipients.', 'ar' => 'لا أهتم حقاً بما يحدث لبعض المتلقين.'],
            ['en' => 'I feel recipients blame me for some of their problems.', 'ar' => 'أشعر أن المتلقين يلومونني على بعض مشاكلهم.'],
        ];

        foreach ($dpQuestions as $i => $text) {
            Question::create([
                'test_id' => $test->id,
                'text' => $text,
                'sort_order' => $i + 10,
                'is_reverse_scored' => false,
                'is_required' => true,
            ]);
        }

        // Personal Accomplishment questions (reverse-scored)
        $paQuestions = [
            ['en' => 'I can easily understand how my recipients feel about things.', 'ar' => 'أستطيع بسهولة فهم مشاعر المتلقين تجاه الأمور.'],
            ['en' => 'I deal very effectively with the problems of my recipients.', 'ar' => 'أتعامل بفعالية كبيرة مع مشاكل المتلقين.'],
            ['en' => 'I feel I\'m positively influencing other people\'s lives through my work.', 'ar' => 'أشعر أنني أؤثر إيجابياً في حياة الآخرين من خلال عملي.'],
            ['en' => 'I feel very energetic.', 'ar' => 'أشعر بالحيوية والنشاط.'],
            ['en' => 'I can easily create a relaxed atmosphere with my recipients.', 'ar' => 'أستطيع بسهولة خلق جو مريح مع المتلقين.'],
            ['en' => 'I feel exhilarated after working closely with my recipients.', 'ar' => 'أشعر بالسعادة بعد العمل عن قرب مع المتلقين.'],
            ['en' => 'I have accomplished many worthwhile things in this job.', 'ar' => 'أنجزت أشياء كثيرة قيّمة في هذا العمل.'],
            ['en' => 'In my work, I deal with emotional problems very calmly.', 'ar' => 'في عملي، أتعامل مع المشاكل العاطفية بهدوء تام.'],
        ];

        foreach ($paQuestions as $i => $text) {
            Question::create([
                'test_id' => $test->id,
                'text' => $text,
                'sort_order' => $i + 15,
                'is_reverse_scored' => true,
                'is_required' => true,
            ]);
        }
    }

    private function createPerceivedStressScale(User $user): void
    {
        $test = Test::create([
            'user_id' => $user->id,
            'title' => [
                'en' => 'Perceived Stress Scale (PSS-10)',
                'ar' => 'مقياس الضغط النفسي المُدرك (PSS-10)',
            ],
            'description' => [
                'en' => 'The Perceived Stress Scale measures the degree to which situations in one\'s life are appraised as stressful. Items were designed to assess how unpredictable, uncontrollable, and overloaded respondents find their lives.',
                'ar' => 'يقيس مقياس الضغط النفسي المُدرك درجة تقييم المواقف في حياة الفرد على أنها مسببة للتوتر. صُممت العبارات لتقييم مدى عدم القدرة على التنبؤ والسيطرة والإثقال الذي يشعر به المستجيبون في حياتهم.',
            ],
            'instructions' => [
                'en' => 'The questions ask about your feelings and thoughts during the last month. In each case, indicate how often you felt or thought a certain way.',
                'ar' => 'تسأل الأسئلة عن مشاعرك وأفكارك خلال الشهر الماضي. في كل حالة، حدد مدى تكرار شعورك أو تفكيرك بطريقة معينة.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 0,
                'max' => 4,
                'labels' => [
                    'en' => ['Never', 'Almost never', 'Sometimes', 'Fairly often', 'Very often'],
                    'ar' => ['أبداً', 'نادراً', 'أحياناً', 'غالباً', 'دائماً تقريباً'],
                ],
            ],
            'time_limit_minutes' => null,
            'randomize_questions' => false,
        ]);

        $questions = [
            ['text' => ['en' => 'In the last month, how often have you been upset because of something that happened unexpectedly?', 'ar' => 'في الشهر الماضي، كم مرة شعرت بالانزعاج بسبب شيء حدث بشكل غير متوقع؟'], 'reverse' => false],
            ['text' => ['en' => 'In the last month, how often have you felt that you were unable to control the important things in your life?', 'ar' => 'في الشهر الماضي، كم مرة شعرت أنك غير قادر على التحكم في الأمور المهمة في حياتك؟'], 'reverse' => false],
            ['text' => ['en' => 'In the last month, how often have you felt nervous and stressed?', 'ar' => 'في الشهر الماضي، كم مرة شعرت بالتوتر والضغط؟'], 'reverse' => false],
            ['text' => ['en' => 'In the last month, how often have you felt confident about your ability to handle your personal problems?', 'ar' => 'في الشهر الماضي، كم مرة شعرت بالثقة في قدرتك على التعامل مع مشاكلك الشخصية؟'], 'reverse' => true],
            ['text' => ['en' => 'In the last month, how often have you felt that things were going your way?', 'ar' => 'في الشهر الماضي، كم مرة شعرت أن الأمور تسير كما تريد؟'], 'reverse' => true],
            ['text' => ['en' => 'In the last month, how often have you found that you could not cope with all the things that you had to do?', 'ar' => 'في الشهر الماضي، كم مرة وجدت أنك لا تستطيع التعامل مع كل الأشياء التي كان عليك القيام بها؟'], 'reverse' => false],
            ['text' => ['en' => 'In the last month, how often have you been able to control irritations in your life?', 'ar' => 'في الشهر الماضي، كم مرة استطعت السيطرة على مصادر الانزعاج في حياتك؟'], 'reverse' => true],
            ['text' => ['en' => 'In the last month, how often have you felt that you were on top of things?', 'ar' => 'في الشهر الماضي، كم مرة شعرت أنك مسيطر على الأمور؟'], 'reverse' => true],
            ['text' => ['en' => 'In the last month, how often have you been angered because of things that were outside of your control?', 'ar' => 'في الشهر الماضي، كم مرة شعرت بالغضب بسبب أشياء خارجة عن إرادتك؟'], 'reverse' => false],
            ['text' => ['en' => 'In the last month, how often have you felt difficulties were piling up so high that you could not overcome them?', 'ar' => 'في الشهر الماضي، كم مرة شعرت أن الصعوبات تتراكم لدرجة أنك لا تستطيع التغلب عليها؟'], 'reverse' => false],
        ];

        foreach ($questions as $i => $q) {
            Question::create([
                'test_id' => $test->id,
                'text' => $q['text'],
                'sort_order' => $i + 1,
                'is_reverse_scored' => $q['reverse'],
                'is_required' => true,
            ]);
        }
    }

    private function createJobSatisfactionSurvey(User $user): void
    {
        $test = Test::create([
            'user_id' => $user->id,
            'title' => [
                'en' => 'Job Satisfaction Survey',
                'ar' => 'استبيان الرضا الوظيفي',
            ],
            'description' => [
                'en' => 'This survey measures overall job satisfaction across multiple dimensions including work environment, compensation, and growth opportunities.',
                'ar' => 'يقيس هذا الاستبيان الرضا الوظيفي العام عبر أبعاد متعددة تشمل بيئة العمل والتعويضات وفرص النمو.',
            ],
            'instructions' => [
                'en' => 'Please indicate your level of agreement with each statement about your current job.',
                'ar' => 'يرجى تحديد مستوى موافقتك على كل عبارة تتعلق بوظيفتك الحالية.',
            ],
            'status' => 'draft',
            'scale_config' => [
                'min' => 1,
                'max' => 5,
                'labels' => [
                    'en' => ['Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree'],
                    'ar' => ['أعارض بشدة', 'أعارض', 'محايد', 'أوافق', 'أوافق بشدة'],
                ],
            ],
            'time_limit_minutes' => 20,
            'randomize_questions' => false,
        ]);

        // Work Environment questions
        $envQuestions = [
            ['en' => 'My workplace provides a comfortable physical environment.', 'ar' => 'يوفر مكان عملي بيئة مادية مريحة.'],
            ['en' => 'I have the tools and resources I need to do my job well.', 'ar' => 'لدي الأدوات والموارد التي أحتاجها لأداء عملي بشكل جيد.'],
            ['en' => 'I have a good relationship with my colleagues.', 'ar' => 'لدي علاقة جيدة مع زملائي في العمل.'],
            ['en' => 'My manager treats me with respect.', 'ar' => 'يعاملني مديري باحترام.'],
            ['en' => 'Communication in my organization is open and transparent.', 'ar' => 'التواصل في منظمتي مفتوح وشفاف.'],
        ];

        foreach ($envQuestions as $i => $text) {
            Question::create([
                'test_id' => $test->id,
                'text' => $text,
                'sort_order' => $i + 1,
                'is_reverse_scored' => false,
                'is_required' => true,
            ]);
        }

        // Compensation & Benefits questions
        $compQuestions = [
            ['en' => 'I feel I am fairly compensated for my work.', 'ar' => 'أشعر أنني أحصل على تعويض عادل عن عملي.'],
            ['en' => 'The benefits package meets my needs.', 'ar' => 'حزمة المزايا تلبي احتياجاتي.'],
            ['en' => 'I am satisfied with the opportunities for salary increases.', 'ar' => 'أنا راضٍ عن فرص زيادة الراتب.'],
            ['en' => 'My pay is competitive compared to similar roles elsewhere.', 'ar' => 'راتبي تنافسي مقارنة بالأدوار المماثلة في أماكن أخرى.'],
            ['en' => 'I feel my efforts are recognized and rewarded.', 'ar' => 'أشعر أن جهودي تُقدر وتُكافأ.'],
        ];

        foreach ($compQuestions as $i => $text) {
            Question::create([
                'test_id' => $test->id,
                'text' => $text,
                'sort_order' => $i + 6,
                'is_reverse_scored' => false,
                'is_required' => true,
            ]);
        }

        // Growth & Development questions
        $growthQuestions = [
            ['en' => 'I have opportunities for professional growth in my role.', 'ar' => 'لدي فرص للنمو المهني في دوري.'],
            ['en' => 'My organization supports my training and development.', 'ar' => 'تدعم منظمتي تدريبي وتطويري.'],
            ['en' => 'I can see a clear career path for myself here.', 'ar' => 'أستطيع رؤية مسار مهني واضح لنفسي هنا.'],
            ['en' => 'I am given challenging work that helps me grow.', 'ar' => 'يُسند إلي عمل مُحفّز يساعدني على النمو.'],
            ['en' => 'I would recommend this organization as a great place to work.', 'ar' => 'أوصي بهذه المنظمة كمكان عمل ممتاز.'],
        ];

        foreach ($growthQuestions as $i => $text) {
            Question::create([
                'test_id' => $test->id,
                'text' => $text,
                'sort_order' => $i + 11,
                'is_reverse_scored' => false,
                'is_required' => true,
            ]);
        }
    }
}
