<?php

namespace Database\Seeders\StudentAbilities;

use App\Models\Question;
use App\Models\Test;

/**
 * Holland Vocational Factors Scale / مقياس هولاند للعوامل المهنية.
 *
 * Source: "مقياس هولاند.pdf" (HVIS — the 180 items and their three answer scales) and
 * "jadid holland.pdf" (the six personality-type descriptions shown with the result).
 *
 * 180 items in three parts of 60 (activities, skills, occupations), each answered on a
 * three-point scale scored 5 / 3 / 1. Within every part the items follow the fixed
 * Holland order R, I, A, S, E, C, so item n belongs to type (n - 1) mod 6 — 30 items per
 * type, 30-150 points. The result is the three highest-scoring types, ties broken in
 * R, I, A, S, E, C order, each shown with its description.
 *
 * Source corrections (spelling only): part 1 item 30 "أوإدارة" → "أو إدارة"; part 1 item 36
 * "ومحدودة" → "ومحددة"; part 2 item 24 "وحفظها" → "وحفظه" (agrees with العمل); part 2
 * item 34 "تعلم ... بسهوله" → "تعليم ... بسهولة"; "إتباع" → "اتباع" and "الإستقصائية /
 * الإجتماعية" → "الاستقصائية / الاجتماعية" in the descriptions; spelling fixes in the
 * instructions (فى، التى، يلى، اتيحت، الحقيقة).
 */
class HollandVocationalScale
{
    public const TITLE_AR = 'مقياس هولاند للعوامل المهنية';

    /** Holland types in the order the items cycle through them (also the tie-break order). */
    public const TYPES = ['R', 'I', 'A', 'S', 'E', 'C'];

    /** Answer option => points; option 1 is the strongest preference in every part. */
    public const SCORE_MAP = ['1' => 5, '2' => 3, '3' => 1];

    public static function definition(): array
    {
        return [
            'title' => [
                'en' => 'Holland Vocational Factors Scale',
                'ar' => self::TITLE_AR,
            ],
            'description' => [
                'en' => 'A 180-item scale of vocational interests based on Holland\'s six personality types (Realistic, Investigative, Artistic, Social, Enterprising, Conventional). The result shows your three strongest types.',
                'ar' => 'مقياس من 180 عبارة للتعرّف على الميول المهنية وفق أنماط هولاند الستة للشخصية (الواقعية، الاستقصائية، الفنية، الاجتماعية، المبادرة، التقليدية). تعرض النتيجة أبرز ثلاثة أنماط لديك.',
            ],
            'instructions' => [
                'en' => "This scale has three parts.\nPart 1 (questions 1-60): statements describing different activities we do in life. We may like some of them and not others. Choose how much you like each activity: Like, Somewhat like, or Dislike.\nPart 2 (questions 61-120): statements describing different skills we may or may not be good at. Choose how good you are at each skill: High, Moderate, or Not good at it.\nPart 3 (questions 121-180): jobs and occupations. We may like some of them, lean towards them and want to do them if we had the chance, while others don't match our real interests. For each one, choose whether you would want (or would have wanted) to do it if you had the chance: Agree, Somewhat agree, or Disagree.",
                'ar' => "يتكوّن هذا المقياس من ثلاثة أجزاء.\nالجزء الأول (العبارات 1-60): فيما يلي مجموعة من العبارات التي تمثل أنشطة مختلفة نمارسها في الحياة، بعضها قد نحبه ولا نحب البعض الآخر، والمطلوب منك تحديد درجة تفضيلك لكل نشاط من هذه الأنشطة: أحب، أحب إلى حد ما، لا أحب.\nالجزء الثاني (العبارات 61-120): فيما يلي مجموعة من العبارات التي تمثل مهارات مختلفة قد نجيدها أو لا نجيدها، والمطلوب منك تحديد درجة إجادتك لكل مهارة من هذه المهارات: عالية، متوسطة، لا أجيد.\nالجزء الثالث (العبارات 121-180): فيما يلي مجموعة من المهن والأعمال التي قد نحب بعضها ونميل إليها ونرغب في ممارستها إذا أتيحت الفرصة لذلك، والبعض الآخر لا نميل إليه ولا يتماشى مع ميولنا ورغباتنا الحقيقية، والمطلوب منك أن تحدد لكل مهنة إن كنت ترغب (أو كنت ترغب) في القيام بها إذا توافرت الفرصة لذلك: موافق، موافق إلى حد ما، غير موافق.",
            ],
            'types' => self::types(),
            'parts' => self::parts(),
        ];
    }

