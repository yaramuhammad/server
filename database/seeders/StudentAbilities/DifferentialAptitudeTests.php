<?php

namespace Database\Seeders\StudentAbilities;

/**
 * Differential Aptitude Tests (DAT) Mini-Battery / نموذج اختبار DAT التطبيقي الشامل.
 *
 * Source: "Differential Aptitude Tests.pdf" + "نموذج اختبار DAT التطبيقي الشامل Differential Aptitude Tests.pdf".
 * Items 1-23 are the same in both languages. Items 24-30 (Language Usage, Spelling & Syntax) are
 * localized in the source: the English version tests English and the Arabic version tests Arabic,
 * so each locale carries its own question, options and key.
 *
 * Source correction: Arabic item 11 asked for "step 4", but its key (Bottom-Left) is the dot's
 * position at step 3, as in the English version. The Arabic text now asks for step 3.
 *
 * The Arabic source also maps DAT profiles to university tracks (interpretive aid, not scored):
 *   Engineering / Architecture / Software → SR + MR + NA
 *   Medicine / Pharmacy / Life sciences   → VR + NA + AR
 *   Computer science / AI / Cybersecurity → AR + NA + PSA
 *   Business / Economics / Finance        → NA + VR + PSA
 *   Media / Law / Political science       → VR + LU + SP
 *   Graphic design / Applied arts         → SR + AR
 */
