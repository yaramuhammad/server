<?php

namespace Database\Seeders\StudentAbilities;

/**
 * Standardized Natural Sciences Achievement Test / اختبار التحصيل القياسي في العلوم الطبيعية.
 *
 * Source: "Standardized Natural Sciences Achievement Test.pdf" + "اختبار التحصيل القياسي في العلوم الطبيعية.pdf".
 * The Arabic source lists item 5's first two options in swapped order (keyed accordingly); here the
 * Arabic options follow the English order so both locales share one key.
 */
class NaturalSciencesAchievementTest
{
    public static function definition(): array
    {
        return [
            'title' => [
                'en' => 'Standardized Natural Sciences Achievement Test',
                'ar' => 'اختبار التحصيل القياسي في العلوم الطبيعية',
            ],
            'description' => [
                'en' => 'A 30-item achievement test covering physics, chemistry and biology (10 items each).',
                'ar' => 'اختبار تحصيلي من 30 سؤالًا يغطي الفيزياء والكيمياء والأحياء (10 أسئلة لكل منها).',
            ],
            'instructions' => [
                'en' => 'Each question has four options and only one correct answer. You may use scratch paper for calculations. Each correct answer earns one point; there is no penalty for wrong answers.',
                'ar' => 'لكل سؤال أربعة بدائل وإجابة صحيحة واحدة فقط. يمكنك استخدام ورقة خارجية لإجراء الحسابات. تُحتسب درجة واحدة لكل إجابة صحيحة، ولا يُخصم شيء على الإجابة الخاطئة.',
            ],
            'chart' => 'column',
            'categories' => [
                'physics' => ['en' => 'Physics', 'ar' => 'الفيزياء'],
                'chemistry' => ['en' => 'Chemistry', 'ar' => 'الكيمياء'],
                'biology' => ['en' => 'Biology', 'ar' => 'الأحياء'],
            ],
            'questions' => [
                // ---------------- Physics (1-10) ----------------
                [
                    'cat' => 'physics',
                    'en' => ['A uniform conducting wire of length L and resistance R is uniformly stretched until its length becomes 2L while keeping its total volume constant. What is the new electrical resistance of the stretched wire?', ['2R', '4R', 'R/2', 'R/4'], 2],
                    'ar' => ['سلك موصل منتظم المقطع طوله L ومقاومته الكهربية R. إذا سُحب السلك بانتظام حتى أصبح طوله ضعف ما كان عليه (2L) مع ثبوت حجمه الكلي، فكم تصبح مقاومته الكهربية الجديدة؟', ['2R', '4R', 'R/2', 'R/4'], 2],
                ],
                [
                    'cat' => 'physics',
                    'en' => ['A particle is launched vertically upward from the ground with an initial velocity v₀. Neglecting air resistance, at what fraction of its maximum height (H_max) is its kinetic energy equal to its potential energy?', ['1/4 H_max', '1/2 H_max', '3/4 H_max', '1/√2 H_max'], 2],
                    'ar' => ['قُذف جسم رأسيًا إلى أعلى من سطح الأرض بسرعة ابتدائية v₀. بإهمال مقاومة الهواء، عند أي كسر من أقصى ارتفاع يصله الجسم (H_max) تكون طاقة حركته مساوية لطاقة وضعه؟', ['1/4 H_max', '1/2 H_max', '3/4 H_max', '1/√2 H_max'], 2],
                ],
                [
                    'cat' => 'physics',
                    'en' => ['An ideal step-up transformer has a primary coil with 200 turns and a secondary coil with 1000 turns. If the primary alternating voltage is 120 V and the primary current is 5 A, what is the secondary current?', ['1 A', '25 A', '600 A', '0.2 A'], 1],
                    'ar' => ['محول كهربائي مثالي رافع للجهد، عدد لفات ملفه الابتدائي 200 لفة وعدد لفات ملفه الثانوي 1000 لفة. إذا كان جهد الملف الابتدائي 120 V وتياره 5 A، فكم تكون شدة تيار الملف الثانوي؟', ['1 A', '25 A', '600 A', '0.2 A'], 1],
                ],
                [
                    'cat' => 'physics',
                    'en' => ['Light of frequency f shines on a metallic surface with work function Φ, causing photoelectrons to be emitted with maximum kinetic energy K_max. If the light frequency is doubled to 2f, what will be the new maximum kinetic energy (K′_max)?', ['2K_max', '2K_max + Φ', 'K_max + hf', '2K_max − Φ'], 3],
                    'ar' => ['سقط ضوء أحادي اللون تردده f على سطح معدن دالة شغله Φ، فانبعثت إلكترونات ضوئية بطاقة حركة عظمى K_max. إذا تضاعف تردد الضوء الساقط إلى 2f، فإن طاقة الحركة العظمى الجديدة (K′_max) للإلكترونات المنبعثة تصبح:', ['2K_max', '2K_max + Φ', 'K_max + hf', '2K_max − Φ'], 3],
                ],
                [
                    'cat' => 'physics',
                    'en' => ['Two long, straight parallel conductors separated by a distance d carry currents I₁ and I₂ in opposite directions. The magnetic force per unit length between them is:', ['Attractive and proportional to 1/d', 'Repulsive and proportional to 1/d', 'Repulsive and proportional to 1/d²', 'Zero because the currents are in opposite directions'], 2],
                    'ar' => ['سلكان مستقيمان طويلان ومتوازيان تفصل بينهما مسافة d ويحملان تيارين كهربيين I₁ و I₂ في اتجاهين متضادين. القوة المغناطيسية المتبادلة لوحدة الأطوال بينهما تكون:', ['قوة تجاذب تتناسب عكسيًا مع d', 'قوة تنافر تتناسب عكسيًا مع d', 'قوة تنافر تتناسب عكسيًا مع d²', 'مساوية للصفر لأن التيارين في اتجاهين متضادين'], 2],
                ],
                [
                    'cat' => 'physics',
                    'en' => ['A simple pendulum has a period T on Earth. If the length of the pendulum is quadrupled (4L) and it is moved to a planet where gravitational acceleration is twice that of Earth (2g), what is its new period?', ['T√2', '2T', 'T/√2', '4T'], 1],
                    'ar' => ['بندول بسيط زمنه الدوري T على سطح الأرض. إذا زاد طول خيط البندول إلى أربعة أضعافه (4L) ونُقل إلى كوكب عجلة جاذبيته ضعف عجلة جاذبية الأرض (2g)، فكم يصبح زمنه الدوري الجديد؟', ['T√2', '2T', 'T/√2', '4T'], 1],
                ],
                [
                    'cat' => 'physics',
                    'en' => ['In a series RLC circuit at resonance, which of the following must hold true regarding the inductive reactance (X_L), capacitive reactance (X_C), and total impedance (Z)?', ['X_L > X_C and Z = X_L + X_C', 'X_L = X_C and Z = R', 'X_L = 0 and Z = X_C', 'X_L = X_C and Z = 0'], 2],
                    'ar' => ['في دائرة تيار متردد تحتوي على مقاومة وملف ومكثف (RLC) على التوالي، أي الشروط التالية يتحقق عند حالة الرنين حيث تكون المعاوقة الكلية (Z) أقل ما يمكن؟', ['X_L > X_C و Z = X_L + X_C', 'X_L = X_C و Z = R', 'X_L = 0 و Z = X_C', 'X_L = X_C و Z = 0'], 2],
                ],
                [
                    'cat' => 'physics',
                    'en' => ['A convex lens has a focal length f = 10 cm. An object is placed 15 cm in front of the lens. The image formed is:', ['Real, inverted, and magnified', 'Real, inverted, and diminished', 'Virtual, erect, and magnified', 'Virtual, inverted, and the same size'], 1],
                    'ar' => ['وُضع جسم على بعد 15 cm أمام عدسة محدبة بعدها البؤري f = 10 cm. تكون الصورة المتكونة:', ['حقيقية، مقلوبة، ومكبرة', 'حقيقية، مقلوبة، ومصغرة', 'تقديرية، معتدلة، ومكبرة', 'تقديرية، مقلوبة، ومساوية للجسم'], 1],
                ],
                [
                    'cat' => 'physics',
                    'en' => ["According to de Broglie's hypothesis, if an electron and a proton have the same kinetic energy, how do their de Broglie wavelengths (λₑ and λₚ) compare?", ['λₑ = λₚ', 'λₑ > λₚ', 'λₑ < λₚ', 'Cannot be determined without the photon speed'], 2],
                    'ar' => ['طبقًا لفرضية دي برولي، إذا كان لإلكترون وبروتون نفس طاقة الحركة، فكيف يُقارن طول موجة دي برولي للإلكترون (λₑ) بطولها للبروتون (λₚ)؟', ['λₑ = λₚ', 'λₑ > λₚ', 'λₑ < λₚ', 'لا يمكن التحديد دون معرفة سرعة الفوتون'], 2],
                ],
                [
                    'cat' => 'physics',
                    'en' => ["According to Snell's law, when a light ray travels from medium 1 (refractive index n₁ = 1.5) into medium 2 (refractive index n₂ = 1.0) at an angle of incidence equal to the critical angle (θc), the angle of refraction is:", ['0°', '45°', '90°', '180°'], 3],
                    'ar' => ['طبقًا لقانون سنل، عندما ينتقل شعاع ضوئي من وسط أكبر كثافة ضوئية (معامل انكساره n₁ = 1.5) إلى وسط أقل كثافة (معامل انكساره n₂ = 1.0) بزاوية سقوط تساوي الزاوية الحرجة (θc)، فإن زاوية الانكسار تكون:', ['0°', '45°', '90°', '180°'], 3],
                ],

                // ---------------- Chemistry (11-20) ----------------
                [
                    'cat' => 'chemistry',
                    'en' => ["For the exothermic gas-phase reversible reaction:\nN₂(g) + 3H₂(g) ⇌ 2NH₃(g)   (ΔH = −92 kJ/mol)\nWhich set of conditions will maximize the equilibrium yield of ammonia (NH₃)?", ['High temperature and low pressure', 'Low temperature and high pressure', 'High temperature and high pressure', 'Low temperature and continuous catalyst removal'], 2],
                    'ar' => ["في التفاعل الغازي الانعكاسي الطارد للحرارة التالي:\nN₂(g) + 3H₂(g) ⇌ 2NH₃(g)   (ΔH = −92 kJ/mol)\nأي الظروف التالية تؤدي إلى زيادة حصيلة غاز النشادر (NH₃) عند الاتزان؟", ['رفع درجة الحرارة وتقليل الضغط', 'خفض درجة الحرارة وزيادة الضغط', 'رفع درجة الحرارة وزيادة الضغط', 'خفض درجة الحرارة مع السحب المستمر للعامل الحفاز'], 2],
                ],
                [
                    'cat' => 'chemistry',
                    'en' => ['What is the pH of a 0.05 M aqueous solution of sulfuric acid (H₂SO₄), assuming complete dissociation of both protons?', ['1.0', '1.3', '2.0', '0.05'], 1],
                    'ar' => ['ما الأس الهيدروجيني (pH) لمحلول مائي من حمض الكبريتيك (H₂SO₄) تركيزه 0.05 M، بفرض أن الحمض يتأين كليًا (البروتونان معًا)؟', ['1.0', '1.3', '2.0', '0.05'], 1],
                ],
                [
                    'cat' => 'chemistry',
                    'en' => ['Which of the following 0.1 M aqueous solutions will exhibit the lowest freezing point?', ['C₆H₁₂O₆ (glucose)', 'NaCl', 'CaCl₂', 'AlCl₃'], 4],
                    'ar' => ['أي المحاليل المائية التالية ذات التركيز نفسه (0.1 M) يمتلك أقل درجة تجمد؟', ['C₆H₁₂O₆ (جلوكوز)', 'NaCl', 'CaCl₂', 'AlCl₃'], 4],
                ],
                [
                    'cat' => 'chemistry',
                    'en' => ['In the standard galvanic cell reaction Zn(s) + Cu²⁺(aq) → Zn²⁺(aq) + Cu(s), which species acts as the reducing agent at the anode?', ['Zn(s)', 'Cu²⁺(aq)', 'Zn²⁺(aq)', 'Cu(s)'], 1],
                    'ar' => ['في خلية دانيال الجلفانية ذات التفاعل Zn(s) + Cu²⁺(aq) → Zn²⁺(aq) + Cu(s)، ما المادة التي تعمل عاملًا مختزلًا عند الأنود؟', ['Zn(s)', 'Cu²⁺(aq)', 'Zn²⁺(aq)', 'Cu(s)'], 1],
                ],
                [
                    'cat' => 'chemistry',
                    'en' => ['According to IUPAC nomenclature, what is the correct systematic name of the organic compound (CH₃)₂CH−CH₂−CHO?', ['3-methylbutanal', '2-methylbutanal', 'Isovaleric alcohol', '3-methylbutan-1-one'], 1],
                    'ar' => ['ما الاسم النظامي (حسب قواعد IUPAC) للمركب العضوي التالي: (CH₃)₂CH−CH₂−CHO؟', ['3-ميثيل بيوتانال', '2-ميثيل بيوتانال', 'كحول أيزوفاليريك', '3-ميثيل بيوتان-1-ون'], 1],
                ],
                [
                    'cat' => 'chemistry',
                    'en' => ['What volume of 0.2 M NaOH solution is required to completely neutralize 25 mL of a 0.1 M H₂SO₄ solution?', ['12.5 mL', '25.0 mL', '50.0 mL', '6.25 mL'], 2],
                    'ar' => ['ما حجم محلول هيدروكسيد الصوديوم (NaOH) الذي تركيزه 0.2 M اللازم لمعادلة 25 mL من محلول حمض الكبريتيك (H₂SO₄) الذي تركيزه 0.1 M معادلة تامة؟', ['12.5 mL', '25.0 mL', '50.0 mL', '6.25 mL'], 2],
                ],
                [
                    'cat' => 'chemistry',
                    'en' => ['The rate law for a reaction A + B → C is determined experimentally to be Rate = k[A]²[B]. If the concentration of A is doubled while the concentration of B is halved, how does the initial rate change?', ['It remains unchanged', 'It increases by a factor of 2', 'It increases by a factor of 4', 'It decreases by half'], 2],
                    'ar' => ['معادلة سرعة تفاعل ما هي Rate = k[A]²[B]. إذا تضاعف تركيز المادة A وانخفض تركيز المادة B إلى النصف، فماذا يحدث لسرعة التفاعل الابتدائية؟', ['تظل ثابتة دون تغيير', 'تزداد إلى الضعف (تُضرب في 2)', 'تزداد إلى 4 أضعاف', 'تقل إلى النصف'], 2],
                ],
                [
                    'cat' => 'chemistry',
                    'en' => ['Which quantum number primarily determines the main energy level (shell) and size of an atomic orbital?', ['Principal quantum number (n)', 'Azimuthal quantum number (l)', 'Magnetic quantum number (mₗ)', 'Spin quantum number (mₛ)'], 1],
                    'ar' => ['أي أعداد الكم التالية يحدد مستوى الطاقة الرئيسي (المدار) وحجم السحابة الإلكترونية في الذرة؟', ['عدد الكم الرئيسي (n)', 'عدد الكم الثانوي (l)', 'عدد الكم المغناطيسي (mₗ)', 'عدد الكم المغزلي (mₛ)'], 1],
                ],
                [
                    'cat' => 'chemistry',
                    'en' => ['Oxidation of a secondary alcohol using an oxidizing agent such as acidified potassium dichromate (K₂Cr₂O₇ / H⁺) yields:', ['An aldehyde', 'A carboxylic acid', 'A ketone', 'An ester'], 3],
                    'ar' => ['عند أكسدة كحول ثانوي باستخدام عامل مؤكسد مثل ثاني كرومات البوتاسيوم المحمضة (K₂Cr₂O₇ / H⁺)، ينتج:', ['ألدهيد', 'حمض كربوكسيلي', 'كيتون', 'إستر'], 3],
                ],
                [
                    'cat' => 'chemistry',
                    'en' => ['Based on periodic trends, which of the following elements has the highest first ionization energy?', ['Sodium (Na)', 'Magnesium (Mg)', 'Chlorine (Cl)', 'Neon (Ne)'], 4],
                    'ar' => ['بناءً على التدرج في الخواص الدورية للعناصر، أي العناصر التالية يمتلك أعلى طاقة تأين أولى؟', ['الصوديوم (Na)', 'الماغنسيوم (Mg)', 'الكلور (Cl)', 'النيون (Ne)'], 4],
                ],

                // ---------------- Biology (21-30) ----------------
                [
                    'cat' => 'biology',
                    'en' => ['If a double-stranded DNA sample is found to contain 28% thymine, what is the percentage of cytosine in this sample?', ['22%', '28%', '44%', '56%'], 1],
                    'ar' => ['إذا كانت نسبة الثايمين (Thymine) في عينة من DNA مزدوج الشريط تساوي 28%، فما نسبة السيتوسين (Cytosine) في هذه العينة؟', ['22%', '28%', '44%', '56%'], 1],
                ],
                [
                    'cat' => 'biology',
                    'en' => ['During aerobic cellular respiration, which phase yields the largest number of ATP molecules per glucose molecule via oxidative phosphorylation?', ['Glycolysis', 'Pyruvate oxidation', 'The Krebs cycle (citric acid cycle)', 'The electron transport chain and chemiosmosis'], 4],
                    'ar' => ['أثناء التنفس الخلوي الهوائي، أي المراحل التالية تنتج أكبر عدد من جزيئات ATP لكل جزيء جلوكوز عن طريق الفسفرة التأكسدية؟', ['التحلل السكري (Glycolysis)', 'أكسدة حمض البيروفيك', 'دورة كريبس (Citric Acid Cycle)', 'سلسلة نقل الإلكترون والأسموزية الكيميائية'], 4],
                ],
                [
                    'cat' => 'biology',
                    'en' => ['In human genetics, if both parents are heterozygous carriers (Aa) of an autosomal recessive disorder, what is the probability that their child will be phenotypically healthy?', ['25%', '50%', '75%', '100%'], 3],
                    'ar' => ['في الوراثة البشرية، إذا كان كلا الأبوين حاملين لمرض وراثي متنحٍ محمول على كروموسوم جسدي (بتركيب جيني Aa)، فما احتمال إنجاب طفل سليم ظاهريًا؟', ['25%', '50%', '75%', '100%'], 3],
                ],
                [
                    'cat' => 'biology',
                    'en' => ['Which organelle contains hydrolytic digestive enzymes responsible for the intracellular degradation of worn-out organelles and foreign pathogens?', ['Peroxisome', 'Lysosome', 'Golgi apparatus', 'Endoplasmic reticulum'], 2],
                    'ar' => ['أي العضيات الخلوية التالية تحتوي على إنزيمات هاضمة (هيدروليزية) مسؤولة عن التحلل الذاتي للعضيات المتهالكة والمواد الغريبة؟', ['البيروكسيسوم (Peroxisome)', 'الليسوسوم (Lysosome)', 'جهاز جولجي (Golgi Apparatus)', 'الشبكة الإندوبلازمية'], 2],
                ],
                [
                    'cat' => 'biology',
                    'en' => ['The hormone responsible for stimulating sodium reabsorption in the distal convoluted tubules and collecting ducts of the nephron to regulate blood pressure is:', ['Antidiuretic hormone (ADH / vasopressin)', 'Aldosterone', 'Calcitonin', 'Insulin'], 2],
                    'ar' => ['الهرمون المسؤول عن تحفيز إعادة امتصاص الصوديوم في الأنيبيبات الملتوية البعيدة والقنوات الجامعة في الكلية لتنظيم ضغط الدم هو:', ['الهرمون المانع لإدرار البول (ADH)', 'الألدوستيرون (Aldosterone)', 'الكالسيتونين', 'الأنسولين'], 2],
                ],
                [
                    'cat' => 'biology',
                    'en' => ['During the light-dependent reactions of photosynthesis, the photolysis of water molecules directly produces:', ['Glucose, ATP, and CO₂', 'Oxygen gas (O₂), protons (H⁺), and electrons (e⁻)', 'NADPH and pyruvate', 'Carbon dioxide (CO₂) and H₂O'], 2],
                    'ar' => ['أثناء التفاعلات الضوئية للبناء الضوئي في النبات، ينتج عن التحلل الضوئي للماء (H₂O) بشكل مباشر:', ['جلوكوز و ATP و CO₂', 'غاز الأكسجين (O₂) وبروتونات (H⁺) وإلكترونات (e⁻)', 'NADPH وحمض البيروفيك', 'ثاني أكسيد الكربون والماء'], 2],
                ],
                [
                    'cat' => 'biology',
                    'en' => ['Which phase of meiosis is characterized by the separation of homologous chromosome pairs moving toward opposite poles of the cell?', ['Anaphase I', 'Anaphase II', 'Metaphase I', 'Telophase II'], 1],
                    'ar' => ['أي أطوار الانقسام الميوزي (الاختزالي) يتميز بانفصال أزواج الكروموسومات المتماثلة وتحركها نحو قطبي الخلية المتقابلين؟', ['الطور الانفصالي الأول (Anaphase I)', 'الطور الانفصالي الثاني (Anaphase II)', 'الطور الاستوائي الأول (Metaphase I)', 'الطور النهائي الثاني (Telophase II)'], 1],
                ],
                [
                    'cat' => 'biology',
                    'en' => ['The primary enzyme responsible for unwinding the double-helical DNA molecule at the replication fork during DNA replication is:', ['DNA polymerase III', 'DNA ligase', 'Helicase', 'RNA primase'], 3],
                    'ar' => ['الإنزيم الرئيسي المسؤول عن فك الالتواء الحلزوني لشريطي DNA عند شوكة التضاعف أثناء تضاعف DNA هو:', ['إنزيم بلمرة DNA (DNA Polymerase)', 'إنزيم الربط (DNA Ligase)', 'إنزيم الهيليكيز (Helicase)', 'إنزيم البريميز (RNA Primase)'], 3],
                ],
                [
                    'cat' => 'biology',
                    'en' => ['In the human nervous system, the rapid transmission of an action potential along a myelinated axon by jumping from one node of Ranvier to the next is known as:', ['Continuous conduction', 'Saltatory conduction', 'Synaptic summation', 'Depolarization threshold'], 2],
                    'ar' => ['في الجهاز العصبي البشري، يُعرف الانتقال السريع لجهد الفعل على طول المحور العصبي المغلف بالميالين عبر القفز من عقدة رانفييه إلى أخرى بـ:', ['التوصيل المستمر', 'التوصيل القفزي (Saltatory Conduction)', 'التجميع التشابكي', 'عتبة إزالة الاستقطاب'], 2],
                ],
                [
                    'cat' => 'biology',
                    'en' => ['What is the functional role of tRNA (transfer RNA) during the translation stage of protein synthesis?', ['Carrying genetic instructions from DNA in the nucleus to the ribosome', 'Delivering specific amino acids to the ribosome to match mRNA codons', 'Catalyzing the formation of the peptide bond between amino acids', 'Inhibiting transcription when target protein levels are elevated'], 2],
                    'ar' => ['ما الدور الوظيفي لـ tRNA (الـ RNA الناقل) أثناء عملية الترجمة لبناء البروتين؟', ['نقل الشفرة الوراثية من النواة إلى الريبوسوم', 'نقل حمض أميني محدد إلى الريبوسوم بناءً على كودون الـ mRNA', 'تحفيز تكوين الرابطة الببتيدية بين الأحماض الأمينية', 'تثبيط عملية النسخ عند ارتفاع مستوى البروتين'], 2],
                ],
            ],
        ];
    }
}