    /**
     * Create the test with its 180 questions for the given owner. Shared by the
     * Student Abilities seeder and the migration that adds it to existing installs.
     */
    public static function create(int $userId): Test
    {
        $def = self::definition();
        $types = $def['types'];

        $test = Test::create([
            'user_id' => $userId,
            'title' => $def['title'],
            'description' => $def['description'],
            'instructions' => $def['instructions'],
            'status' => 'published',
            'scale_config' => ['min' => 1, 'max' => 3, 'labels' => $def['parts'][0]['labels']],
            'scoring_type' => 'category',
            'scoring_config' => [
                'categories' => array_map(fn ($key) => [
                    'key' => $key,
                    'label' => $types[$key]['label'],
                    'description' => $types[$key]['description'],
                    'interpretation' => [],
                ], self::TYPES),
                'top_categories' => 3,
            ],
            'chart_type' => 'bar',
            'randomize_questions' => false,
        ]);

        $sortOrder = 0;
        foreach ($def['parts'] as $part) {
            foreach ($part['items'] as $i => [$ar, $en]) {
                Question::create([
                    'test_id' => $test->id,
                    'text' => ['en' => $en, 'ar' => $ar],
                    'sort_order' => ++$sortOrder,
                    'is_reverse_scored' => false,
                    'is_required' => true,
                    'category_key' => self::TYPES[$i % 6],
                    'weight' => 1.00,
                    'scale_override' => [
                        'min' => 1,
                        'max' => 3,
                        'labels' => $part['labels'],
                        'score_map' => self::SCORE_MAP,
                    ],
                ]);
            }
        }

        return $test;
    }

