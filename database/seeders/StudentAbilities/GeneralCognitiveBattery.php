<?php

namespace Database\Seeders\StudentAbilities;

/**
 * Standardized General Cognitive Ability Battery / بطارية اختبار القدرات المعرفية العامة.
 *
 * Source: "Standardized General Cognitive Ability Batter.pdf" + "بطارية اختبار القدرات المعرفية العامة.pdf".
 * Options and keys are kept in source order; StudentAbilitiesSeeder shuffles them.
 *
 * Source corrections:
 *  - Item 10: the English key table says "D" but its own rationale (and the Arabic key) give C (25/4).
 *  - Item 26: options B ("all four shapes remain visible") and C (the four shapes listed) said the
 *    same thing. C is kept as the keyed answer; B is replaced with a wrong distractor.
 */
class GeneralCognitiveBattery
{
    public static function definition(): array
    {
        return [
            'title' => [
                'en' => 'Standardized General Cognitive Ability Battery',
                'ar' => 'بطارية اختبار القدرات المعرفية العامة',
            ],
            'description' => [
                'en' => 'A 30-item battery measuring general cognitive ability across four domains: fluid and abstract matrix reasoning, numerical and quantitative reasoning, verbal-deductive logic, and spatial and structural reasoning.',
                'ar' => 'بطارية من 30 سؤالًا تقيس القدرات المعرفية العامة عبر أربعة محاور: الاستدلال التجريدي والمصفوفات، والتفكير العددي والكمي، والاستدلال المنطقي واللفظي، والتفكير المكاني والفضائي.',
            ],
            'instructions' => [
                'en' => 'Each question has four options and only one correct answer. Read each question carefully and choose the best answer. Each correct answer earns one point; there is no penalty for wrong answers.',
                'ar' => 'لكل سؤال أربعة بدائل وإجابة صحيحة واحدة فقط. اقرأ كل سؤال بعناية واختر الإجابة الأنسب. تُحتسب درجة واحدة لكل إجابة صحيحة، ولا يُخصم شيء على الإجابة الخاطئة.',
            ],
            'chart' => 'radar',
            'categories' => [
                'fluid' => ['en' => 'Fluid & Abstract Matrix Reasoning', 'ar' => 'الاستدلال التجريدي والمصفوفات'],
                'numerical' => ['en' => 'Numerical & Quantitative Reasoning', 'ar' => 'التفكير العددي والكمي'],
                'verbal_logic' => ['en' => 'Verbal-Deductive Logic', 'ar' => 'الاستدلال المنطقي واللفظي'],
                'spatial' => ['en' => 'Spatial & Structural Reasoning', 'ar' => 'التفكير المكاني والفضائي'],
            ],
            'questions' => [
                // ---------------- Section 1: Fluid & Abstract Matrix Reasoning (1-8) ----------------
                [
                    'cat' => 'fluid',
                    'en' => [
                        "Examine the 3×3 matrix transformation rule:\n• Row 1: Solid square → Square with a horizontal line → Square with a horizontal & vertical cross (+)\n• Row 2: Solid circle → Circle with a horizontal line → Circle with a horizontal & vertical cross (+)\n• Row 3: Solid triangle → Triangle with a horizontal line → [ ? ]\nWhich option completes Row 3?",
                        ['Solid triangle with a diagonal slash', 'Triangle containing both a horizontal and vertical cross (+)', 'Inverted solid triangle', 'Concentric triangles with a vertical line'],
                        2,
                    ],
                    'ar' => [
                        "تأمل قاعدة التغير في المصفوفة البصرية التالية (3×3):\n• الصف الأول: مربع مصمت ← مربع به خط أفقي ← مربع به تقاطع أفقي ورأسي (+)\n• الصف الثاني: دائرة مصمتة ← دائرة بها خط أفقي ← دائرة بها تقاطع أفقي ورأسي (+)\n• الصف الثالث: مثلث مصمت ← مثلث به خط أفقي ← [ ؟ ]\nأي الأشكال التالية يكمل الخانة المفقودة في الصف الثالث؟",
                        ['مثلث مصمت به خط مائل', 'مثلث به تقاطع أفقي ورأسي (+)', 'مثلث مقلوب مصمت', 'مثلثان متداخلان بهما خط رأسي'],
                        2,
                    ],
                ],
                [
                    'cat' => 'fluid',
                    'en' => [
                        "Consider a sequence of rotating and shading figures:\n• Step 1: Arrow pointing UP (0°), WHITE\n• Step 2: Arrow pointing RIGHT (90° clockwise), BLACK\n• Step 3: Arrow pointing DOWN (180° clockwise), STRIPED\n• Step 4: Arrow pointing LEFT (270° clockwise), WHITE\n• Step 5: [ ? ]\nWhat is the state of the arrow at Step 5?",
                        ['Pointing LEFT and BLACK', 'Pointing UP (360°/0°) and BLACK', 'Pointing DOWN and STRIPED', 'Pointing RIGHT and WHITE'],
                        2,
                    ],
                    'ar' => [
                        "تتبع حركة دوران السهم وتغير تظليله في التتابع التالي:\n• الخطوة 1: سهم يتجه إلى الأعلى (0°)، ولونه أبيض\n• الخطوة 2: سهم يتجه إلى اليمين (90° مع عقارب الساعة)، ولونه أسود\n• الخطوة 3: سهم يتجه إلى الأسفل (180° مع عقارب الساعة)، ولونه مخطط\n• الخطوة 4: سهم يتجه إلى اليسار (270° مع عقارب الساعة)، ولونه أبيض\n• الخطوة 5: [ ؟ ]\nما حالة السهم الصحيحة في الخطوة 5؟",
                        ['يتجه لليسار ولونه أسود', 'يتجه للأعلى (360°/0°) ولونه أسود', 'يتجه للأسفل ولونه مخطط', 'يتجه لليمين ولونه أبيض'],
                        2,
                    ],
                ],
                [
                    'cat' => 'fluid',
                    'en' => [
                        "• Rule A: All figures in Set 1 must have an even number of sides.\n• Rule B: All figures in Set 1 must contain exactly one shaded circle.\n• Figure X: Hexagon containing one shaded circle.\n• Figure Y: Pentagon containing one shaded circle.\n• Figure Z: Square containing two shaded circles.\nWhich figure(s) strictly belong to Set 1?",
                        ['Figure X only', 'Figure Y only', 'Figures X and Z', 'Figures X, Y, and Z'],
                        1,
                    ],
                    'ar' => [
                        "• القاعدة (أ): جميع أشكال المجموعة (1) يجب أن تحتوي على عدد أضلاع زوجي.\n• القاعدة (ب): جميع أشكال المجموعة (1) يجب أن تحتوي على دائرة مظللة واحدة فقط.\n• الشكل (س): مسدس يحتوي على دائرة مظللة واحدة.\n• الشكل (ص): خماسي يحتوي على دائرة مظللة واحدة.\n• الشكل (ع): مربع يحتوي على دائرتين مظللتين.\nأي الأشكال ينتمي بشكل صارم إلى المجموعة (1)؟",
                        ['الشكل (س) فقط', 'الشكل (ص) فقط', 'الشكلان (س) و(ع)', 'الأشكال (س) و(ص) و(ع)'],
                        1,
                    ],
                ],
                [
                    'cat' => 'fluid',
                    'en' => [
                        "A 3×3 number matrix follows this rule in every row:\nColumn 3 = Column 1 + 2 × (Column 2)\n• Row 1: [2, 4, 10]\n• Row 2: [3, 5, 13]\n• Row 3: [4, 6, ?]\nWhat is the missing value?",
                        ['14', '16', '18', '20'],
                        2,
                    ],
                    'ar' => [
                        "مصفوفة أرقام (3×3) تتبع عناصرها القاعدة التالية في كل صف:\nالعمود الثالث = العمود الأول + 2 × (العمود الثاني)\n• الصف الأول: [2، 4، 10]\n• الصف الثاني: [3، 5، 13]\n• الصف الثالث: [4، 6، ؟]\nما القيمة المفقودة في الخانة الأخيرة؟",
                        ['14', '16', '18', '20'],
                        2,
                    ],
                ],
                [
                    'cat' => 'fluid',
                    'en' => [
                        "Examine the shape transformation rule: a circle becomes a square, the number of internal lines doubles, and the fill colour flips (Black → White).\nIf the input is a Black Circle with 2 internal lines, what is the output?",
                        ['White Square with 2 internal lines', 'White Square with 4 internal lines', 'Black Square with 4 internal lines', 'White Circle with 4 internal lines'],
                        2,
                    ],
                    'ar' => [
                        "تتبع قاعدة التحول التالية: الدائرة تتحول إلى مربع، وعدد الخطوط الداخلية يتضاعف، ولون التعبئة ينعكس (من أسود إلى أبيض).\nإذا كان الشكل المُدخل دائرة سوداء تحتوي على خطين داخليين، فما الشكل الناتج؟",
                        ['مربع أبيض به خطان داخليان', 'مربع أبيض به 4 خطوط داخلية', 'مربع أسود به 4 خطوط داخلية', 'دائرة بيضاء بها 4 خطوط داخلية'],
                        2,
                    ],
                ],
                [
                    'cat' => 'fluid',
                    'en' => [
                        'Select the figure that does NOT belong with the others:',
                        ['Hexagon with 3 diagonal lines', 'Octagon with 4 diagonal lines', 'Square with 2 diagonal lines', 'Pentagon with 3 diagonal lines'],
                        4,
                    ],
                    'ar' => [
                        'حدد الشكل الذي لا ينتمي لنفس المجموعة والخصائص الهندسية للأشكال الأخرى:',
                        ['مسدس يحتوي على 3 أقطار', 'ثماني أضلاع يحتوي على 4 أقطار', 'مربع يحتوي على قطرين', 'خماسي أضلاع يحتوي على 3 أقطار'],
                        4,
                    ],
                ],
                [
                    'cat' => 'fluid',
                    'en' => [
                        "In a matrix of 9 symbols:\n• Row 1 has 1 star, 2 stars, 3 stars.\n• Row 2 has 2 dots, 4 dots, 6 dots.\n• Row 3 has 3 triangles, 6 triangles, [ ? ].\nWhat completes Row 3?",
                        ['7 triangles', '8 triangles', '9 triangles', '12 triangles'],
                        3,
                    ],
                    'ar' => [
                        "في مصفوفة رموز تتبع التزايد العددي:\n• الصف الأول به: نجمة واحدة، نجمتان، 3 نجوم.\n• الصف الثاني به: نقطتان، 4 نقاط، 6 نقاط.\n• الصف الثالث به: 3 مثلثات، 6 مثلثات، [ ؟ ].\nما المكمل الصحيح للصف الثالث؟",
                        ['7 مثلثات', '8 مثلثات', '9 مثلثات', '12 مثلثًا'],
                        3,
                    ],
                ],
                [
                    'cat' => 'fluid',
                    'en' => [
                        "A flat line segment of equal parts bends 90° at each step:\n• Step 1: Straight line (0 bends)\n• Step 2: L-shape (1 right-angle bend)\n• Step 3: U-shape (2 right-angle bends)\n• Step 4: Square frame missing one side (3 bends)\n• Step 5: [ ? ]\nWhat shape is formed at Step 5 (4 right-angle bends)?",
                        ['Closed square', 'Rectangle missing two sides', 'S-shape', 'Triangle'],
                        1,
                    ],
                    'ar' => [
                        "قطعة مستقيمة مسطحة متساوية الأجزاء تُطوى بزاوية قائمة (90°) عند كل خطوة:\n• الخطوة 1: قطعة مستقيمة (0 ثنيات)\n• الخطوة 2: حرف L (ثنية قائمة واحدة)\n• الخطوة 3: حرف U (ثنيتان قائمتان)\n• الخطوة 4: إطار مربع ينقصه ضلع واحد (3 ثنيات)\n• الخطوة 5: [ ؟ ]\nما الشكل الناتج في الخطوة 5 بعد حدوث 4 ثنيات قائمة؟",
                        ['مربع مغلق بالكامل', 'مستطيل ينقصه ضلعان', 'حرف S مائل', 'مثلث متساوي الأضلاع'],
                        1,
                    ],
                ],

                // ---------------- Section 2: Numerical & Quantitative Reasoning (9-16) ----------------
                [
                    'cat' => 'numerical',
                    'en' => ["Examine the numerical sequence:\n3, 7, 16, 35, 74, [ ? ]\nWhat number comes next?", ['148', '153', '149', '160'], 2],
                    'ar' => ["تأمل السلسلة الرقمية التالية:\n3، 7، 16، 35، 74، [ ؟ ]\nما الرقم الذي يكمل السلسلة منطقيًا؟", ['148', '153', '149', '160'], 2],
                ],
                [
                    'cat' => 'numerical',
                    'en' => ["If 2A + B = 14 and A − 2B = −3, what is the value of A² / B?", ['3', '5', '25/4', '25/3'], 3],
                    'ar' => ["إذا كان 2A + B = 14 و A − 2B = −3، فما قيمة النسبة A² / B؟", ['3', '5', '25/4', '25/3'], 3],
                ],
                [
                    'cat' => 'numerical',
                    'en' => ['Three research assistants process 120 surveys in 4 hours. How many hours would it take 5 research assistants, working at the same constant rate, to process 300 surveys?', ['4 hours', '5 hours', '6 hours', '3.5 hours'], 3],
                    'ar' => ['يستطيع 3 باحثين فحص 120 استبانة خلال 4 ساعات. كم ساعة يحتاجها 5 باحثين يعملون بنفس المعدل الثابت لفحص 300 استبانة؟', ['4 ساعات', '5 ساعات', '6 ساعات', '3.5 ساعة'], 3],
                ],
                [
                    'cat' => 'numerical',
                    'en' => ["Find the missing term in the double-rule series:\n2, 5, 6, 10, 18, 15, 54, [ ? ]", ['20', '25', '162', '30'], 1],
                    'ar' => ["أوجد الحد المفقود في المتتابعة المزدوجة التالية:\n2، 5، 6، 10، 18، 15، 54، [ ؟ ]", ['20', '25', '162', '30'], 1],
                ],
                [
                    'cat' => 'numerical',
                    'en' => ['A dataset has a mean of 50 and a standard deviation of 10. If every value is multiplied by 2 and then increased by 5, what are the new mean and standard deviation?', ['Mean = 105, SD = 20', 'Mean = 105, SD = 25', 'Mean = 100, SD = 20', 'Mean = 110, SD = 20'], 1],
                    'ar' => ['مجموعة بيانات متوسطها الحسابي 50 وانحرافها المعياري 10. إذا ضُربت كل قيمة في 2 ثم أُضيف إليها 5، فما المتوسط والانحراف المعياري الجديدان على الترتيب؟', ['المتوسط = 105، والانحراف المعياري = 20', 'المتوسط = 105، والانحراف المعياري = 25', 'المتوسط = 100، والانحراف المعياري = 20', 'المتوسط = 110، والانحراف المعياري = 20'], 1],
                ],
                [
                    'cat' => 'numerical',
                    'en' => ['If X is inversely proportional to Y², and X = 8 when Y = 3, what is the value of X when Y = 6?', ['2', '4', '1', '16'], 1],
                    'ar' => ['إذا كانت X تتناسب عكسيًا مع مربع Y (أي Y²)، وكانت X = 8 عندما Y = 3، فما قيمة X عندما Y = 6؟', ['2', '4', '1', '16'], 1],
                ],
                [
                    'cat' => 'numerical',
                    'en' => ['A machine fills 80 bottles in 2 minutes. Another machine fills 120 bottles in 4 minutes. Working together, how many minutes will it take both machines to fill 700 bottles?', ['8 minutes', '10 minutes', '12 minutes', '14 minutes'], 2],
                    'ar' => ['آلة تعبئ 80 زجاجة في دقيقتين، وآلة أخرى تعبئ 120 زجاجة في 4 دقائق. إذا عملت الآلتان معًا، فكم دقيقة تلزمهما لتعبئة 700 زجاجة؟', ['8 دقائق', '10 دقائق', '12 دقيقة', '14 دقيقة'], 2],
                ],
                [
                    'cat' => 'numerical',
                    'en' => ["What is the next number in the sequence:\n1, 4, 27, 256, [ ? ]", ['625', '1024', '3125', '4096'], 3],
                    'ar' => ["ما الحد التالي في السلسلة الأسية التالية:\n1، 4، 27، 256، [ ؟ ]", ['625', '1024', '3125', '4096'], 3],
                ],

                // ---------------- Section 3: Verbal-Deductive Logic (17-23) ----------------
                [
                    'cat' => 'verbal_logic',
                    'en' => [
                        "• Premise 1: All quantitative researchers are proficient in statistical modeling.\n• Premise 2: Some statistical modeling experts are data scientists.\n• Premise 3: No qualitative archivist is a data scientist.\nWhich conclusion MUST be logically true?",
                        ['All data scientists are quantitative researchers', 'Some quantitative researchers are qualitative archivists', 'Some statistical modeling experts are not qualitative archivists', 'No quantitative researcher is a data scientist'],
                        3,
                    ],
                    'ar' => [
                        "• المقدمة 1: جميع الباحثين الكميين يمتلكون مهارة عالية في النمذجة الإحصائية.\n• المقدمة 2: بعض خبراء النمذجة الإحصائية هم علماء بيانات.\n• المقدمة 3: لا يوجد أي موثّق كيفي يعمل عالمَ بيانات.\nأي النتائج التالية يجب أن تكون صحيحة منطقيًا وبشكل قاطع؟",
                        ['جميع علماء البيانات هم باحثون كميون', 'بعض الباحثين الكميين يعملون موثقين كيفيين', 'بعض خبراء النمذجة الإحصائية ليسوا موثقين كيفيين', 'لا يوجد أي باحث كمي يعمل عالمَ بيانات'],
                        3,
                    ],
                ],
                [
                    'cat' => 'verbal_logic',
                    'en' => [
                        "\"If a student achieves a top percentile score on the spatial test, they are recommended for Advanced Robotics. A student is recommended for Advanced Robotics only if they have completed Linear Algebra.\"\nFact: Student Omar was NOT recommended for Advanced Robotics.\nWhat can be conclusively inferred?",
                        ['Omar did not complete Linear Algebra', 'Omar did not achieve a top spatial score, OR he did not complete Linear Algebra', 'Omar failed all psychometric tests', 'Omar completed Linear Algebra but failed spatial aptitude'],
                        2,
                    ],
                    'ar' => [
                        "\"إذا حقق الطالب درجة ممتازة في اختبار القدرات المكانية، يُرشَّح لمسار الروبوتات المتقدمة. ولا يُرشَّح الطالب لمسار الروبوتات المتقدمة إلا إذا كان قد أتم مساق الجبر الخطي.\"\nحقيقة مؤكدة: الطالب عمر لم يُرشَّح لمسار الروبوتات المتقدمة.\nما الذي يمكن استنتاجه منطقيًا بشكل قاطع عن عمر؟",
                        ['عمر لم يُتم مساق الجبر الخطي', 'عمر لم يحقق درجة ممتازة في الاختبار المكاني، أو أنه لم يُتم مساق الجبر الخطي', 'عمر رسب في جميع الاختبارات السيكومترية', 'عمر أتم الجبر الخطي ولكنه رسب في الاختبار المكاني'],
                        2,
                    ],
                ],
                [
                    'cat' => 'verbal_logic',
                    'en' => ['ALGORITHM : EFFICIENCY :: EMPIRICAL DATA : [ ? ]', ['VALIDITY', 'HYPOTHESIS', 'ASSUMPTION', 'HEURISTIC'], 1],
                    'ar' => ['حدد الكلمة المكملة للتناظر التالي:' . "\nالخوارزمية : الكفاءة :: البيانات التجريبية : [ ؟ ]", ['الصلاحية (Validity)', 'الفرضية', 'الافتراض', 'الحدس النظري'], 1],
                ],
                [
                    'cat' => 'verbal_logic',
                    'en' => [
                        "• Premise 1: If it rains, the ground gets wet.\n• Premise 2: If the ground gets wet, the outdoor sports event is canceled.\n• Fact: The outdoor sports event was NOT canceled.\nWhich statement MUST be true?",
                        ['It rained heavily', 'The ground got wet', 'It did not rain', 'The ground was covered with a tarp'],
                        3,
                    ],
                    'ar' => [
                        "• المقدمة 1: إذا هطل المطر، تصبح الأرض مبتلة.\n• المقدمة 2: إذا أصبحت الأرض مبتلة، تُلغى الفعالية الرياضية الخارجية.\n• حقيقة: الفعالية الرياضية الخارجية لم تُلغَ.\nأي العبارات التالية صحيحة بالضرورة؟",
                        ['المطر هطل بغزارة', 'الأرض أصبحت مبتلة', 'المطر لم يهطل', 'تمت تغطية الملعب بعازل'],
                        3,
                    ],
                ],
                [
                    'cat' => 'verbal_logic',
                    'en' => [
                        'All items in Category Alpha are marked with Code 9. No items marked with Code 9 are fragile. Some items marked with Code 9 are made of glass. Which statement logically follows?',
                        ['All items made of glass are fragile', 'Some items made of glass are not fragile', 'No Category Alpha item is made of glass', 'All Category Alpha items are fragile'],
                        2,
                    ],
                    'ar' => [
                        'جميع عناصر الفئة (ألف) مسجلة تحت الرمز (9). ولا يوجد أي عنصر مسجل تحت الرمز (9) قابل للكسر. وبعض العناصر المسجلة تحت الرمز (9) مصنوعة من الزجاج. أي الاستنتاجات التالية منطقي؟',
                        ['جميع المواد المصنوعة من الزجاج قابلة للكسر', 'بعض المواد المصنوعة من الزجاج غير قابلة للكسر', 'لا يوجد أي عنصر من الفئة (ألف) مصنوع من الزجاج', 'جميع عناصر الفئة (ألف) قابلة للكسر'],
                        2,
                    ],
                ],
                [
                    'cat' => 'verbal_logic',
                    'en' => ['HYPOTHESIS : EXPERIMENT :: THEORY : [ ? ]', ['VERIFICATION', 'SPECULATION', 'OBSERVATION', 'DEDUCTION'], 1],
                    'ar' => ['حدد الكلمة المكملة للتناظر المنطقي التالي:' . "\nالفرضية : التجربة :: النظرية : [ ؟ ]", ['التحقق (Verification)', 'التخمين', 'الملاحظة', 'الاستنباط'], 1],
                ],
                [
                    'cat' => 'verbal_logic',
                    'en' => ['Four students (A, B, C, D) sat for an exam. A scored higher than B. C scored higher than D but lower than B. Who achieved the lowest score?', ['Student A', 'Student B', 'Student C', 'Student D'], 4],
                    'ar' => ['جلس أربعة طلاب (أ، ب، ج، د) لاختبار. حصل الطالب (أ) على درجة أعلى من (ب)، وحصل (ج) على درجة أعلى من (د) ولكنها أقل من (ب). من الطالب الذي حصل على أدنى درجة؟', ['الطالب (أ)', 'الطالب (ب)', 'الطالب (ج)', 'الطالب (د)'], 4],
                ],

                // ---------------- Section 4: Spatial & Structural Reasoning (24-30) ----------------
                [
                    'cat' => 'spatial',
                    'en' => ['A 2D net of a cube has 6 faces: Top = Star, Center = Circle, Right = Square, Left = Triangle, Bottom = Cross, Back = Diamond. If the Circle is at the front and the Cross is at the bottom, which face is directly opposite the Circle?', ['Star', 'Diamond', 'Square', 'Triangle'], 2],
                    'ar' => ['شبكة مسطحة لمكعب تحتوي على 6 أوجه: الأعلى = نجمة، المركز = دائرة، اليمين = مربع، اليسار = مثلث، الأسفل = علامة زائد (+)، الخلف = معين. إذا طُويت الشبكة لتصبح مكعبًا وكانت الدائرة في الوجه الأمامي وعلامة الزائد في الوجه السفلي، فما الوجه المقابل مباشرة لوجه الدائرة؟', ['النجمة', 'المعين', 'المربع', 'المثلث'], 2],
                ],
                [
                    'cat' => 'spatial',
                    'en' => ['A 3D asymmetric structure is rotated 90° clockwise about its vertical axis, then rotated 180° about its horizontal axis. If an extended pin was located at the Top-Front-Right corner, where is it after both rotations?', ['Bottom-Back-Left', 'Bottom-Front-Left', 'Top-Back-Right', 'Bottom-Back-Right'], 1],
                    'ar' => ['مجسم ثلاثي الأبعاد غير متماثل تم تدويره 90° مع عقارب الساعة حول محوره الرأسي، ثم قُلب 180° حول محوره الأفقي. إذا كان هناك دبوس مثبت في الزاوية العليا-الأمامية-اليمنى، فأين يصبح موقعه الجديد؟', ['السفلى-الخلفية-اليسرى', 'السفلى-الأمامية-اليسرى', 'العليا-الخلفية-اليمنى', 'السفلى-الخلفية-اليمنى'], 1],
                ],
                [
                    'cat' => 'spatial',
                    'en' => [
                        "• Layer 1: Transparent grid containing a Black Triangle and a White Circle.\n• Layer 2: Identical grid containing a Black Circle and a White Triangle in the exact same coordinates.\n• Rule: Identical shapes of opposite colours overlap and cancel out (become blank). Non-identical shapes remain visible.\nWhat is visible when Layer 1 is superimposed onto Layer 2?",
                        ['Nothing (completely blank)', 'Only the two circles remain visible', 'A Black Triangle, White Circle, Black Circle, and White Triangle', 'The grid becomes solid black'],
                        3,
                    ],
                    'ar' => [
                        "• الطبقة 1: شبكة شفافة تحتوي على مثلث أسود ودائرة بيضاء.\n• الطبقة 2: شبكة مطابقة تحتوي على دائرة سوداء ومثلث أبيض في نفس الإحداثيات تمامًا.\n• القاعدة: عندما تتطابق أشكال متماثلة مختلفة الألوان في نفس الموقع، يلغي بعضها بعضًا وتصبح فارغة، بينما الأشكال المختلفة لا تُلغى.\nما الذي يظهر عند وضع الطبقة 1 فوق الطبقة 2 تمامًا؟",
                        ['لا شيء (شبكة فارغة بالكامل)', 'تظهر الدائرتان فقط', 'مثلث أسود، ودائرة بيضاء، ودائرة سوداء، ومثلث أبيض', 'تتحول الشبكة بالكامل إلى اللون الأسود'],
                        3,
                    ],
                ],
                [
                    'cat' => 'spatial',
                    'en' => ['How many unit cubes (1×1×1) are required to build a solid 4×4×4 cube with a hollow 1×1 tunnel passing completely through the center from top to bottom?', ['64', '60', '52', '48'], 2],
                    'ar' => ['كم عدد المكعبات الصغيرة (1×1×1) اللازمة لبناء مكعب مصمت أبعاده (4×4×4) يحتوي على نفق مفرغ أبعاده (1×1) يمتد في المنتصف من الأعلى إلى الأسفل مباشرة؟', ['64 مكعبًا', '60 مكعبًا', '52 مكعبًا', '48 مكعبًا'], 2],
                ],
                [
                    'cat' => 'spatial',
                    'en' => ['A 3D object viewed from the front looks like a square, and viewed from the top looks like a circle. What is the 3D object?', ['Sphere', 'Cylinder', 'Cone', 'Pyramid'], 2],
                    'ar' => ['مجسم ثلاثي الأبعاد يبدو من المنظر الأمامي على شكل مربع، ويبدو من المنظر العلوي على شكل دائرة. ما هذا المجسم؟', ['كرة', 'أسطوانة', 'مخروط', 'هرم'], 2],
                ],
                [
                    'cat' => 'spatial',
                    'en' => ['A sheet of paper is folded in half from left to right, then folded in half from top to bottom, and a circular hole is punched in the center. When completely unfolded, how many holes are in the paper?', ['1', '2', '4', '8'], 3],
                    'ar' => ['ورقة مربعة طُويت للنصف من اليسار إلى اليمين، ثم طُويت للنصف من الأعلى إلى الأسفل، وثُقبت دائرة واحدة في منتصف الورقة المطوية. عند فرد الورقة بالكامل، كم ثقبًا يظهر فيها؟', ['ثقب واحد', 'ثقبان', '4 ثقوب', '8 ثقوب'], 3],
                ],
                [
                    'cat' => 'spatial',
                    'en' => ['A clock shows 3:00. What is the angle between the hour hand and the minute hand if the clock is viewed in a mirror?', ['45°', '90°', '180°', '270°'], 2],
                    'ar' => ['ساعة حائط تشير عقاربها إلى الساعة 3:00 تمامًا. ما مقدار الزاوية بين عقرب الساعات وعقرب الدقائق إذا نظرنا إلى الساعة عبر انعكاس مرآة مستوية؟', ['45°', '90°', '180°', '270°'], 2],
                ],
            ],
        ];
    }
}