class DifferentialAptitudeTests
{
    public static function definition(): array
    {
        return [
            'title' => [
                'en' => 'Differential Aptitude Tests (DAT) Mini-Battery',
                'ar' => 'اختبار القدرات الفارقة (DAT) التطبيقي الشامل',
            ],
            'description' => [
                'en' => 'A 30-item mini-battery profiling eight aptitudes: verbal reasoning, numerical ability, abstract reasoning, spatial relations, mechanical reasoning, perceptual speed & accuracy, language usage, and spelling & syntax. The resulting profile supports academic track guidance.',
                'ar' => 'بطارية مصغرة من 30 سؤالًا ترسم ملفًا لثماني قدرات: التفكير اللفظي، والتفكير العددي، والاستدلال التجريدي، والعلاقات المكانية، والتفكير الميكانيكي، والسرعة والدقة الإدراكية، واستخدام اللغة، والإملاء والسلامة اللغوية. يدعم الملف الناتج توجيه الطالب نحو المسار الأكاديمي المناسب.',
            ],
            'instructions' => [
                'en' => 'Each question has four options and only one correct answer. Read each question carefully and choose the best answer. Each correct answer earns one point; there is no penalty for wrong answers.',
                'ar' => 'لكل سؤال أربعة بدائل وإجابة صحيحة واحدة فقط. اقرأ كل سؤال بعناية واختر الإجابة الأنسب. تُحتسب درجة واحدة لكل إجابة صحيحة، ولا يُخصم شيء على الإجابة الخاطئة.',
            ],
            'chart' => 'radar',
            'categories' => [
                'vr' => ['en' => 'Verbal Reasoning (VR)', 'ar' => 'التفكير اللفظي (VR)'],
                'na' => ['en' => 'Numerical Ability (NA)', 'ar' => 'التفكير العددي (NA)'],
                'ar' => ['en' => 'Abstract Reasoning (AR)', 'ar' => 'الاستدلال التجريدي (AR)'],
                'sr' => ['en' => 'Spatial Relations (SR)', 'ar' => 'العلاقات المكانية (SR)'],
                'mr' => ['en' => 'Mechanical Reasoning (MR)', 'ar' => 'التفكير الميكانيكي (MR)'],
                'psa' => ['en' => 'Perceptual Speed & Accuracy (PSA)', 'ar' => 'السرعة والدقة الإدراكية (PSA)'],
                'lu' => ['en' => 'Language Usage (LU)', 'ar' => 'استخدام اللغة (LU)'],
                'sp' => ['en' => 'Spelling & Syntax (SP)', 'ar' => 'الإملاء والسلامة اللغوية (SP)'],
            ],
            'questions' => [
                // ---------------- Verbal Reasoning (1-4) ----------------
                [
                    'cat' => 'vr',
                    'en' => ["Select the pair that best completes the analogy in the correct order:\n\"_______ is to POETRY as _______ is to PAINTING.\"", ['Anthology – Canvas', 'Rhyme – Colors', 'Poet – Painter', 'Stanza – Brush'], 1],
                    'ar' => ["اختر الزوج الذي يكمل الفراغين بالترتيب الصحيح ليشكل تناظرًا منطقيًا:\n\"(.......) بالنسبة للشعر مثل (.......) بالنسبة للرسم.\"", ['ديوان – لوحة', 'قافية – ألوان', 'شاعر – رسام', 'قصيدة – فرشاة'], 1],
                ],
                [
                    'cat' => 'vr',
                    'en' => ["Select the concept that completes the logical research analogy:\nHYPOTHESIS : EXPERIMENT :: THEORY : _______", ['Verification', 'Speculation', 'Observation', 'Deduction'], 1],
                    'ar' => ["اختر المفهوم الأكثر دقة لإكمال التناظر:\nفرضية : تجربة :: نظرية : .......", ['إثبات وتأكيد', 'تخمين', 'ملاحظة', 'استنباط'], 1],
                ],
                [
                    'cat' => 'vr',
                    'en' => ["Choose the term that reflects the exact semantic antonym relationship:\n\"FLEXIBILITY is to RIGIDITY as _______ is to AMBIGUITY.\"", ['Transparency', 'Complexity', 'Clarity', 'Obscurity'], 3],
                    'ar' => ["اختر الكلمة التي تمثل علاقة التضاد الدلالية الدقيقة:\n\"المرونة بالنسبة للصلابة كـ ....... بالنسبة للغموض.\"", ['الشفافية', 'التعقيد', 'الوضوح', 'الضبابية'], 3],
                ],
                [
                    'cat' => 'vr',
                    'en' => ['Which of the following pairs demonstrates the same functional relationship as (ENGINE : AUTOMOBILE)?', ['Sail : Ship', 'Heart : Body', 'Wheel : Bicycle', 'Compass : Direction'], 2],
                    'ar' => ['أي الأزواج التالية يمثل نفس العلاقة المنطقية بين (محرك : سيارة)؟', ['شراع : سفينة', 'قلب : جسم', 'عجلة : دراجة', 'بوصلة : اتجاه'], 2],
                ],

                // ---------------- Numerical Ability (5-8) ----------------
                [
                    'cat' => 'na',
                    'en' => ["Find the missing number that logically completes the sequence:\n4, 9, 19, 39, 79, [ ? ]", ['149', '159', '158', '169'], 2],
                    'ar' => ["أوجد الرقم المفقود الذي يكمل السلسلة التالية:\n4، 9، 19، 39، 79، [ ؟ ]", ['149', '159', '158', '169'], 2],
                ],
                [
                    'cat' => 'na',
                    'en' => ['If 2A + B = 14 and A − 2B = −3, what is the value of the ratio A² / B?', ['3', '5', '25/4', '25/3'], 3],
                    'ar' => ['إذا كان 2A + B = 14 و A − 2B = −3، فما قيمة النسبة A² / B؟', ['3', '5', '25/4', '25/3'], 3],
                ],
                [
                    'cat' => 'na',
                    'en' => ['Three research assistants can screen 120 survey records in 4 hours. How many hours would it take 5 research assistants working at the exact same constant rate to screen 300 survey records?', ['4 hours', '5 hours', '6 hours', '3.5 hours'], 3],
                    'ar' => ['يستطيع 3 باحثين فحص 120 استبانة خلال 4 ساعات. كم ساعة يحتاجها 5 باحثين يعملون بنفس المعدل لفحص 300 استبانة؟', ['4 ساعات', '5 ساعات', '6 ساعات', '3.5 ساعة'], 3],
                ],
                [
                    'cat' => 'na',
                    'en' => ["What is the missing term in the exponent sequence:\n1, 4, 27, 256, [ ? ]", ['625', '1024', '3125', '4096'], 3],
                    'ar' => ["ما الرقم المفقود في السلسلة الأسية التالية:\n1، 4، 27، 256، [ ؟ ]", ['625', '1024', '3125', '4096'], 3],
                ],

                // ---------------- Abstract Reasoning (9-12) ----------------
                [
                    'cat' => 'ar',
                    'en' => [
                        "Examine the figure progression rule:\n• Figure 1: Square with 1 vertical line and an arrow pointing UP.\n• Figure 2: Square with 2 vertical lines and an arrow pointing RIGHT (90° clockwise).\n• Figure 3: Square with 3 vertical lines and an arrow pointing DOWN (90° clockwise).\nWhich option correctly describes Figure 4?",
                        ['Square with 4 vertical lines and an arrow pointing LEFT', 'Square with 3 horizontal lines and an arrow pointing UP', 'Square with 4 horizontal lines and an arrow pointing RIGHT', 'Square with 1 line and an arrow pointing UP'],
                        1,
                    ],
                    'ar' => [
                        "تتبع حركة الشكل في التتابع التالي:\n• الشكل 1: مربع به خط رأسي واحد وسهم يتجه للأعلى.\n• الشكل 2: مربع به خطان رأسيان وسهم يتجه لليمين (90° مع عقارب الساعة).\n• الشكل 3: مربع به 3 خطوط رأسية وسهم يتجه للأسفل (90° إضافية).\nما صفات الشكل 4 الصحيح؟",
                        ['مربع به 4 خطوط رأسية وسهم يتجه لليسار', 'مربع به 3 خطوط أفقية وسهم يتجه للأعلى', 'مربع به 4 خطوط أفقية وسهم يتجه لليمين', 'مربع به خط واحد وسهم يتجه للأعلى'],
                        1,
                    ],
                ],
                [
                    'cat' => 'ar',
                    'en' => ["Follow the rule transformation: a circle becomes a square, the line count doubles, and the fill colour flips from Black to White.\nIf the input is a Black Circle containing 2 lines, what is the output figure?", ['White Square containing 2 lines', 'White Square containing 4 lines', 'Black Square containing 4 lines', 'White Circle containing 4 lines'], 2],
                    'ar' => ["تتبع قاعدة التغير التالية: الدائرة تتحول إلى مربع، وعدد الخطوط الداخلية يتضاعف، واللون المظلل ينعكس (أسود ← أبيض).\nإذا كان الشكل الأساسي دائرة سوداء بداخلها خطان، فما الشكل الناتج؟", ['مربع أبيض داخله خطان', 'مربع أبيض داخله 4 خطوط', 'مربع أسود داخله 4 خطوط', 'دائرة بيضاء داخلها 4 خطوط'], 2],
                ],
                [
                    'cat' => 'ar',
                    'en' => ['An arrow rotates 90° clockwise each step, while a shaded dot moves to the adjacent corner counter-clockwise. If the dot starts in the Top-Right corner at Step 1, where is the dot at Step 3?', ['Bottom-Right corner', 'Top-Left corner', 'Bottom-Left corner', 'Top-Right corner'], 3],
                    'ar' => ['إذا كان السهم يدور بزاوية 90° مع عقارب الساعة في كل خطوة، ونقطة التظليل تنتقل إلى الزاوية المجاورة عكس عقارب الساعة، فما وضع النقطة في الخطوة الثالثة إذا كانت في الخطوة الأولى في الزاوية العليا اليمنى؟', ['الزاوية السفلى اليمنى', 'الزاوية العليا اليسرى', 'الزاوية السفلى اليسرى', 'تعود إلى الزاوية العليا اليمنى'], 3],
                ],
                [
                    'cat' => 'ar',
                    'en' => ['Select the figure that does NOT belong with the others based on geometric rules:', ['Hexagon containing 3 diagonals', 'Octagon containing 4 diagonals', 'Square containing 2 diagonals', 'Pentagon containing 3 diagonals'], 4],
                    'ar' => ['أي الأشكال التالية لا ينتمي لنفس القواعد العامة للمجموعة؟', ['مسدس به 3 أقطار', 'ثماني أضلاع به 4 أقطار', 'مربع به قطران', 'خماسي أضلاع به 3 أقطار'], 4],
                ],

                // ---------------- Spatial Relations (13-16) ----------------
                [
                    'cat' => 'sr',
                    'en' => [
                        "Consider a 2D flat cross-net of a cube consisting of 6 squares:\n• The center square is shaded solid Black.\n• The opposite square in 3D assembly contains a White Circle.\n• The remaining 4 squares are plain white.\nWhen folded into a complete 3D cube, what face lies directly opposite the solid Black face?",
                        ['One of the plain white faces', 'The face containing the White Circle', 'An outer edge', 'The net cannot form a cube'],
                        2,
                    ],
                    'ar' => [
                        "لديك مخطط مسطح لورقة مقواة من 6 مربعات:\n• المربع المركزي مظلل بالكامل باللون الأسود.\n• المربع المقابل له في التجميع ثلاثي الأبعاد يحتوي على دائرة بيضاء.\n• الأوجه الأربعة المتبقية بيضاء.\nعند طي هذا المخطط ليشكل مكعبًا كاملًا، ماذا يقابل الوجه المظلل بالأسود؟",
                        ['أحد الأوجه البيضاء الفارغة', 'الوجه الذي يحتوي على الدائرة البيضاء', 'حافة المكعب الخارجية', 'لا شيء، المخطط لا يشكل مكعبًا'],
                        2,
                    ],
                ],
                [
                    'cat' => 'sr',
                    'en' => ['A 3D object viewed from the front appears as a square, and viewed from the top appears as a circle. What is the 3D object?', ['Sphere', 'Cylinder', 'Cone', 'Pyramid'], 2],
                    'ar' => ['مجسم ثلاثي الأبعاد يُنظر إليه من الأمام فيبدو على شكل مربع، ويُنظر إليه من الأعلى فيبدو على شكل دائرة. ما هذا المجسم؟', ['كرة', 'أسطوانة', 'مخروط', 'هرم'], 2],
                ],
                [
                    'cat' => 'sr',
                    'en' => ['An asymmetric 3D object is rotated 90° clockwise along its vertical axis, then flipped 180° along its horizontal axis. If an extended pin was located at the Top-Front-Right corner, where is its new position?', ['Bottom-Back-Left', 'Bottom-Front-Left', 'Top-Back-Right', 'Bottom-Back-Right'], 1],
                    'ar' => ['مجسم غير متماثل تم تدويره 90° مع عقارب الساعة حول محوره الرأسي، ثم قُلب 180° حول محوره الأفقي. إذا كان هناك دبوس مثبت في الزاوية العليا-الأمامية-اليمنى، فأين يصبح موقعه الجديد؟', ['السفلى-الخلفية-اليسرى', 'السفلى-الأمامية-اليسرى', 'العليا-الخلفية-اليمنى', 'السفلى-الخلفية-اليمنى'], 1],
                ],
                [
                    'cat' => 'sr',
                    'en' => ['A square sheet of paper is folded in half twice (left-to-right, then top-to-bottom) and a single hole is punched in the center. When completely unfolded, how many holes appear in the sheet?', ['1 hole', '2 holes', '4 holes', '8 holes'], 3],
                    'ar' => ['ورقة مربعة طُويت مرتين للنصف (من اليسار لليمين، ثم من الأعلى للأسفل)، وثُقبت ثقبًا واحدًا في المنتصف. عند فرد الورقة تمامًا، كم ثقبًا يظهر فيها؟', ['ثقب واحد', 'ثقبان', '4 ثقوب', '8 ثقوب'], 3],
                ],

                // ---------------- Mechanical Reasoning (17-20) ----------------
                [
                    'cat' => 'mr',
                    'en' => ['A mechanical system consists of two meshed gears: Gear A has 12 teeth and Gear B has 24 teeth. If Gear A rotates clockwise at 4 full revolutions per minute:', ['Gear B rotates clockwise at 4 revolutions per minute', 'Gear B rotates counter-clockwise at 2 revolutions per minute', 'Gear B rotates clockwise at 8 revolutions per minute', 'Gear B rotates counter-clockwise at 4 revolutions per minute'], 2],
                    'ar' => ['نظام ميكانيكي يتكون من ترسين متعشقين؛ الترس (أ) به 12 سنًا، والترس (ب) به 24 سنًا. إذا دار الترس (أ) في اتجاه عقارب الساعة بمعدل 4 دورات كاملة في الدقيقة:', ['يدور الترس (ب) مع عقارب الساعة بمعدل 4 دورات في الدقيقة', 'يدور الترس (ب) عكس عقارب الساعة بمعدل دورتين في الدقيقة', 'يدور الترس (ب) مع عقارب الساعة بمعدل 8 دورات في الدقيقة', 'يدور الترس (ب) عكس عقارب الساعة بمعدل 4 دورات في الدقيقة'], 2],
                ],
                [
                    'cat' => 'mr',
                    'en' => ['A Class 1 lever is 2 meters long. A weight of 100 kg is placed on one end, 0.5 meters from the fulcrum. What force is required at the opposite end (1.5 meters from the fulcrum) to achieve equilibrium?', ['33.3 kg', '50 kg', '100 kg', '300 kg'], 1],
                    'ar' => ['رافعة من النوع الأول طولها 2 متر. وُضع ثقل مقداره 100 كجم على أحد طرفيها على بعد 0.5 متر من نقطة الارتكاز. ما القوة اللازمة عند الطرف الآخر (على بعد 1.5 متر من نقطة الارتكاز) لإحداث التوازن؟', ['33.3 كجم', '50 كجم', '100 كجم', '300 كجم'], 1],
                ],
                [
                    'cat' => 'mr',
                    'en' => ['In a compound pulley system containing two movable pulleys and one fixed pulley, what minimal force is required to lift a load weighing 200 N?', ['200 N', '100 N', '50 N', '25 N'], 3],
                    'ar' => ['في نظام بكرات مركب يحتوي على بكرتين متحركتين وبكرة ثابتة، إذا أردنا رفع حمولة وزنها 200 نيوتن، فما القوة الدنيا المطلوبة لسحب الحبل؟', ['200 نيوتن', '100 نيوتن', '50 نيوتن', '25 نيوتن'], 3],
                ],
                [
                    'cat' => 'mr',
                    'en' => ["When a fluid flows through a pipe whose diameter narrows to half, how do its velocity and pressure change according to Bernoulli's principle?", ['Velocity increases and pressure increases', 'Velocity increases and pressure decreases', 'Velocity decreases and pressure increases', 'Velocity decreases and pressure decreases'], 2],
                    'ar' => ['عند اندفاع سائل عبر أنبوب يضيق قطره إلى النصف، كيف تتغير سرعة السائل وضغطه وفق مبدأ برنولي؟', ['تزداد السرعة ويزداد الضغط', 'تزداد السرعة ويقل الضغط', 'تقل السرعة ويزداد الضغط', 'تقل السرعة ويقل الضغط'], 2],
                ],

                // ---------------- Perceptual Speed & Accuracy (21-23) ----------------
                [
                    'cat' => 'psa',
                    'en' => ["Compare the target code to the options and identify the exact match:\nTarget code: Xk-8092-bM", ['Xk-8029-bM', 'Xk-8092-bm', 'Xk-8092-bM', 'XK-8092-bM'], 3],
                    'ar' => ["قارن الكود الهدف بالبدائل وحدد المطابق تمامًا:\nالكود الهدف: Xk-8092-bM", ['Xk-8029-bM', 'Xk-8092-bm', 'Xk-8092-bM', 'XK-8092-bM'], 3],
                ],
                [
                    'cat' => 'psa',
                    'en' => ["Match the following serial number accurately:\nTarget number: 98420157-A", ['98420157-a', '98420157-A', '98421057-A', '98240157-A'], 2],
                    'ar' => ["طابق الرقم المسلسل التالي بدقة:\nالرقم الهدف: 98420157-A", ['98420157-a', '98420157-A', '98421057-A', '98240157-A'], 2],
                ],
                [
                    'cat' => 'psa',
                    'en' => ["Compare the alphanumeric string:\nTarget string: pQrST-99-vW", ['pQrST-99-vW', 'pQRST-99-vW', 'pQrST-99-Vw', 'pQrST-69-vW'], 1],
                    'ar' => ["قارن السلسلة الأبجدية الرقمية التالية:\nالسلسلة الهدف: pQrST-99-vW", ['pQrST-99-vW', 'pQRST-99-vW', 'pQrST-99-Vw', 'pQrST-69-vW'], 1],
                ],

                // ---------------- Language Usage (24-27) — localized per language ----------------
                [
                    'cat' => 'lu',
                    'en' => [
                        'Identify the sentence that contains a grammatical structure error:',
                        ['Neither the principal investigator nor the research assistants were aware of the data anomaly.', 'The team of quantitative analysts have submitted their individual reports today.', 'Between you and I, the statistical model requires full recalibration.', 'Had the committee reviewed the protocol earlier, the delays would have been avoided.'],
                        3,
                    ],
                    'ar' => [
                        'حدد العبارة التي تضمنت تركيبًا لغويًا غير صحيح نحويًا:',
                        ['إن للمصلحين الاجتماعيين دورًا حيويًا في نهضة المجتمعات.', 'لم يتبقَّ من الوقت إلا دقائق معدودة لاستكمال الاختبار.', 'ظل المعلمون يحرصون على رعاية الطلاب الموهوبون.', 'أصبح الاستثمار في رأس المال البشري ضرورة ملحة.'],
                        3,
                    ],
                ],
                [
                    'cat' => 'lu',
                    'en' => ["Select the option that uses correct subjunctive mood grammar:\n\"The academic review board requested that the lead researcher _______ the consent documentation prior to participant recruitment.\"", ['modifies', 'modified', 'modify', 'will modify'], 3],
                    'ar' => ["اختر الإعراب الصحيح لكلمة (طالبًا) في الجملة:\n\"لا طالبًا للعلم يتوانى عن تحقيق أهدافه الأكاديمية.\"", ['اسم لا النافية للجنس مبني على الفتح', 'اسم لا النافية للجنس شبيه بالمضاف منصوب وعلامة نصبه الفتحة', 'مبتدأ مرفوع وعلامة رفعه الضمة', 'مفعول به مقدم منصوب'], 2],
                ],
                [
                    'cat' => 'lu',
                    'en' => [
                        'Select the sentence that maintains precise parallel structure:',
                        ['"The new curriculum aims not only at improving quantitative skills but also at enhancing qualitative synthesis."', '"The new curriculum aims not only at improving quantitative skills but also enhances qualitative synthesis."', '"The new curriculum aims to improve quantitative skills and also enhancing qualitative synthesis."', '"The new curriculum aims at improving quantitative skills, and qualitative synthesis is enhanced."'],
                        1,
                    ],
                    'ar' => [
                        'حدد الجملة التي احتوت على أسلوب استثناء (ناقص منفي) صحيح:',
                        ['ما حضر من الطلاب إلا أحمدُ.', 'قرأتُ الكتبَ إلا كتابًا.', 'ما جاء أحدٌ إلا عليًّا.', 'حضر الجميعُ غيرَ زيدٍ.'],
                        1,
                    ],
                ],
                [
                    'cat' => 'lu',
                    'en' => [
                        'Select the most appropriate academic transition phrase to connect cause and effect:',
                        ['"And so because of that thing, the results came out like this."', '"Consequently, empirical analysis demonstrates that the observed variables directly influenced..."', '"So due to this state, the numbers turned out to be high."', '"And that is why the outcome happened in this manner."'],
                        2,
                    ],
                    'ar' => [
                        'اختر التركيب الأكثر دقة ورصانة للربط في النصوص الأكاديمية:',
                        ['وبناءً على هذا الشيء أدى ذلك إلى ظهور النتائج.', 'ونتيجةً لذلك، يتبين أن المتغيرات المذكورة أفضت منطقيًا إلى...', 'وعشان كدا النتائج ظهرت بالشكل ده.', 'وبسبب هذه الحالة قامت النتائج تطلع هكذا.'],
                        2,
                    ],
                ],

                // ---------------- Spelling & Syntax (28-30) — localized per language ----------------
                [
                    'cat' => 'sp',
                    'en' => ['Which of the following words is spelled incorrectly?', ['Accommodate', 'Maintenance', 'Occurred', 'Receivement'], 4],
                    'ar' => ['أي الكلمات التالية تضمنت خطأً إملائيًا في رسم الهمزة أو الألف؟', ['مسؤولية', 'إستراتيجية', 'انتقال', 'شئون'], 2],
                ],
                [
                    'cat' => 'sp',
                    'en' => [
                        'Identify the sentence with 100% correct spelling and capitalization:',
                        ['"The principal scholar emphasized that research integrity is essential for institutional accreditation."', '"The prinicipal scholar emphasized that research integrity is essential for institutional acreditation."', '"The principal scholar emphasized that research integrety is esential for institutional accreditation."', '"The principle scholar emphasized that research integrity is essential for institutional accreditetion."'],
                        1,
                    ],
                    'ar' => [
                        'حدد الجملة التي كُتبت فيها همزات القطع والوصل بأسلوب إملائي صحيح 100%:',
                        ['إنتقل الطالب إلى أستاذ إبراهيم لإستفادة من خبرته.', 'انتقل الطالبُ إلى الأستاذِ إبراهيمَ للاستفادةِ من خبرتِه.', 'انتقل الطالب الى استاذ ابراهيم للاستفادة من خبرته.', 'إنعقد الاجتماع بإشراف رئيس الادارة.'],
                        2,
                    ],
                ],
                [
                    'cat' => 'sp',
                    'en' => ["Select the word that correctly completes the sentence with proper mechanics:\n\"The long-term _______ of occupational stress on academic productivity must be systematically evaluated.\"", ['affect', 'effect', 'effected', 'affecting'], 2],
                    'ar' => ['ما السبب الإملائي الصحيح لكتابة الهمزة على نبرة (ياء) في كلمة (بِئْر)؟', ['لأنها ساكنة بعد حرف مكسور', 'لأنها مفتوحة بعد كسر', 'لأنها مكسورة بعد سكون', 'للضرورة الإملائية الشائعة'], 1],
                ],
            ],
        ];
    }
}