    private static function types(): array
    {
        return [
            'R' => [
                'label' => ['en' => 'Realistic (R)', 'ar' => 'الشخصية الواقعية (R)'],
                'description' => [
                    'ar' => "يرغب أصحاب هذه الشخصية باستعمال الآلات والأدوات المحسوسة، ويدفعهم إلى العمل حبّ الإنجاز.\nيتميز هؤلاء الأفراد بالصفات الآتية: المطابقة، الصرامة، العفوية، العناد، الصدق، التواضع، الفطرية، الاعتدال، المثابرة، العملانية، الخجل، الاقتصاد (التوفير).\nيمتازون بالقدرات البدنية والميكانيكية، ويرغبون بالعمل خارجاً ويبتعدون عن المهن التي تتطلب البقاء طويلاً داخل المكاتب والغرف المغلقة.\nينظرون إلى ذواتهم كأصحاب شخصية عملية تمتلك المهارات اليدوية أكثر من المهارات الاجتماعية.\nيعملون في مهن تستلزم أنشطة جسدية وتتضمن استعمال الآلات والأدوات ذاتية التشغيل، لذا فهم ينجحون في مهن محدّدة كالهندسة، والزراعة، وقيادة المركبات الهوائية كالطائرات العادية أو الهيليكوبتر، والمساحة، والكهربائيات.\nيراهم الآخرون أصحاب شخصية عادية وسويّة بالرغم من أنّهم يسعون إلى تجنّب التفاعل مع الآخرين.",
                    'en' => "People with this personality like using machines and hands-on tools, and they are driven to work by a love of achievement.\nThey are characterized by the following traits: conformity, strictness, spontaneity, stubbornness, honesty, modesty, naturalness, moderation, perseverance, practicality, shyness, and thrift.\nThey have strong physical and mechanical abilities, like working outdoors, and stay away from jobs that require staying for long hours in offices and closed rooms.\nThey see themselves as practical people who have more manual skills than social skills.\nThey work in jobs that require physical activity and the use of machines and self-operating tools, so they succeed in fields such as engineering, agriculture, flying aircraft such as airplanes or helicopters, surveying, and electrical work.\nOthers see them as ordinary, well-balanced people, even though they try to avoid interacting with others.",
                ],
            ],
            'I' => [
                'label' => ['en' => 'Investigative (I)', 'ar' => 'الشخصية الاستقصائية (I)'],
                'description' => [
                    'ar' => "يرغب أصحاب هذه الشخصية بفهم واستكشاف الأشياء المحيطة بهم، ويدفعهم حبّ العمل والتعلّم.\nيمتلكون قدرات رياضية وعلمية، ويميلون للعمل المنفرد وحل المسائل.\nينظرون إلى ذواتهم كأصحاب شخصية تحليلية تمتلك المهارات النظرية أكثر من المهارات الاجتماعية.\nيراهم الآخرون أصحاب شخصية ذكية ومنطوية على نفسها، لذا من الأفضل أن يتجنّبوا العمل بمجالات البيع والتسويق.\nيتميز هؤلاء الأفراد بالصفات الآتية: التحليل، الحذر، الدقة، النقد، الفضول العلمي، الاستقلالية في التفكير، الذاتية (التركيز على أفكاره ومشاعره الذاتية)، المنهجية، العقلانية، التحفظ.\nيعملون في مهن تستلزم أنشطة بحثية وفكرية وقدرة على حلّ المشكلات، لذا فهم ينجحون في مهن محدّدة كالعلوم النفسية، الكيمياء، الفيزياء، الجيولوجيا، المختبر، الطب.",
                    'en' => "People with this personality like understanding and exploring the things around them, and they are driven by a love of work and learning.\nThey have mathematical and scientific abilities, and they prefer working alone and solving problems.\nThey see themselves as analytical people who have more theoretical skills than social skills.\nOthers see them as intelligent and introverted, so they are better off avoiding work in sales and marketing.\nThey are characterized by the following traits: analysis, caution, precision, critical thinking, scientific curiosity, independent thinking, subjectivity (focusing on their own thoughts and feelings), methodical thinking, rationality, and reserve.\nThey work in jobs that require research, intellectual activity and the ability to solve problems, so they succeed in fields such as psychology, chemistry, physics, geology, laboratory work, and medicine.",
                ],
            ],
            'A' => [
                'label' => ['en' => 'Artistic (A)', 'ar' => 'الشخصية الفنية (A)'],
                'description' => [
                    'ar' => "يرغب أصحاب هذه الشخصية بالتأليف والقيام بالأنشطة الموسيقية والفنية، وتدفعهم الأفكار الخلّاقة والمشاعر الجيّاشة.\nيمتلكون مهارات فنية، ويستمتعون بإبداع أعمال جديدة، ولديهم مخيلة واسعة.\nينظرون إلى ذواتهم كأصحاب شخصية منفتحة على التجربة، تمتلك المهارات الإبداعية أكثر من المهارات المكتبية الروتينية.\nيراهم الآخرون أصحاب شخصية نادرة، متحرّرة ومبدعة، لذا من الأفضل أن يتجنّبوا العمل بمجالات تتطلب اتباع قواعد صارمة وطرق روتينية.\nيتميز هؤلاء الأفراد بالصفات الآتية: عدم الانتظام، العاطفة الجياشة، التعبير عن الذات، المثالية، الخيال الواسع، اللاعملانية، الاندفاع، الاستقلال، النقد الذاتي، الحدس، الانفتاح، الابتكار.\nيعملون في مهن تستلزم أنشطة إبداعية في الموسيقى والفنون والكتابة، لذا فهم ينجحون في مهن محدّدة كالتصميم الداخلي، العزف والتأليف الموسيقي، النحت، الصحافة، المسرح، والتمثيل.",
                    'en' => "People with this personality like composing and doing musical and artistic activities, and they are driven by creative ideas and strong emotions.\nThey have artistic skills, enjoy creating new works, and have a broad imagination.\nThey see themselves as open to new experiences, with more creative skills than routine office skills.\nOthers see them as rare, free-spirited and creative people, so they are better off avoiding fields that require following strict rules and routine methods.\nThey are characterized by the following traits: disorderliness, strong emotions, self-expression, idealism, broad imagination, impracticality, impulsiveness, independence, self-criticism, intuition, openness, and innovation.\nThey work in jobs that require creative activity in music, the arts and writing, so they succeed in fields such as interior design, playing and composing music, sculpture, journalism, theater, and acting.",
                ],
            ],
            'S' => [
                'label' => ['en' => 'Social (S)', 'ar' => 'الشخصية الاجتماعية (S)'],
                'description' => [
                    'ar' => "يرغب أصحاب هذه الشخصية بمساعدة الآخرين وتعليمهم وإرشادهم، ويدفعهم حبّ العمل الصالح والخدمة الاجتماعية.\nيمتلكون مهارات اجتماعية، ويستمتعون بإرشاد الآخرين وحلّ مشكلاتهم، ولديهم قدرة على الإقناع.\nينظرون إلى ذواتهم كأصحاب شخصية متفهّمة للآخرين، تمتلك المهارات الاجتماعية أكثر من المهارات المكتبية الميكانيكية اليدوية.\nيراهم الآخرون أصحاب شخصية مقبولة، ذات نزعة إنسانية، ومحبّة للتفاعل مع الآخرين، لذا من الأفضل أن يتجنّبوا العمل بالمجالات التقنية والميكانيكية.\nيتميز هؤلاء الأفراد بالصفات الآتية: الإقناع، التعاون، الود، الكرم، الإفادة، المثالية، الحنان، الصبر، المسؤولية، الحس الاجتماعي، العطف، اللباقة، التسامح، الدفء.\nيعملون في مهن تستلزم أنشطة التعامل مع الآخرين ومساعدتهم وإرشادهم، لذا فهم ينجحون في مهن محدّدة كالتعليم، التمريض، التوجيه والإرشاد التربوي والاجتماعي، العلوم الدينية، تقويم النطق.",
                    'en' => "People with this personality like helping, teaching and guiding others, and they are driven by a love of doing good and of social service.\nThey have social skills, enjoy guiding others and solving their problems, and are able to persuade.\nThey see themselves as people who understand others, with more social skills than office, mechanical or manual skills.\nOthers see them as likable, humane people who enjoy interacting with others, so they are better off avoiding technical and mechanical fields.\nThey are characterized by the following traits: persuasiveness, cooperation, friendliness, generosity, helpfulness, idealism, tenderness, patience, responsibility, social awareness, compassion, tact, tolerance, and warmth.\nThey work in jobs that involve dealing with others, helping them and guiding them, so they succeed in fields such as teaching, nursing, educational and social guidance and counseling, religious studies, and speech therapy.",
                ],
            ],
            'E' => [
                'label' => ['en' => 'Enterprising (E)', 'ar' => 'الشخصية المبادرة (E)'],
                'description' => [
                    'ar' => "يرغب أصحاب هذه الشخصية بإقناع الآخرين وقيادتهم، وتدفعهم المكانة الاجتماعيّة اللائقة والكسب المالي.\nيمتلكون قدرات القيادة والخطابة العامة، ويرغبون بالمال والسياسة والتأثير في الناس.\nينظرون إلى ذواتهم كأصحاب شخصية مقنعة، تمتلك المهارات التجارية والتسويقية أكثر من المهارات العلمية.\nيراهم الآخرون أصحاب شخصية مليئة بالطاقة والحيوية، لذا من الأفضل أن يتجنّبوا العمل بالمجالات العلمية والفكرية المعقّدة.\nيتميز هؤلاء الأفراد بالصفات الآتية: الامتلاك والاستحواذ، حب المغامرة، الطموح، مهارات الإصغاء، التسلّط، النشاط، الانطلاق، الدافعية، التفاؤل، التفتيش عن المنفعة، الثقة بالنفس.\nيعملون في مهن تستلزم أنشطة البيع والإقناع والقيادة، لذا فهم ينجحون في مهن محدّدة كالإدارة، البيع، التسويق، الحقوق، الإشراف التربوي، وكالات السياحة والسفر.",
                    'en' => "People with this personality like persuading and leading others, and they are driven by a respectable social standing and financial gain.\nThey have leadership and public speaking abilities, and they are drawn to money, politics and influencing people.\nThey see themselves as persuasive people, with more business and marketing skills than scientific skills.\nOthers see them as full of energy and vitality, so they are better off avoiding complex scientific and intellectual fields.\nThey are characterized by the following traits: possessiveness, love of adventure, ambition, listening skills, dominance, energy, outgoingness, motivation, optimism, seeking benefit, and self-confidence.\nThey work in jobs that involve selling, persuading and leading, so they succeed in fields such as management, sales, marketing, law, educational supervision, and travel and tourism agencies.",
                ],
            ],
            'C' => [
                'label' => ['en' => 'Conventional (C)', 'ar' => 'الشخصية التقليدية (C)'],
                'description' => [
                    'ar' => "يرغب أصحاب هذه الشخصية باتباع ضوابط وتعليمات ثابتة، ويدفعهم حبّ امتلاك النفوذ والقوة الاجتماعيّة والسياسيّة.\nيمتلكون قدرات ذهنية ومكتبية، ويميلون للعمل في المكاتب والغرف، وتنظيم الأشياء.\nينظرون إلى ذواتهم كأصحاب شخصية منجزة، تمتلك مهارات إنجاز الأعمال والقدرة على التركيز والصبر، أكثر من المهارات الإبداعية الفنيّة.\nيراهم الآخرون أصحاب شخصية حذرة ومتشدّدة في اتباع القوانين، لذا من الأفضل أن يتجنّبوا العمل بالمجالات غير واضحة التوجيهات.\nيتميز هؤلاء الأفراد بالصفات الآتية: الضمير الحي، الحذر، الفعالية، الخضوع للضوابط، الطاعة، الترتيب، المثابرة، العملانية، التوفير، عدم الابتكار.\nيعملون في مهن تستلزم أنشطة التعامل مع الأرقام واستعمال الأدوات المكتبية والآلات وفق معايير محددة، لذا فهم ينجحون في مهن محدّدة كالمحاسبة، التحليل المالي، أمانة السرّ، الأعمال المصرفيّة، الإرسال الإذاعي.",
                    'en' => "People with this personality like following fixed rules and instructions, and they are driven by a love of having influence and social and political power.\nThey have mental and clerical abilities, and they prefer working in offices and rooms and organizing things.\nThey see themselves as achievers, with more skill at getting work done, concentrating and being patient than artistic, creative skills.\nOthers see them as cautious people who are strict about following rules, so they are better off avoiding fields without clear direction.\nThey are characterized by the following traits: conscientiousness, caution, efficiency, compliance with rules, obedience, orderliness, perseverance, practicality, thrift, and lack of innovation.\nThey work in jobs that involve dealing with numbers and using office tools and machines according to set standards, so they succeed in fields such as accounting, financial analysis, secretarial work, banking, and broadcasting.",
                ],
            ],
        ];
    }

