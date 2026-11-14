<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class CreativityTestSeeder extends Seeder
{
    public const TITLE_AR = 'هل أنت مبدع؟';

    /**
     * Items where "Agree" is the creative answer. On every other item "Disagree" is,
     * so it is reverse scored.
     */
    public const AGREE_IS_CREATIVE = [4, 5, 6, 8, 9, 11, 12, 13, 14, 15, 16, 18, 19, 21, 22, 23, 24, 26];

    public function run(): void
    {
        $admin = User::first();

        $test = Test::create([
            'user_id' => $admin->id,
            'title' => [
                'en' => 'Are You Creative?',
                'ar' => self::TITLE_AR,
            ],
            'description' => [
                'en' => 'A 30-item creativity assessment that measures your creative thinking tendencies and innovation potential.',
                'ar' => 'اختبار من 30 بنداً يقيس ميولك في التفكير الإبداعي وإمكاناتك الابتكارية.',
            ],
            'instructions' => [
                'en' => 'After reading each statement, select your answer: Agree, Neutral/Don\'t know, or Disagree. Answer spontaneously, accurately, and honestly. Do not try to guess what a creative person\'s answer would be — let your answer be personal.',
                'ar' => 'بعد قراءة كل عبارة من عبارات هذا الاختبار حدد إجابتك: موافق، محايد أو لا أعرف، أو غير موافق. أجب بعفوية ودقة وأمانة قدر الإمكان ولا تحاول أن تخمّن ما ستكون عليه إجابة الأشخاص المبدعين. فلتكن إجابتك إجابة شخصية.',
            ],
            'status' => 'published',
            'scale_config' => [
                'min' => 1,
                'max' => 3,
                'labels' => [
                    '1' => ['en' => 'Agree', 'ar' => 'موافق'],
                    '2' => ['en' => 'Neutral / Don\'t know', 'ar' => 'محايد / لا أعرف'],
                    '3' => ['en' => 'Disagree', 'ar' => 'غير موافق'],
                ],
            ],
            'scoring_type' => 'range',
            'scoring_config' => [
                'use_percentage' => false,
                'ranges' => self::ranges(),
            ],
            'randomize_questions' => false,
        ]);

        foreach (self::questions() as $qNum => $q) {
            Question::create([
                'test_id' => $test->id,
                'text' => ['en' => $q['en'], 'ar' => $q['ar']],
                'sort_order' => $qNum,
                'is_reverse_scored' => false,
                'is_required' => true,
                'category_key' => null,
                'weight' => 1.00,
                'scale_override' => [
                    'min' => 1,
                    'max' => 3,
                    'labels' => [
                        '1' => ['en' => 'Agree', 'ar' => 'موافق'],
                        '2' => ['en' => 'Neutral / Don\'t know', 'ar' => 'محايد / لا أعرف'],
                        '3' => ['en' => 'Disagree', 'ar' => 'غير موافق'],
                    ],
                    'score_map' => self::scoreMap($qNum),
                ],
            ]);
        }

        $this->command->info("Created 'Are You Creative?' test with {$test->questions()->count()} questions.");
    }

    /**
     * Points per answer (1 = Agree, 2 = Neutral, 3 = Disagree): the creative answer
     * scores 5, Neutral 3, the other answer 1.
     */
    public static function scoreMap(int $qNum): array
    {
        return in_array($qNum, self::AGREE_IS_CREATIVE, true)
            ? ['1' => 5, '2' => 3, '3' => 1]
            : ['1' => 1, '2' => 3, '3' => 5];
    }

    /** Bands for the raw total of the 30 items (30-150). */
    public static function ranges(): array
    {
        return [
            ['min' => 30, 'max' => 50, 'label' => ['en' => 'Not Creative', 'ar' => 'غير مبتكر'], 'description' => ['en' => 'You tend to follow conventional approaches', 'ar' => 'تميل إلى اتباع الأساليب التقليدية']],
            ['min' => 51, 'max' => 70, 'label' => ['en' => 'Below Average', 'ar' => 'تحت المتوسط'], 'description' => ['en' => 'Your creativity is below average', 'ar' => 'إبداعك تحت المتوسط']],
            ['min' => 71, 'max' => 90, 'label' => ['en' => 'Average', 'ar' => 'متوسط'], 'description' => ['en' => 'You have average creative abilities', 'ar' => 'لديك قدرات إبداعية متوسطة']],
            ['min' => 91, 'max' => 110, 'label' => ['en' => 'Above Average', 'ar' => 'فوق المتوسط'], 'description' => ['en' => 'Your creativity is above average', 'ar' => 'إبداعك فوق المتوسط']],
            ['min' => 111, 'max' => 130, 'label' => ['en' => 'Very Creative', 'ar' => 'مبتكر جداً'], 'description' => ['en' => 'You are highly creative', 'ar' => 'أنت مبتكر بدرجة عالية']],
            ['min' => 131, 'max' => 150, 'label' => ['en' => 'Exceptionally Creative', 'ar' => 'مبتكر غير اعتيادي'], 'description' => ['en' => 'You have exceptional creative abilities', 'ar' => 'لديك قدرات إبداعية استثنائية']],
        ];
    }

    /** The 30 items, numbered as in the source. */
    public static function questions(): array
    {
        return [
            1  => ['en' => 'I consider the best way to solve problems is the logical step-by-step approach', 'ar' => 'اعتبر أن أفضل طريقة لحل المشكلات هي الطريقة القائمة على التسلسل المنطقي (خطوة خطوة)'],
            2  => ['en' => 'Asking questions that cannot be answered is a waste of time for me', 'ar' => 'إن طرح الأسئلة التي لا يمكن الحصول على أجوبة عنها يشكل بالنسبة لي مضيعة للوقت'],
            3  => ['en' => 'I always work with full certainty that I have the best methods for solving problems', 'ar' => 'أعمل دائماً بيقين تام بأنني أملك أفضل الطرق لحل المشكلات'],
            4  => ['en' => 'I focus on topics that interest me with an intensity that exceeds most other people', 'ar' => 'أركز على الموضوعات التي تهمني تركيزاً شديداً يفوق ما يقدر عليه معظم الأشخاص الآخرين'],
            5  => ['en' => 'When I try to solve a problem, I spend a long time analyzing it', 'ar' => 'عندما أحاول حل مشكلة معينة فإنني أستغرق وقتاً طويلاً في تحليلها'],
            6  => ['en' => 'I occasionally express opinions that others do not share', 'ar' => 'أعبر من حين لآخر عن آراء لا يشاركني فيها الأشخاص الآخرون'],
            7  => ['en' => 'I spend a long time questioning myself about what others think of me', 'ar' => 'أستهلك وقتاً طويلاً في مساءلة نفسي عما يفكر به الآخرون عنى'],
            8  => ['en' => 'I am interested in complex problems and situations because I consider them challenges', 'ar' => 'أهتم بالمشكلات والمواقف المعقدة لأنني اعتبرها تحديات بالنسبة لي'],
            9  => ['en' => 'Doing what I believe is best is more important to me than trying to gain others\' approval', 'ar' => 'قيامي بما اعتقد أنه الأفضل أهم بالنسبة لي من محاولة الحصول على استحسان الآخرين'],
            10 => ['en' => 'I do not appreciate people who appear uncertain about certain topics', 'ar' => 'لا أشعر بالتقدير للأشخاص الذين يظهرون بمظهر غير الواثق وغير المتأكد من موضوعات معينة'],
            11 => ['en' => 'I mostly feel the need to deal with important and exciting matters', 'ar' => 'أشعر في معظم الأحيان بالحاجة إلى معالجة الأمور المهمة والمثيرة'],
            12 => ['en' => 'When looking for a solution to a problem, I rely on my personal feeling of what is right and wrong', 'ar' => 'عندما أبحث عن حل لمشكلة معينة فإنني أعتمد على شعوري الشخصي بما هو صحيح وما هو خاطئ'],
            13 => ['en' => 'I believe I have untapped potential', 'ar' => 'اعتقد بأنني أملك طاقات ما تزال غير مستغلة حتى الآن'],
            14 => ['en' => 'Daydreaming is a starting point for many of my important projects', 'ar' => 'تشكل أحلام اليقظة عندي منطلقاً للعديد من المشاريع الهامة'],
            15 => ['en' => 'I like objective and rational people', 'ar' => 'أحب الأشخاص الموضوعيين والعقلانيين'],
            16 => ['en' => 'I consider myself more enthusiastic than most people I know', 'ar' => 'اعتبر نفسي أشد حماسة من معظم الأشخاص الذين أعرفهم'],
            17 => ['en' => 'I feel more comfortable with people who belong to my social and economic class', 'ar' => 'أشعر براحة أكبر مع الأشخاص الذين ينتمون إلى الفئة الاجتماعية والاقتصادية التي أنتمي إليها'],
            18 => ['en' => 'I like people who have great confidence in their opinions', 'ar' => 'أحب الأشخاص الذين يثقون ثقة كبيرة بآرائهم'],
            19 => ['en' => 'I am willing during a discussion to give up my point of view to win someone as a friend', 'ar' => 'إني مستعد أثناء النقاش للتنازل عن وجهة نظري لشخص آخر يهدف أن أكسبه صديقاً لي'],
            20 => ['en' => 'I avoid situations that make me feel inferior', 'ar' => 'أبتعد عن المواقف التي تسبب لي شعوراً بالنقص'],
            21 => ['en' => 'When evaluating information, I care more about its source than its content', 'ar' => 'أثناء تقييم المعلومات أهتم بمصدرها أكثر من اهتمامي بمضمونها'],
            22 => ['en' => 'I resolve uncertain and unexpected matters', 'ar' => 'أنهيت الأمور غير المؤكدة وغير المتوقعة'],
            23 => ['en' => 'I can maintain my drive and enthusiasm for my projects in the face of obstacles', 'ar' => 'أستطيع أن احتفظ باندفاعي وحماستي لمشاريعي في وجه العقبات والمعارضات التي تعترضنى'],
            24 => ['en' => 'I care about the future more than the present', 'ar' => 'أهتم بالمستقبل أكثر من اهتمامي بالحاضر'],
            25 => ['en' => 'I do not like to ask questions that make me appear ignorant', 'ar' => 'لا أحب أن أطرح الأسئلة التي تظهرني بمظهر الجاهل'],
            26 => ['en' => 'When I start a project, I am determined to finish it even under the most difficult circumstances', 'ar' => 'عندما أبدأ مشروعاً فإنني أصمم على إنهائه حتى في أصعب الظروف'],
            27 => ['en' => 'I sometimes believe that ideas come to me from an external source and I am not directly responsible for generating them', 'ar' => 'اعتقد أحياناً بأن الأفكار تأتيني من مصدر خارجي وبأنني غير مسئول مباشرة عن توليدها'],
            28 => ['en' => 'Theoretically-oriented people are less important than practically-oriented people', 'ar' => 'الأشخاص ذوو التوجه النظري أقل أهمية من الأشخاص ذوى التوجه العملي'],
            29 => ['en' => 'I am not interested in problems that are easy to solve', 'ar' => 'لا تهمني المشكلات التي يسهل حلها'],
            30 => ['en' => 'Ideas that seem obvious to others do not seem so to me', 'ar' => 'الأفكار التي تبدو بديهية بالنسبة للآخرين لا تبدو لي كذلك'],
        ];
    }
}
