<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class EntrepreneurialAttitudeOrientationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        // Same percentage-based interpretation bands used by the other
        // entrepreneurship category-scored tests. With a 10-point scale,
        // per-category max varies (item count differs per subscale) but
        // percentage interpretation stays consistent.
        $interp = [
            ['min' => 0,  'max' => 39,  'label' => ['en' => 'Low',       'ar' => 'منخفض']],
            ['min' => 40, 'max' => 59,  'label' => ['en' => 'Moderate',  'ar' => 'متوسط']],
            ['min' => 60, 'max' => 79,  'label' => ['en' => 'High',      'ar' => 'مرتفع']],
            ['min' => 80, 'max' => 100, 'label' => ['en' => 'Very High', 'ar' => 'مرتفع جداً']],
        ];

        $test = Test::create([
            'user_id' => $admin->id,
            'title' => [
                'en' => 'Entrepreneurial Attitude Orientation Scale (EAO)',
                'ar' => 'مقياس توجهات اتجاه ريادة الأعمال (EAO)',
            ],
            'description' => [
                'en' => "A 75-item scale measuring entrepreneurial attitudes across four content subscales: Achievement, Innovation, Personal Control, and Self-Esteem. Each item also reflects one of three components (affect, behavior, cognition).\n\nScale: 1 = Strongly Disagree, 5 = Slightly Disagree, 6 = Slightly Agree, 10 = Strongly Agree.",
                'ar' => "مقياس من 75 بنداً يقيس الاتجاهات الريادية عبر أربعة مقاييس فرعية: الإنجاز، والابتكار، والتحكم الشخصي، وتقدير الذات. يعكس كل بند أيضاً أحد المكونات الثلاثة (الانفعال، السلوك، الإدراك).\n\nالمقياس: 1 = لا أوافق بشدة، 5 = لا أوافق قليلاً، 6 = أوافق قليلاً، 10 = أوافق بشدة.",
            ],
            'instructions' => [
                'en' => 'Indicate how much you agree with each statement by selecting a number between 1 and 10. Work as quickly as you can — mark down your first thought without overthinking. Please answer all questions.',
                'ar' => 'حدد مدى موافقتك على كل عبارة باختيار رقم بين 1 و10. أجب بسرعة دون إطالة التفكير، وضع أول ما يتبادر إلى ذهنك. يُرجى الإجابة على جميع الأسئلة.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 1,
                'max' => 10,
            ],
            'scoring_type' => 'category',
            'scoring_config' => [
                'categories' => [
                    ['key' => 'achievement',      'label' => ['en' => 'Achievement',      'ar' => 'الإنجاز'],         'interpretation' => $interp],
                    ['key' => 'innovation',       'label' => ['en' => 'Innovation',       'ar' => 'الابتكار'],        'interpretation' => $interp],
                    ['key' => 'personal_control', 'label' => ['en' => 'Personal Control', 'ar' => 'التحكم الشخصي'],  'interpretation' => $interp],
                    ['key' => 'self_esteem',      'label' => ['en' => 'Self-Esteem',      'ar' => 'تقدير الذات'],     'interpretation' => $interp],
                ],
            ],
            'randomize_questions' => false,
            'chart_type' => 'column',
        ]);

        // Subscale assignments (from the parenthetical tags in the source PDF).
        $categoryMap = [
            1 => 'achievement', 2 => 'innovation', 3 => 'achievement', 4 => 'personal_control',
            5 => 'self_esteem', 6 => 'innovation', 7 => 'achievement', 8 => 'personal_control',
            9 => 'achievement', 10 => 'personal_control', 11 => 'achievement', 12 => 'self_esteem',
            13 => 'innovation', 14 => 'self_esteem', 15 => 'personal_control', 16 => 'self_esteem',
            17 => 'innovation', 18 => 'self_esteem', 19 => 'innovation', 20 => 'achievement',
            21 => 'self_esteem', 22 => 'self_esteem', 23 => 'achievement', 24 => 'achievement',
            25 => 'self_esteem', 26 => 'achievement', 27 => 'achievement', 28 => 'self_esteem',
            29 => 'self_esteem', 30 => 'achievement', 31 => 'achievement', 32 => 'innovation',
            33 => 'self_esteem', 34 => 'achievement', 35 => 'achievement', 36 => 'personal_control',
            37 => 'personal_control', 38 => 'innovation', 39 => 'innovation', 40 => 'achievement',
            41 => 'innovation', 42 => 'personal_control', 43 => 'innovation', 44 => 'achievement',
            45 => 'personal_control', 46 => 'innovation', 47 => 'personal_control', 48 => 'achievement',
            49 => 'innovation', 50 => 'self_esteem', 51 => 'personal_control', 52 => 'innovation',
            53 => 'self_esteem', 54 => 'innovation', 55 => 'self_esteem', 56 => 'innovation',
            57 => 'achievement', 58 => 'innovation', 59 => 'achievement', 60 => 'personal_control',
            61 => 'achievement', 62 => 'innovation', 63 => 'innovation', 64 => 'personal_control',
            65 => 'achievement', 66 => 'innovation', 67 => 'achievement', 68 => 'innovation',
            69 => 'innovation', 70 => 'achievement', 71 => 'innovation', 72 => 'innovation',
            73 => 'innovation', 74 => 'innovation', 75 => 'innovation',
        ];

        // Reverse-scored items (marked with * in the PDF).
        $reverseItems = [5, 14, 18, 21, 28, 29, 33, 37, 38, 50, 66, 73, 74];

        $questions = [
            1  => ['en' => 'I get my biggest thrills when my work is among the best there is.', 'ar' => 'أشعر بأكبر متعة عندما يكون عملي من بين الأفضل.'],
            2  => ['en' => 'I seldom follow instructions unless the task I am working on is too complex.', 'ar' => 'نادراً ما أتبع التعليمات إلا إذا كانت المهمة التي أعمل عليها معقدة جداً.'],
            3  => ['en' => 'I never put important matters off until a more convenient time.', 'ar' => 'لا أؤجل الأمور المهمة أبداً إلى وقت أكثر ملاءمة.'],
            4  => ['en' => 'I have always worked hard in order to be among the best in my field.', 'ar' => 'لقد عملت دائماً بجدٍ لأكون من بين الأفضل في مجالي.'],
            5  => ['en' => 'I feel like a total failure when my business plans don\'t turn out the way I think they should.', 'ar' => 'أشعر بأنني فاشل تماماً عندما لا تسير خططي التجارية على النحو الذي أعتقد أنها يجب أن تسير عليه.'],
            6  => ['en' => 'I feel very energetic working with innovative colleagues in a dynamic business climate.', 'ar' => 'أشعر بحيوية كبيرة عند العمل مع زملاء مبتكرين في بيئة عمل ديناميكية.'],
            7  => ['en' => 'I believe that concrete results are necessary in order to judge business success.', 'ar' => 'أعتقد أن النتائج الملموسة ضرورية للحكم على النجاح التجاري.'],
            8  => ['en' => 'I create the business opportunities I take advantage of.', 'ar' => 'أنا من يصنع الفرص التجارية التي أستفيد منها.'],
            9  => ['en' => 'I spend a considerable amount of time making any organization I belong to function better.', 'ar' => 'أقضي وقتاً كبيراً في جعل أي منظمة أنتمي إليها تعمل بشكل أفضل.'],
            10 => ['en' => 'I know that social and economic conditions will not affect my success in business.', 'ar' => 'أعلم أن الظروف الاجتماعية والاقتصادية لن تؤثر على نجاحي في العمل.'],
            11 => ['en' => 'I believe it is important to analyze your own weaknesses in business dealings.', 'ar' => 'أعتقد أنه من المهم تحليل نقاط ضعفك الخاصة في التعاملات التجارية.'],
            12 => ['en' => 'I usually perform very well on my part of any business project I am involved with.', 'ar' => 'عادةً ما أؤدي جزئي بشكل جيد جداً في أي مشروع تجاري أشارك فيه.'],
            13 => ['en' => 'I get excited when I am able to approach tasks in unusual ways.', 'ar' => 'أشعر بالحماس عندما أستطيع التعامل مع المهام بطرق غير مألوفة.'],
            14 => ['en' => 'I feel very self-conscious when making business proposals.', 'ar' => 'أشعر بحرج كبير عند تقديم المقترحات التجارية.'],
            15 => ['en' => 'I believe that in the business world the work of competent people will always be recognized.', 'ar' => 'أعتقد أن عمل الأشخاص الأكفاء في عالم الأعمال سيُقدَّر دائماً.'],
            16 => ['en' => 'I believe successful people handle themselves well at business gatherings.', 'ar' => 'أعتقد أن الأشخاص الناجحين يحسنون التصرف في التجمعات التجارية.'],
            17 => ['en' => 'I enjoy being able to use old business concepts in new ways.', 'ar' => 'أستمتع بقدرتي على استخدام مفاهيم تجارية قديمة بطرق جديدة.'],
            18 => ['en' => 'I seem to spend a lot of time looking for someone who can tell me how to solve all my business problems.', 'ar' => 'يبدو أنني أمضي وقتاً طويلاً في البحث عن شخص يخبرني كيف أحل جميع مشاكلي التجارية.'],
            19 => ['en' => 'I feel terribly restricted being tied down to tightly organized business activities, even when I am in control.', 'ar' => 'أشعر بتقييد شديد عند الالتزام بأنشطة تجارية منظمة بإحكام، حتى عندما أكون أنا المسيطر.'],
            20 => ['en' => 'I often sacrifice personal comfort in order to take advantage of business opportunities.', 'ar' => 'كثيراً ما أضحي بالراحة الشخصية من أجل الاستفادة من الفرص التجارية.'],
            21 => ['en' => 'I feel self-conscious when I am with very successful business people.', 'ar' => 'أشعر بحرج عندما أكون مع أشخاص ناجحين جداً في الأعمال.'],
            22 => ['en' => 'I believe that to succeed in business it is important to get along with the people you work with.', 'ar' => 'أعتقد أنه للنجاح في الأعمال من المهم الانسجام مع الأشخاص الذين تعمل معهم.'],
            23 => ['en' => 'I do every job as thoroughly as possible.', 'ar' => 'أؤدي كل عمل بأقصى قدر ممكن من الدقة والشمول.'],
            24 => ['en' => 'To be successful I believe it is important to use your time wisely.', 'ar' => 'للنجاح، أعتقد أنه من المهم استخدام وقتك بحكمة.'],
            25 => ['en' => 'I believe that the authority I have in business is due mainly to my expertise in certain areas.', 'ar' => 'أعتقد أن السلطة التي أمتلكها في العمل تعود بشكل رئيسي إلى خبرتي في مجالات معينة.'],
            26 => ['en' => 'I believe that to be successful a businessman must spend time planning the future of his business.', 'ar' => 'أعتقد أن على رجل الأعمال الناجح أن يقضي وقتاً في التخطيط لمستقبل عمله.'],
            27 => ['en' => 'I make a conscientious effort to get the most out of my business resources.', 'ar' => 'أبذل جهداً واعياً للاستفادة القصوى من موارد عملي.'],
            28 => ['en' => 'I feel uncomfortable when I\'m unsure of what my business associates think of me.', 'ar' => 'أشعر بعدم الراحة عندما لا أكون متأكداً مما يفكر فيه شركائي في العمل تجاهي.'],
            29 => ['en' => 'I often put on a show to impress the people I work with.', 'ar' => 'كثيراً ما أتظاهر لإبهار الأشخاص الذين أعمل معهم.'],
            30 => ['en' => 'I believe that one key to success in business is to not procrastinate.', 'ar' => 'أعتقد أن أحد مفاتيح النجاح في العمل هو عدم التسويف.'],
            31 => ['en' => 'I get a sense of pride when I do a good job on my business projects.', 'ar' => 'أشعر بالفخر عندما أنجز عملاً جيداً في مشاريعي التجارية.'],
            32 => ['en' => 'I believe that organizations which don\'t experience radical changes now and then tend to get stuck in a rut.', 'ar' => 'أعتقد أن المنظمات التي لا تشهد تغييرات جذرية من حين لآخر تميل إلى الجمود في الروتين.'],
            33 => ['en' => 'I feel inferior to most people I work with.', 'ar' => 'أشعر بأنني أقل شأناً من معظم الأشخاص الذين أعمل معهم.'],
            34 => ['en' => 'I think that to succeed in business these days you must eliminate inefficiencies.', 'ar' => 'أعتقد أنه للنجاح في الأعمال هذه الأيام يجب القضاء على أوجه القصور.'],
            35 => ['en' => 'I feel proud when I look at the results I have achieved in my business activities.', 'ar' => 'أشعر بالفخر عندما أنظر إلى النتائج التي حققتها في أنشطتي التجارية.'],
            36 => ['en' => 'I feel resentful when I get bossed around at work.', 'ar' => 'أشعر بالاستياء عندما يفرض علي الآخرون أوامرهم في العمل.'],
            37 => ['en' => 'Even though I spend some time trying to influence business events around me every day, I have had very little success.', 'ar' => 'على الرغم من أنني أقضي بعض الوقت يومياً في محاولة التأثير في الأحداث التجارية من حولي، إلا أنني حققت نجاحاً ضئيلاً جداً.'],
            38 => ['en' => 'I feel best about my work when I know I have followed accepted procedures.', 'ar' => 'أشعر بأفضل ما يكون تجاه عملي عندما أعلم أنني اتبعت الإجراءات المقبولة.'],
            39 => ['en' => 'Most of my time is spent working on several business ideas at the same time.', 'ar' => 'أقضي معظم وقتي في العمل على عدة أفكار تجارية في الوقت نفسه.'],
            40 => ['en' => 'I believe it is more important to think about future possibilities than past accomplishments.', 'ar' => 'أعتقد أن التفكير في الاحتمالات المستقبلية أكثر أهمية من التفكير في الإنجازات السابقة.'],
            41 => ['en' => 'I believe that in order to succeed, one must conform to accepted business practices.', 'ar' => 'أعتقد أنه للنجاح يجب على المرء الالتزام بالممارسات التجارية المقبولة.'],
            42 => ['en' => 'I believe that any organization can become more effective by employing competent people.', 'ar' => 'أعتقد أن أي منظمة يمكن أن تصبح أكثر فاعلية بتوظيف أشخاص أكفاء.'],
            43 => ['en' => 'I usually delegate routine tasks after only a short period of time.', 'ar' => 'عادةً ما أُفوّض المهام الروتينية بعد فترة قصيرة فقط.'],
            44 => ['en' => 'I will spend a considerable amount of time analyzing my future business needs before I allocate any resources.', 'ar' => 'سأقضي وقتاً كبيراً في تحليل احتياجات عملي المستقبلية قبل تخصيص أي موارد.'],
            45 => ['en' => 'I feel very good because I am ultimately responsible for my own business success.', 'ar' => 'أشعر بالرضا الكبير لأنني المسؤول في النهاية عن نجاح عملي.'],
            46 => ['en' => 'I believe that to become successful in business you must spend some time every day developing new opportunities.', 'ar' => 'أعتقد أنه للنجاح في العمل يجب أن تقضي بعض الوقت يومياً في تطوير فرص جديدة.'],
            47 => ['en' => 'I get excited creating my own business opportunities.', 'ar' => 'أشعر بالحماس عند صنع فرص عملي الخاصة.'],
            48 => ['en' => 'I make it a point to do something significant and meaningful at work every day.', 'ar' => 'أحرص على القيام بشيء مهم وذي معنى في العمل كل يوم.'],
            49 => ['en' => 'I usually take control in unstructured situations.', 'ar' => 'عادةً ما أتولى زمام الأمور في المواقف غير المنظمة.'],
            50 => ['en' => 'I never persist very long on a difficult job before giving up.', 'ar' => 'لا أثابر طويلاً على عمل صعب قبل أن أستسلم.'],
            51 => ['en' => 'I spend a lot of time planning my business activities.', 'ar' => 'أقضي وقتاً طويلاً في التخطيط لأنشطتي التجارية.'],
            52 => ['en' => 'I believe that to arrive at a good solution to a business problem, it is important to question the assumptions made in defining the problem.', 'ar' => 'أعتقد أنه للوصول إلى حل جيد لمشكلة تجارية، من المهم التشكيك في الافتراضات التي وضعت في تعريف المشكلة.'],
            53 => ['en' => 'I often feel badly about the quality of work I do.', 'ar' => 'كثيراً ما أشعر بالسوء حيال جودة العمل الذي أؤديه.'],
            54 => ['en' => 'I believe it is important to continually look for new ways to do things in business.', 'ar' => 'أعتقد أنه من المهم البحث المستمر عن طرق جديدة لإنجاز الأمور في العمل.'],
            55 => ['en' => 'I believe it is important to make a good first impression.', 'ar' => 'أعتقد أنه من المهم ترك انطباع أول جيد.'],
            56 => ['en' => 'I believe that when pursuing business goals or objectives, the final result is far more important than following the accepted procedures.', 'ar' => 'أعتقد أنه عند السعي وراء الأهداف التجارية، فإن النتيجة النهائية أهم بكثير من اتباع الإجراءات المقبولة.'],
            57 => ['en' => 'I feel depressed when I don\'t accomplish any meaningful work.', 'ar' => 'أشعر بالإحباط عندما لا أنجز أي عمل ذي معنى.'],
            58 => ['en' => 'I often approach business tasks in unique ways.', 'ar' => 'كثيراً ما أتعامل مع المهام التجارية بطرق فريدة.'],
            59 => ['en' => 'I believe the most important thing in selecting business associates is their competency.', 'ar' => 'أعتقد أن الأهم في اختيار شركاء العمل هو كفاءتهم.'],
            60 => ['en' => 'I take an active part in community affairs so that I can influence events that affect my business.', 'ar' => 'أشارك بنشاط في شؤون المجتمع كي أستطيع التأثير في الأحداث التي تؤثر على عملي.'],
            61 => ['en' => 'I feel good when I have worked hard to improve my business.', 'ar' => 'أشعر بالرضا عندما أعمل بجد لتحسين عملي.'],
            62 => ['en' => 'I enjoy finding good solutions for problems that nobody has looked at yet.', 'ar' => 'أستمتع بإيجاد حلول جيدة لمشكلات لم ينظر إليها أحد بعد.'],
            63 => ['en' => 'I believe that to be successful a company must use business practices that may seem unusual at first glance.', 'ar' => 'أعتقد أنه على الشركة الناجحة استخدام ممارسات تجارية قد تبدو غير اعتيادية للوهلة الأولى.'],
            64 => ['en' => 'My knack for dealing with people has enabled me to create many of my business opportunities.', 'ar' => 'مكنتني مهارتي في التعامل مع الناس من صنع كثير من فرص عملي.'],
            65 => ['en' => 'I get a sense of accomplishment from the pursuit of my business opportunities.', 'ar' => 'أشعر بالإنجاز من خلال السعي وراء فرصي التجارية.'],
            66 => ['en' => 'I believe that currently accepted regulations were established for a good reason.', 'ar' => 'أعتقد أن اللوائح المقبولة حالياً قد وُضعت لسبب وجيه.'],
            67 => ['en' => 'I always feel good when I make the organizations I belong to function better.', 'ar' => 'أشعر دائماً بالرضا عندما أجعل المنظمات التي أنتمي إليها تعمل بشكل أفضل.'],
            68 => ['en' => 'I get real excited when I think of new ideas to stimulate my business.', 'ar' => 'أشعر بحماس حقيقي عندما أفكر في أفكار جديدة لتنشيط عملي.'],
            69 => ['en' => 'I believe it is important to approach business opportunities in unique ways.', 'ar' => 'أعتقد أنه من المهم التعامل مع الفرص التجارية بطرق فريدة.'],
            70 => ['en' => 'I always try to make friends with people who may be useful in my business.', 'ar' => 'أحاول دائماً تكوين صداقات مع الأشخاص الذين قد يكونون مفيدين لعملي.'],
            71 => ['en' => 'I usually seek out colleagues who are excited about exploring new ways of doing things.', 'ar' => 'عادةً ما أبحث عن زملاء متحمسين لاستكشاف طرق جديدة للقيام بالأشياء.'],
            72 => ['en' => 'I enjoy being the catalyst for change in business affairs.', 'ar' => 'أستمتع بكوني المحرك للتغيير في الشؤون التجارية.'],
            73 => ['en' => 'I always follow accepted business practices in the dealings I have with others.', 'ar' => 'أتبع دائماً الممارسات التجارية المقبولة في تعاملاتي مع الآخرين.'],
            74 => ['en' => 'I rarely question the value of established procedures.', 'ar' => 'نادراً ما أشكك في قيمة الإجراءات المعتمدة.'],
            75 => ['en' => 'I get a thrill out of doing new, unusual things in my business affairs.', 'ar' => 'أشعر بمتعة في القيام بأشياء جديدة وغير مألوفة في شؤون عملي.'],
        ];

        foreach ($questions as $qNum => $q) {
            Question::create([
                'test_id' => $test->id,
                'text' => ['en' => $q['en'], 'ar' => $q['ar']],
                'sort_order' => $qNum,
                'is_reverse_scored' => in_array($qNum, $reverseItems),
                'is_required' => true,
                'category_key' => $categoryMap[$qNum],
                'weight' => 1.00,
            ]);
        }

        $this->command->info("Created Entrepreneurial Attitude Orientation Scale (EAO) with {$test->questions()->count()} questions (" . count($reverseItems) . " reverse-scored) across 4 subscales.");
    }
}