    /**
     * The three parts in source order. Items are [Arabic, English]; within each part item n
     * belongs to type TYPES[(n - 1) % 6].
     */
    private static function parts(): array
    {
        return [
            // ---------------- Part 1: activities (questions 1-60) ----------------
            [
                'labels' => [
                    '1' => ['en' => 'Like', 'ar' => 'أحب'],
                    '2' => ['en' => 'Somewhat like', 'ar' => 'أحب إلى حد ما'],
                    '3' => ['en' => 'Dislike', 'ar' => 'لا أحب'],
                ],
                'items' => [
                    ['إصلاح أثاث المنزل', 'Repairing household furniture'],
                    ['دراسة النظريات العلمية', 'Studying scientific theories'],
                    ['العزف على آلة موسيقية', 'Playing a musical instrument'],
                    ['دراسة مقرر في العلاقات الإنسانية', 'Taking a course in human relations'],
                    ['القدرة على إقناع الآخرين', 'Being able to persuade others'],
                    ['العمل الدائم في مؤسسة كبرى', 'Working permanently in a large organization'],
                    ['دراسة مقرر في ميكانيكا السيارات', 'Taking a course in car mechanics'],
                    ['العمل في مختبر للعلوم', 'Working in a science laboratory'],
                    ['تصميم الأزياء', 'Fashion design'],
                    ['العمل مدرساً في فصول لمحو الأمية', 'Working as a teacher in adult literacy classes'],
                    ['المشاركة في حملة انتخابية', 'Taking part in an election campaign'],
                    ['دراسة مقرر في المحاسبة', 'Taking a course in accounting'],
                    ['إصلاح لعبة مكسورة', 'Fixing a broken toy'],
                    ['قراءة الكتب والمجلات العلمية', 'Reading scientific books and magazines'],
                    ['قراءة مقالات في الأدب', 'Reading articles on literature'],
                    ['قراءة الكتب والمجلات الاجتماعية', 'Reading books and magazines on social topics'],
                    ['تعلم أساليب النجاح في الحياة', 'Learning ways to succeed in life'],
                    ['تنظيم السجلات والملفات', 'Organizing records and files'],
                    ['دراسة مقرر في التوصيلات الكهربائية', 'Taking a course in electrical wiring'],
                    ['دراسة مقرر في الرياضيات', 'Taking a course in mathematics'],
                    ['رسم اللوحات الفنية', 'Painting pictures'],
                    ['مساعدة الآخرين في حلّ مشكلاتهم', 'Helping others solve their problems'],
                    ['أن أكون من رجال الأعمال', 'Being a businessperson'],
                    ['تحليل الأرقام والبيانات', 'Analyzing numbers and data'],
                    ['زراعة حديقة منزلية', 'Growing a home garden'],
                    ['العمل في مركز للبحوث العلمية', 'Working in a scientific research center'],
                    ['دراسة فن الديكور', 'Studying the art of interior decoration'],
                    ['العمل متطوعاً في جمعية خيرية', 'Volunteering at a charity'],
                    ['حضور المزادات التجارية', 'Attending commercial auctions'],
                    ['أعمال السكرتارية أو إدارة المكاتب', 'Secretarial work or office management'],
                    ['تشغيل بعض الآلات والأجهزة البسيطة', 'Operating some simple machines and devices'],
                    ['كتابة مقال عن الأسلحة البيولوجية', 'Writing an article about biological weapons'],
                    ['التمثيل المسرحي أو السينمائي', 'Acting in theater or film'],
                    ['دراسة مقرر في الخدمة الاجتماعية', 'Taking a course in social work'],
                    ['قيادة مجموعة من الأشخاص في عمل ما', 'Leading a group of people in a task'],
                    ['العمل وفق خطوات وطرق واضحة ومحددة', 'Working according to clear, defined steps and methods'],
                    ['فك أو تركيب ماكينة أو جهاز', 'Taking apart or assembling a machine or device'],
                    ['دراسة مقرر في الكيمياء', 'Taking a course in chemistry'],
                    ['تلحين الأغاني أو المقطوعات الموسيقية', 'Composing songs or pieces of music'],
                    ['العمل مشرفاً للأطفال المعاقين', 'Working as a supervisor for children with disabilities'],
                    ['قراءة أخبار الأسهم والأوراق المالية', 'Reading news about stocks and securities'],
                    ['العمل صرافاً في بنك', 'Working as a bank teller'],
                    ['العمل في مزرعة لتربية الحيوانات والطيور', 'Working on a farm that raises animals and poultry'],
                    ['إجراء بحث عن النباتات الطبية', 'Doing research on medicinal plants'],
                    ['تأليف القصص أو الروايات', 'Writing stories or novels'],
                    ['المشاركة في تنظيم الأنشطة الاجتماعية', 'Helping organize social activities'],
                    ['لقاء القادة والشخصيات الهامة', 'Meeting leaders and important figures'],
                    ['العمل بشكل منظم ومرتب ومنضبط', 'Working in an organized, orderly and disciplined way'],
                    ['صنع أشياء من الخشب', 'Making things out of wood'],
                    ['دراسة مقرر في الفيزياء', 'Taking a course in physics'],
                    ['كتابة الشعر', 'Writing poetry'],
                    ['كتابة مقال عن الأطفال المشردين', 'Writing an article about homeless children'],
                    ['المشاركة في محل أو عمل تجاري', 'Taking part in a shop or a commercial business'],
                    ['مراجعة أي عمل بدقة لاكتشاف الأخطاء', 'Reviewing any work carefully to find mistakes'],
                    ['دراسة مقرر في تقنيات الحاسوب', 'Taking a course in computer technology'],
                    ['العمل في مرصد فلكي', 'Working in an astronomical observatory'],
                    ['العمل في مركز للثقافة والفنون', 'Working in a culture and arts center'],
                    ['تعليم مقرر في السلوك الاجتماعي', 'Teaching a course on social behavior'],
                    ['دراسة مقرر عن ترويج البضائع والسلع الاستهلاكية', 'Taking a course on promoting consumer goods and products'],
                    ['تنظيم الوقت واحترامه', 'Organizing time and respecting it'],
                ],
            ],

            // ---------------- Part 2: skills (questions 61-120) ----------------
            [
                'labels' => [
                    '1' => ['en' => 'High', 'ar' => 'عالية'],
                    '2' => ['en' => 'Moderate', 'ar' => 'متوسطة'],
                    '3' => ['en' => 'Not good at it', 'ar' => 'لا أجيد'],
                ],
                'items' => [
                    ['إصلاح بعض الأجهزة البسيطة', 'Repairing some simple devices'],
                    ['إجراء التجارب المعملية', 'Doing laboratory experiments'],
                    ['العزف على آلة موسيقية', 'Playing a musical instrument'],
                    ['التحدث مع الناس بسهولة', 'Talking with people easily'],
                    ['إقناع الآخرين والتأثير فيهم', 'Persuading and influencing others'],
                    ['تنظيم العمل', 'Organizing work'],
                    ['استخدام بعض الأدوات الميكانيكية', 'Using some mechanical tools'],
                    ['حل مسائل الرياضيات', 'Solving math problems'],
                    ['الغناء الفردي أو الجماعي', 'Singing solo or in a group'],
                    ['شرح الأشياء للآخرين', 'Explaining things to others'],
                    ['التحدث إلى جمع من الناس بطلاقة', 'Speaking fluently to a group of people'],
                    ['إنجاز العمل بدقة وسرعة', 'Getting work done accurately and quickly'],
                    ['طلاء بعض الجدران', 'Painting some walls'],
                    ['تفسير بعض الظواهر الطبيعية', 'Explaining some natural phenomena'],
                    ['رسم المناظر الطبيعية', 'Drawing landscapes'],
                    ['البحث عن أسباب المشكلات الاجتماعية', 'Looking into the causes of social problems'],
                    ['التخطيط لعمل من الأعمال', 'Planning a piece of work'],
                    ['أداء الأعمال المكتبية والروتينية', 'Doing office and routine work'],
                    ['تغيير زيت أو إطارات السيارات', 'Changing car oil or tires'],
                    ['وصف وظائف أعضاء وأجهزة الجسم', 'Describing the functions of the body\'s organs and systems'],
                    ['تأليف مقطوعات موسيقية', 'Composing pieces of music'],
                    ['إقامة العلاقات الاجتماعية', 'Building social relationships'],
                    ['ترويج ما أريد من أفكار', 'Promoting the ideas I want'],
                    ['تخطيط العمل وحفظه في ملفات', 'Planning work and keeping it in files'],
                    ['استخدام بعض الرسومات الهندسية', 'Using some engineering drawings'],
                    ['كتابة تقرير علمي', 'Writing a scientific report'],
                    ['تصميم الأزياء أو الأثاث', 'Designing clothes or furniture'],
                    ['مساعدة من لديهم متاعب أو مشكلات', 'Helping people who have troubles or problems'],
                    ['إدارة حملة لبيع السلع والبضائع', 'Running a campaign to sell goods and merchandise'],
                    ['ترتيب أوراق العمل وحفظها في ملفات', 'Arranging work papers and keeping them in files'],
                    ['عمل بعض الرسومات الهندسية', 'Making some engineering drawings'],
                    ['استخدام الحاسوب في دراسة المشكلات العلمية', 'Using a computer to study scientific problems'],
                    ['التمثيل المسرحي أو السينمائي', 'Acting in theater or film'],
                    ['تعليم الأطفال أو الكبار بسهولة', 'Teaching children or adults easily'],
                    ['تنظيم وإدارة أعمال الآخرين', 'Organizing and managing other people\'s work'],
                    ['الالتزام بالخطوات والنظام المحدد للعمل', 'Following the set steps and procedures for work'],
                    ['تلميع أثاث المنزل', 'Polishing household furniture'],
                    ['فهم المعادلات الجبرية', 'Understanding algebraic equations'],
                    ['تأليف القصص أو الروايات', 'Writing stories or novels'],
                    ['العمل في جماعة', 'Working in a group'],
                    ['قيادة مجموعة لتنفيذ مشروع تجاري', 'Leading a group to carry out a commercial project'],
                    ['إدخال البيانات في الحاسوب', 'Entering data into a computer'],
                    ['استخدام بعض أدوات الزراعة', 'Using some farming tools'],
                    ['تفسير المعادلات الكيميائية البسيطة', 'Interpreting simple chemical equations'],
                    ['تأليف الشعر', 'Writing poetry'],
                    ['المساعدة في حل المشكلات الاجتماعية', 'Helping solve social problems'],
                    ['المهارة في البيع والشراء', 'Skill at buying and selling'],
                    ['كتابة خطابات العمل', 'Writing business letters'],
                    ['عمل بعض التوصيلات الكهربائية البسيطة', 'Doing some simple electrical wiring'],
                    ['فهم العديد من النظريات العلمية', 'Understanding many scientific theories'],
                    ['كتابة المقالات الأدبية', 'Writing literary articles'],
                    ['الإشراف على مشروع خيري', 'Supervising a charity project'],
                    ['الجرأة في تنفيذ ما أريد', 'Boldness in carrying out what I want'],
                    ['الانضباط في العمل', 'Discipline at work'],
                    ['تركيب صنبور للمياه', 'Installing a water faucet'],
                    ['تحليل البيانات والمعلومات', 'Analyzing data and information'],
                    ['تصميم الديكور', 'Interior decoration design'],
                    ['الاعتناء بالمرضى ورعايتهم', 'Looking after and caring for the sick'],
                    ['تقبل المخاطرة والمغامرة', 'Accepting risk and adventure'],
                    ['دقة إعداد الكشوف والبيانات', 'Preparing lists and records accurately'],
                ],
            ],

            // ---------------- Part 3: occupations (questions 121-180) ----------------
            [
                'labels' => [
                    '1' => ['en' => 'Agree', 'ar' => 'موافق'],
                    '2' => ['en' => 'Somewhat agree', 'ar' => 'موافق إلى حد ما'],
                    '3' => ['en' => 'Disagree', 'ar' => 'غير موافق'],
                ],
                'items' => [
                    ['فني إلكترونيات', 'Electronics technician'],
                    ['عالم نبات', 'Botanist'],
                    ['مؤلف مسرحي', 'Playwright'],
                    ['معلم', 'Teacher'],
                    ['بائع', 'Salesperson'],
                    ['محاسب', 'Accountant'],
                    ['مزارع', 'Farmer'],
                    ['فني تحاليل طبية', 'Medical laboratory technician'],
                    ['مخرج سينمائي', 'Film director'],
                    ['موظف علاقات عامة', 'Public relations officer'],
                    ['مدير مبيعات', 'Sales manager'],
                    ['موظف تأمينات', 'Insurance clerk'],
                    ['مهندس ميكانيكي', 'Mechanical engineer'],
                    ['كيميائي', 'Chemist'],
                    ['صحفي', 'Journalist'],
                    ['مدير نادٍ اجتماعي', 'Social club manager'],
                    ['مندوب تأمينات', 'Insurance agent'],
                    ['سكرتير', 'Secretary'],
                    ['كهربائي', 'Electrician'],
                    ['باحث علوم اجتماعية', 'Social science researcher'],
                    ['مؤلف موسيقي', 'Composer'],
                    ['مدرب رياضي', 'Sports coach'],
                    ['محامٍ', 'Lawyer'],
                    ['خبير ضرائب', 'Tax expert'],
                    ['نجار', 'Carpenter'],
                    ['فلكي', 'Astronomer'],
                    ['مؤلف قصص', 'Story writer'],
                    ['أخصائي اجتماعي', 'Social worker'],
                    ['مدير فندق', 'Hotel manager'],
                    ['صرّاف', 'Cashier'],
                    ['مهندس طيران', 'Aeronautical engineer'],
                    ['جيولوجي', 'Geologist'],
                    ['ممثل', 'Actor'],
                    ['ممرض', 'Nurse'],
                    ['مشرف مشروع تجاري', 'Commercial project supervisor'],
                    ['كاتب في مؤسسة حكومية', 'Clerk in a government agency'],
                    ['مشرف بناء', 'Construction supervisor'],
                    ['متخصص في الأحياء', 'Biology specialist'],
                    ['مصمم ديكور', 'Interior designer'],
                    ['مشرف أنشطة اجتماعية', 'Social activities supervisor'],
                    ['مدير عمل خاص', 'Manager of a private business'],
                    ['مدير مكتب', 'Office manager'],
                    ['طيار', 'Pilot'],
                    ['باحث في الفيزياء', 'Physics researcher'],
                    ['مذيع تليفزيون', 'TV presenter'],
                    ['أخصائي نفسي', 'Psychologist'],
                    ['تاجر', 'Merchant'],
                    ['موظف استقبال', 'Receptionist'],
                    ['ضابط شرطة', 'Police officer'],
                    ['صيدلي', 'Pharmacist'],
                    ['معلم فنون', 'Art teacher'],
                    ['أخصائي علاج طبيعي', 'Physical therapist'],
                    ['رئيس بنك', 'Bank president'],
                    ['مدرس إدارة أعمال', 'Business administration teacher'],
                    ['ميكانيكي سيارات', 'Car mechanic'],
                    ['مدرس رياضيات', 'Mathematics teacher'],
                    ['رسام', 'Painter (artist)'],
                    ['مستشار لحلّ المشكلات الاجتماعية', 'Counselor for social problems'],
                    ['وكيل سياحي', 'Travel agent'],
                    ['موظف استعلامات', 'Information desk clerk'],
                ],
            ],
        ];
    }
}
