<?php

namespace Database\Seeders\StudentAbilities;

/**
 * Standardized Social Sciences Achievement Test / اختبار التحصيل القياسي في العلوم الاجتماعية.
 *
 * Source: "Standardized Social Sciences Achievement Test.pdf" + "اختبار التحصيل القياسي في العلوم الاجتماعية.pdf".
 */
class SocialSciencesAchievementTest
{
    public static function definition(): array
    {
        return [
            'title' => [
                'en' => 'Standardized Social Sciences Achievement Test',
                'ar' => 'اختبار التحصيل القياسي في العلوم الاجتماعية',
            ],
            'description' => [
                'en' => 'A 30-item achievement test covering five social-science disciplines: geography, history, psychology, philosophy and sociology (6 items each).',
                'ar' => 'اختبار تحصيلي من 30 سؤالًا يغطي خمسة فروع من العلوم الاجتماعية: الجغرافيا، والتاريخ، وعلم النفس، والفلسفة، وعلم الاجتماع (6 أسئلة لكل فرع).',
            ],
            'instructions' => [
                'en' => 'Each question has four options and only one correct answer. Read each question carefully and choose the best answer. Each correct answer earns one point; there is no penalty for wrong answers.',
                'ar' => 'لكل سؤال أربعة بدائل وإجابة صحيحة واحدة فقط. اقرأ كل سؤال بعناية واختر الإجابة الأنسب. تُحتسب درجة واحدة لكل إجابة صحيحة، ولا يُخصم شيء على الإجابة الخاطئة.',
            ],
            'chart' => 'column',
            'categories' => [
                'geography' => ['en' => 'Geography', 'ar' => 'الجغرافيا'],
                'history' => ['en' => 'History', 'ar' => 'التاريخ'],
                'psychology' => ['en' => 'Psychology', 'ar' => 'علم النفس'],
                'philosophy' => ['en' => 'Philosophy', 'ar' => 'الفلسفة'],
                'sociology' => ['en' => 'Sociology', 'ar' => 'علم الاجتماع'],
            ],
            'questions' => [
                // ---------------- Geography (1-6) ----------------
                [
                    'cat' => 'geography',
                    'en' => ["Which of the following factors is most decisive in shaping a nation's competitive advantage within the contemporary global knowledge-based economy?", ['Vast territorial land area and heavily fortified natural borders.', 'Exclusive reliance on the extraction and export of crude petroleum and mineral resources.', 'High human capital development, robust innovation systems, and digital technological integration.', 'Possessing the longest coastline and regional trade ports.'], 3],
                    'ar' => ['أي العوامل التالية يُعد الأكثر حسمًا في تشكيل الميزة التنافسية للدول في اقتصاد المعرفة العالمي المعاصر؟', ['اتساع المساحة الجغرافية ووفرة الحدود الطبيعية المحصنة.', 'التركيز الحصري على استخراج وتصدير الموارد البترولية والمعدنية الخام.', 'قوة رأس المال البشري، ومنظومة الابتكار، والقدرة على توظيف التكنولوجيا الرقمية.', 'امتلاك أطول الشواطئ البحرية والموانئ التجارية الإقليمية.'], 3],
                ],
                [
                    'cat' => 'geography',
                    'en' => ['The Demographic Transition Model (DTM) in its third phase is characterized by which of the following phenomena?', ['High birth rates coupled with a sharp decline in mortality rates, generating exponential population surges.', 'Declining birth rates coupled with stable, low death rates, stabilizing population growth at lower levels.', 'Complete equilibrium between high birth rates and high death rates (primitive stability).', 'Mass rural migration driven by industrial decentralization.'], 2],
                    'ar' => ['يتميز نموذج التحول الديموغرافي (Demographic Transition Model) في مرحلته الثالثة بأي من الظواهر التالية؟', ['ارتفاع معدلات المواليد مع انخفاض حاد في معدلات الوفيات.', 'انخفاض معدلات المواليد مع استقرار معدلات الوفيات عند مستوى منخفض، لتستقر الزيادة السكانية عند مستويات منخفضة.', 'تساوٍ تام بين معدلات المواليد والوفيات عند مستويات مرتفعة جدًا (ثبات بدائي).', 'هجرة سكانية واسعة النطاق من الحضر إلى الريف.'], 2],
                ],
                [
                    'cat' => 'geography',
                    'en' => ["According to Halford Mackinder's \"Heartland Theory\" in geopolitics, strategic global dominance is centered upon:", ['Controlling global maritime shipping lanes and international straits.', 'Controlling the pivot area in the heart of Eurasia.', 'Financial and economic dominance over developing nations through foreign direct investment.', 'Absolute possession of intercontinental ballistic missile arsenals.'], 2],
                    'ar' => ['وفقًا لنظرية "قلب العالم" (Heartland Theory) للجغرافي السياسي هالفورد ماكيندر، فإن السيطرة الاستراتيجية على العالم تتمحور حول:', ['السيطرة على السواحل البحرية والممرات الملاحية العالمية الكبرى.', 'السيطرة على المنطقة المحورية في قلب قارتي آسيا وأوروبا (Eurasian Heartland).', 'الهيمنة الاقتصادية على دول العالم النامي عبر الاستثمار المالي.', 'الامتلاك المطلق للترسانات الصاروخية العابرة للقارات.'], 2],
                ],
                [
                    'cat' => 'geography',
                    'en' => ["In urban geography, Walter Christaller's \"Central Place Theory\" primarily examines:", ['The spatial distribution patterns of human settlements and service centers based on threshold and range.', 'The structural causes of desertification and agricultural encroachment.', 'The civil engineering design of multi-lane metropolitan highway networks.', 'The geographic distribution of tropical climates and their impact on agrarian cycles.'], 1],
                    'ar' => ['في سياق الجغرافيا الحضرية، تدرس نظرية "الأماكن المركزية" (Central Place Theory) لوالتر كريستالر:', ['أنماط التوزيع المكاني للمستوطنات البشرية ومراكز تقديم الخدمات والسلع وفق مفهومي العتبة والمدى.', 'أسباب التصحر والزحف العمراني على الأراضي الزراعية.', 'التخطيط الهندسي لشبكات الطرق السريعة داخل المدن الكبرى.', 'التوزيع الجغرافي للمناخات المدارية وتأثيرها على الأنشطة الزراعية.'], 1],
                ],
                [
                    'cat' => 'geography',
                    'en' => ['In historical geographical thought, "Environmental Determinism" asserts that:', ['Human agency can fundamentally reshape physical environments to suit economic needs.', 'The physical environment and climate are the primary, governing forces dictating human behavior, culture, and societal evolution.', 'International cooperation is mandatory to mitigate global climate change.', 'Human economic activities operate completely independently of geographic constraints.'], 2],
                    'ar' => ['ماذا يُقصد بمفهوم "الحتمية البيئية" (Environmental Determinism) في الفكر الجغرافي التاريخي؟', ['قدرة الإنسان على تطويع الطبيعة وتغيير تضاريسها لتناسب احتياجاته الاقتصادية.', 'الاعتقاد بأن البيئة الطبيعية والمناخية هي العامل المحدِّد والمسيطر على سلوك المجتمعات البشرية وثقافتها وتطورها.', 'التعاون الدولي المشترك لمواجهة التغيرات المناخية والاحتباس الحراري.', 'استقلالية الأنشطة الاقتصادية البشرية تمامًا عن القيود الجغرافية.'], 2],
                ],
                [
                    'cat' => 'geography',
                    'en' => ['Analyzing satellite imagery and Geographic Information Systems (GIS) spatial data to assess environmental damage resulting from regional flooding represents an applied use of:', ['Descriptive historical geography.', 'Quantitative geography and applied spatial analysis.', 'Pure structural geomorphology.', 'Traditional demographic population geography.'], 2],
                    'ar' => ['يمثل تحليل صور الأقمار الصناعية وبيانات نظم المعلومات الجغرافية (GIS) لتقييم الأضرار البيئية الناتجة عن الفيضانات تطبيقًا عمليًا لـ:', ['الجغرافيا التاريخية الوصفية.', 'الجغرافيا الكمية والتحليل المكاني التطبيقي.', 'الجيومورفولوجيا البنيوية البحتة.', 'جغرافيا السكان الديموغرافية التقليدية.'], 2],
                ],

                // ---------------- History (7-12) ----------------
                [
                    'cat' => 'history',
                    'en' => ['The structural transformation of the international system following the end of World War II in 1945 resulted in:', ['The consolidation of a multipolar system led by traditional European empires.', 'The emergence of a bipolar system and the geopolitical/ideological division of the world into Western and Eastern blocs.', 'The absolute governance of regional conflicts by the League of Nations.', 'A complete return to isolationist foreign policies by the United States.'], 2],
                    'ar' => ['أدى التحول الهيكلي في النظام الدولي عقب انتهاء الحرب العالمية الثانية عام 1945 إلى:', ['ترسيخ نظام متعدد الأقطاب تتزعمه الإمبراطوريات الأوروبية التقليدية.', 'بروز نظام القطبية الثنائية وانقسام العالم سياسيًا وأيديولوجيًا بين المعسكرين الغربي والشرقي.', 'سيطرة عصبة الأمم بشكل مطلق على إدارة النزاعات الإقليمية.', 'العودة الكاملة للسياسات الانعزالية للولايات المتحدة الأمريكية.'], 2],
                ],
                [
                    'cat' => 'history',
                    'en' => ['What fundamental socioeconomic shift paved the way for the Industrial Revolution in England during the mid-18th century?', ['The collapse of the feudal system, accumulated commercial capital from colonial trade, and a mobile labor force generated by the enclosure movement.', 'A complete societal regression into subsistence farming and the abolition of foreign trade.', 'The early discovery and commercial use of nuclear energy sources.', 'The enforcement of a trade embargo on European ports by the Russian Empire.'], 1],
                    'ar' => ['ما العامل الاقتصادي والاجتماعي الأساسي الذي مهّد لظهور الثورة الصناعية في إنجلترا في منتصف القرن الثامن عشر؟', ['انهيار النظام الإقطاعي وتوافر رأس المال المتراكم من التجارة الاستعمارية، إلى جانب وفرة الأيدي العاملة نتيجة حركة تسوير الأراضي.', 'الاعتماد الكامل على الزراعة التقليدية وإلغاء التجارة الخارجية.', 'اكتشاف مصادر الطاقة النووية في وقت مبكر من القرن الثامن عشر.', 'فرض قيصر روسيا حصارًا تجاريًا على الموانئ الأوروبية.'], 1],
                ],
                [
                    'cat' => 'history',
                    'en' => ['The signing of the Treaty of Versailles (1919) after World War I marked a major turning point characterized by:', ['Establishing a just and sustainable framework that permanently prevented future European armed conflicts.', 'Imposing harsh punitive conditions and exorbitant financial reparations on Germany, creating a political and psychological environment that fueled the rise of Nazism.', 'Granting immediate and absolute independence to all Asian and African colonies.', 'The dissolution of all political borders between victorious and defeated European states.'], 2],
                    'ar' => ['يمثل توقيع "معاهدة فرساي" (1919) عقب الحرب العالمية الأولى نقطة تحول كبرى تميزت بـ:', ['تأسيس نظام عادل ومستدام منع قيام أي نزاعات مسلحة لاحقة في أوروبا.', 'فرض شروط قاسية وتعويضات مالية باهظة على ألمانيا، مما خلق بيئة سياسية ونفسية مهدت لظهور النازية.', 'منح الاستقلال التام والفوري لجميع المستعمرات الآسيوية والأفريقية.', 'إلغاء الحدود السياسية بين الدول الأوروبية المنتصرة والمهزومة.'], 2],
                ],
                [
                    'cat' => 'history',
                    'en' => ['When analyzing diplomatic historical documents, how do historians distinguish between a "primary source" and a "secondary source"?', ['A primary source is written by eyewitnesses or comprises official documents contemporary to the event, whereas a secondary source is a later analytical study using those records.', 'Secondary sources are invariably more objective because they are emotionally detached from the era.', 'Primary sources are restricted exclusively to modern academic textbooks.', 'No methodological difference exists between the two in modern historiography.'], 1],
                    'ar' => ['عند دراسة الوثائق التاريخية الدبلوماسية، كيف يفرق المؤرخون بين "المصدر الأساسي" (Primary Source) و"المصدر الثانوي" (Secondary Source)؟', ['المصدر الأساسي يكتبه شهود عيان أو يتمثل في وثائق رسمية معاصرة للحدث، بينما المصدر الثانوي دراسة تحليلية لاحقة تعتمد على تلك الوثائق.', 'المصدر الثانوي أكثر دقة وموضوعية دائمًا لعدم تأثره بانفعالات العصر.', 'المصدر الأساسي يقتصر على الكتب الدراسية الحديثة فقط.', 'لا يوجد فرق منهجي بين الاثنين في كتابة التاريخ الحديث.'], 1],
                ],
                [
                    'cat' => 'history',
                    'en' => ['The Great Depression, which began in the United States in 1929, affected the global landscape by causing:', ['A massive boom in international free trade and widespread wage increases across continents.', 'The contraction of global trade, the collapse of banking systems, and the subsequent rise of totalitarian and fascist regimes in Europe.', 'Major capitalist nations to voluntarily and permanently abandon market economies.', 'Absolute stability in foreign exchange markets without government intervention.'], 2],
                    'ar' => ['أثرت أزمة "الكساد الكبير" (The Great Depression) التي بدأت عام 1929 في الولايات المتحدة على المشهد العالمي من خلال:', ['انتعاش حركة التجارة الحرة الدولية وتضاعف الأجور في قارات العالم.', 'تراجع التجارة العالمية، وانهيار الأنظمة المصرفية، وصعود الأنظمة الشمولية والفاشية في أوروبا.', 'إعلان الدول الكبرى تخليها التام عن النظام الرأسمالي.', 'استقرار أسواق الصرف الأجنبي دون أي تدخل حكومي.'], 2],
                ],
                [
                    'cat' => 'history',
                    'en' => ['The historical significance of national liberation movements across Asia and Africa during the 1950s and 1960s lies in:', ['Redrawing the global geopolitical map, ending the era of traditional colonial empires, and giving birth to newly independent sovereign states.', 'The complete integration of these nations into legacy colonial military pacts.', 'The immediate end of all international migration toward European states.', 'The adoption of localized internal barter systems isolated from the global financial architecture.'], 1],
                    'ar' => ['تتمثل الأهمية التاريخية لحركات التحرر الوطني في آسيا وأفريقيا خلال خمسينيات القرن العشرين وستينياته في:', ['إعادة رسم الخريطة الجيوسياسية العالمية، وإنهاء عصر الإمبراطوريات الاستعمارية التقليدية، وظهور دول مستقلة جديدة.', 'اندماج هذه الدول كليًا ضمن التكتلات العسكرية الاستعمارية القديمة.', 'توقف الهجرة الدولية نحو الدول الأوروبية تمامًا.', 'اعتماد هذه الدول على اقتصاد مقايضة داخلي بعيدًا عن النظام المالي العالمي.'], 1],
                ],

                // ---------------- Psychology (13-18) ----------------
                [
                    'cat' => 'psychology',
                    'en' => ["According to Jean Piaget's theory of cognitive development, at what stage does a child develop the capacity for abstract thinking, formal logical deduction, and systematic hypothesis testing?", ['Sensorimotor stage', 'Preoperational stage', 'Concrete operational stage', 'Formal operational stage'], 4],
                    'ar' => ['وفقًا لنظرية النمو المعرفي لجان بياجيه (Jean Piaget)، في أي مرحلة يطور الطفل القدرة على التفكير المجرد والاستدلال المنطقي واختبار الفرضيات؟', ['مرحلة الحس-حركية (Sensorimotor Stage)', 'مرحلة ما قبل العمليات (Preoperational Stage)', 'مرحلة العمليات المادية المحسوسة (Concrete Operational Stage)', 'مرحلة العمليات الشكلية المجردة (Formal Operational Stage)'], 4],
                ],
                [
                    'cat' => 'psychology',
                    'en' => ['In psychodynamic theory, an individual using the defense mechanism of "rationalization" is engaging in:', ['Redirecting unacceptable impulses into socially noble and productive outlets.', 'Constructing seemingly logical, socially acceptable justifications for unacceptable behaviors or failures to protect self-esteem from guilt.', 'Denying the existence of painful external realities altogether.', 'Transferring aggressive feelings from the actual source of threat to a safer substitute target.'], 2],
                    'ar' => ['في نظرية التحليل النفسي، يُعرَّف استخدام الفرد لحيلة "التبرير" (Rationalization) الدفاعية بأنه:', ['إعادة توجيه الدوافع غير المقبولة نحو أهداف اجتماعية نبيلة.', 'إيجاد مبررات منطقية ومعقولة ظاهريًا لسلوكيات غير مقبولة أو فاشلة لحماية صورة الذات من الشعور بالذنب.', 'إنكار وجود الواقع المؤلم كليًا والتعامل معه كأن لم يكن.', 'نقل المشاعر العدوانية من مصدرها الأصلي إلى مصدر آخر أقل خطورة.'], 2],
                ],
                [
                    'cat' => 'psychology',
                    'en' => ['The Big Five personality model evaluates the dimension of "Conscientiousness" to measure an individual\'s degree of:', ['Openness to aesthetic and artistic experiences.', 'Interpersonal sociability and reward-seeking behavior.', 'Self-discipline, organization, dutifulness, and long-range planning.', 'Chronic emotional instability and stress reactivity.'], 3],
                    'ar' => ['يركز نموذج العوامل الخمسة الكبرى للشخصية (Big Five) على سمة "يقظة الضمير" (Conscientiousness) لتقييم مدى اتصاف الفرد بـ:', ['الانفتاح على التجارب الفنية والجمالية الجديدة.', 'الميل نحو التفاعل الاجتماعي المفرط والرغبة في الإثارة.', 'التنظيم، والانضباط الذاتي، والالتزام بالمسؤوليات، والتخطيط بعيد المدى.', 'القلق المستمر وسرعة الاستجابة الانفعالية السلبية.'], 3],
                ],
                [
                    'cat' => 'psychology',
                    'en' => ["In Ivan Pavlov's classical conditioning experiments, what does the term \"extinction\" denote?", ['The spontaneous recovery of a conditioned response after a prolonged rest period.', 'The gradual weakening and disappearance of the conditioned response when the conditioned stimulus (bell) is repeatedly presented without the unconditioned stimulus (food).', 'The generalization of a conditioned response to stimuli resembling the original trigger.', "The organism's ability to discriminate between distinct environmental cues."], 2],
                    'ar' => ['في تجارب الإشراط الكلاسيكي (Classical Conditioning) لإيفان بافلوف، ماذا يُقصد بعملية "الانطفاء" (Extinction)؟', ['ظهور الاستجابة الشرطية تلقائيًا بعد فترة راحة طويلة.', 'تلاشي الاستجابة الشرطية تدريجيًا عندما يتكرر ظهور المثير الشرطي (الجرس) مع غياب المثير الطبيعي (الطعام).', 'تعميم الاستجابة على مثيرات مشابهة للمثير الأصلي.', 'قدرة الكائن الحي على التمييز بين المثيرات المختلفة.'], 2],
                ],
                [
                    'cat' => 'psychology',
                    'en' => ["According to Lazarus and Folkman's cognitive appraisal model of stress, an individual's level of psychological distress is determined by:", ['The physical magnitude of the external event alone, independent of cognitive processing.', "Primary appraisal of the event's personal significance (is it threatening?), followed by secondary appraisal of coping resources and capabilities.", 'Automatic autonomic neural reflexes unaffected by conscious thought.', 'Genetic heritability exclusively.'], 2],
                    'ar' => ['وفقًا لنموذج التقييم المعرفي للضغوط النفسية (Cognitive Appraisal Model) للازاروس وفولكمان، يتحدد مستوى التوتر لدى الفرد بناءً على:', ['حجم الحدث الخارجي المادي فقط بغض النظر عن تفكير الفرد.', 'التقييم الأولي لأهمية الموقف (هل هو مهدِّد؟)، يعقبه التقييم الثانوي للقدرات والموارد المتاحة للتعامل معه.', 'الاستجابة العصبية اللاإرادية المحضة التي لا تخضع لأي معالجة ذهنية.', 'الوراثة البيولوجية وحدها.'], 2],
                ],
                [
                    'cat' => 'psychology',
                    'en' => ["In social psychology, what did Solomon Asch's classic experiments on conformity demonstrate?", ['Absolute individual independence in judgment regardless of group pressure.', 'The strong tendency for individuals to alter their opinions and behaviors to match a unanimous majority, even when that majority is clearly incorrect.', 'A biological predisposition toward direct aggression when working in large groups.', "The complete absence of social norms' impact on private decision-making."], 2],
                    'ar' => ['في علم النفس الاجتماعي، ماذا أثبتت تجارب سولومون آش (Solomon Asch) الشهيرة حول الامتثال (Conformity)؟', ['استقلالية الأفراد التامة في إصدار الأحكام مهما كانت الضغوط الجماعية.', 'ميل الأفراد إلى مطابقة آرائهم وسلوكياتهم مع إجماع أغلبية الجماعة حتى لو كانت رؤية الأغلبية خاطئة بوضوح.', 'ميل الأفراد نحو العدوان المباشر عند العمل في مجموعات كبيرة.', 'غياب تأثير المعايير الاجتماعية على قرارات الفرد الخاصة.'], 2],
                ],

                // ---------------- Philosophy (19-24) ----------------
                [
                    'cat' => 'philosophy',
                    'en' => ['John Locke and the British Empiricist school argued in epistemology that the human mind at birth is a:', ['Blank slate (tabula rasa), acquiring all knowledge exclusively through sensory experience and empirical observation.', 'Pre-structured rational entity possessing innate, a priori principles independent of experience.', 'Complex biological apparatus that deduces universal truths purely through formal logic.', 'Metaphysical substance entirely detached from material biology.'], 1],
                    'ar' => ['يرى الفيلسوف الإنجليزي جون لوك ورواد المذهب التجريبي (Empiricism) في نظرية المعرفة أن العقل البشري عند الولادة يكون بمثابة:', ['صفحة بيضاء (Tabula Rasa) تستمد كل معارفها حصريًا من التجربة الحسية والخبرة.', 'كيان عقلي مزوَّد بأفكار فطرية ومبادئ قبلية (A priori) سابقة على التجربة.', 'بنية بيولوجية معقدة تستنبط الحقائق الكلية عبر المنطق الصوري البحت.', 'كيان ميتافيزيقي منفصل تمامًا عن الحواس المادية.'], 1],
                ],
                [
                    'cat' => 'philosophy',
                    'en' => ["Immanuel Kant's deontological ethics holds that the moral worth of an action is determined by:", ['The utility, consequences, and net collective happiness generated by the outcome.', "The agent's pure intention and compliance with absolute rational duty (the categorical imperative), irrespective of material consequences.", 'The pursuit of personal pleasure and avoidance of physical pain.', 'Prevailing cultural norms and societal customs.'], 2],
                    'ar' => ['تقوم "أخلاق الواجب" (Deontological Ethics) عند إيمانويل كانط على أساس أن القيمة الأخلاقية للفعل تُقاس بـ:', ['النتائج النافعة والمصلحة العامة المترتبة على الفعل (المنفعة).', 'النية الخالصة والواجب العقلي المطلق المتمثل في "الأمر المطلق"، بغض النظر عن النتائج المادية.', 'السعي نحو تحقيق اللذة وتجنب الألم الشخصي.', 'الأعراف والتقاليد الاجتماعية السائدة في المجتمع.'], 2],
                ],
                [
                    'cat' => 'philosophy',
                    'en' => ['In social contract theory, how does Thomas Hobbes describe the pre-political state of nature in Leviathan?', ['A harmonious condition of universal peace, cooperation, and mutual benevolence.', 'A state of war of every man against every man, where life is solitary, poor, nasty, brutish, and short, necessitating an absolute sovereign for security.', 'An ideal direct democratic commune managed through egalitarian assemblies.', 'A tranquil environment devoid of any interpersonal friction.'], 2],
                    'ar' => ['في فلسفة العقد الاجتماعي، كيف يصف توماس هوبز في كتابه "اللوياثان" الحالة الطبيعية للبشر قبل قيام الدولة؟', ['حالة من السلام المطلق والتعاون والمحبة المتبادلة بين الأفراد.', 'حالة حرب الجميع ضد الجميع، تكون فيها الحياة موحشة وفقيرة ووحشية وقصيرة، مما يستدعي تفويض حاكم مطلق للحفاظ على الأمن.', 'حالة ديمقراطية مثالية تُدار عبر مجالس شعبية مباشرة.', 'حالة هادئة خالية من أي احتكاك بين الأفراد.'], 2],
                ],
                [
                    'cat' => 'philosophy',
                    'en' => ['What is the core methodological distinction between deductive and inductive reasoning in logic?', ['Deduction moves from general premises to a necessary, certain conclusion if the premises are valid, whereas induction moves from particular observations to probable generalizations.', 'Deduction relies entirely on laboratory experimentation, while induction relies on pure philosophical speculation.', 'There is no structural difference between the two in modern critical analysis.', 'Induction yields absolute logical certainty, while deduction produces mere probability.'], 1],
                    'ar' => ['ما الفرق الجوهري بين الاستدلال الاستنباطي (Deductive Reasoning) والاستدلال الاستقرائي (Inductive Reasoning) في المنطق؟', ['الاستنباط يبدأ من مقدمات عامة ليصل إلى نتيجة يقينية إذا صحت المقدمات، بينما الاستقراء يبدأ بملاحظات جزئية ليصل إلى تعميمات محتملة الصدق.', 'الاستنباط يعتمد كليًا على المشاهدة المعملية، بينما الاستقراء يعتمد على التأمل الفلسفي المجرد.', 'لا يوجد أي فرق منهجي بينهما في التفكير النقدي المعاصر.', 'الاستقراء يقدم نتائج يقينية قطعية، بينما الاستنباط يقدم احتمالات ظنية.'], 1],
                ],
                [
                    'cat' => 'philosophy',
                    'en' => ['Existentialism, as articulated by Jean-Paul Sartre, centers on the foundational premise that:', ['Existence precedes essence; humans are born without a predefined nature and are radically free and wholly responsible for defining their own identity.', 'Human essence is strictly predetermined by biological and genetic architecture.', 'Nature governs human destiny deterministically, leaving no room for authentic agency.', 'Moral values are objective, eternal absolutes independent of human consciousness.'], 1],
                    'ar' => ['يركز التيار الوجودي (Existentialism)، ممثلًا في جان بول سارتر، على المبدأ القائل بأن:', ['الوجود يسبق الماهية؛ فالإنسان يولد بلا ماهية مسبقة، وهو حر حرية جذرية ومسؤول وحده عن صنع هويته وخياراته.', 'ماهية الإنسان محددة سلفًا بخصائصه البيولوجية والوراثية.', 'الطبيعة تحكم مصير الإنسان بشكل حتمي ولا مجال للحرية الفردية.', 'القيم الأخلاقية ثابتة ومطلقة ومستقلة عن الوعي الإنساني.'], 1],
                ],
                [
                    'cat' => 'philosophy',
                    'en' => ['Philosopher Thomas Kuhn argued that scientific advancement does not occur through smooth, cumulative progression, but rather via:', ['Scientific revolutions involving shifts from one established paradigm to a new, incompatible paradigm following systemic anomalies.', 'Literal empirical verification of isolated micro-hypotheses without overturning macro-theories.', 'Pure mathematical derivation divorced from empirical observation.', 'Dogmatic adherence to classical frameworks despite contradictory data.'], 1],
                    'ar' => ['يرى الفيلسوف توماس كون (Thomas Kuhn) أن تطور العلوم لا يتم بطريقة تراكمية خطية هادئة، بل عبر:', ['ثورات علمية (Scientific Revolutions) تتضمن الانتقال من نموذج إرشادي (Paradigm) قديم إلى نموذج جديد كليًا بعد أزمات معرفية.', 'التحقق التجريبي الحرفي من كل فرضية جزئية دون تغيير في النظريات الكبرى.', 'الاستنباط الرياضي البحت بمعزل عن التجارب والملاحظات الواقعية.', 'التمسك المطلق بالأطر التقليدية رغم تناقضها مع البيانات الجديدة.'], 1],
                ],

                // ---------------- Sociology (25-30) ----------------
                [
                    'cat' => 'sociology',
                    'en' => ['According to Structural Functionalism (associated with Émile Durkheim and Talcott Parsons), society is conceptualized as:', ['An arena of constant, irreconcilable conflict between social classes over the means of production.', 'A complex systemic whole whose interrelated institutions function collectively to maintain social order, stability, and equilibrium.', 'A random aggregation of isolated individuals whose actions bear no systemic interdependence.', 'The mere product of linguistic symbols and subjective micro-interactions.'], 2],
                    'ar' => ['وفقًا للنظرية البنائية الوظيفية (Structural Functionalism) لإميل دوركايم وتالكوت بارسونز، يُنظر إلى المجتمع على أنه:', ['ساحة صراع مستمر بين الطبقات الاجتماعية المالكة لأدوات الإنتاج والطبقات المستغَلة.', 'نسق اجتماعي متكامل تتساند أجزاؤه ومؤسساته وتتفاعل لضمان استقرار البناء الاجتماعي وحفظ توازنه.', 'مجرد تجمع عشوائي للأفراد لا علاقة بين أفعالهم الفردية.', 'مجرد ناتج للرموز اللغوية والمعاني المتبادلة في التفاعل اليومي.'], 2],
                ],
                [
                    'cat' => 'sociology',
                    'en' => ["How does Karl Marx's conflict theory explain historical and social change?", ['Change is driven by the evolution of philosophical ideas and moral values across epochs.', 'Change is propelled by ongoing class struggle over ownership of the means of production and the distribution of wealth between exploiters and the exploited.', 'Change occurs spontaneously through total consensus and voluntary harmony among all members.', 'Change is driven exclusively by climatological and biological shifts.'], 2],
                    'ar' => ['كيف يفسر كارل ماركس في "نظرية الصراع" (Conflict Theory) التغير الاجتماعي والتاريخي؟', ['التغير ناتج عن تطور الأفكار الفلسفية والأخلاقية للمجتمعات عبر الزمن.', 'التغير يدفعه الصراع الطبقي المستمر حول السيطرة على وسائل الإنتاج وتوزيع الثروة بين المستغِلين والمستغَلين.', 'التغير يحدث تلقائيًا بالرضا التام والتوافق بين جميع أفراد المجتمع.', 'التغير مرتبط حصرًا بالتطورات المناخية والبيولوجية.'], 2],
                ],
                [
                    'cat' => 'sociology',
                    'en' => ['Symbolic Interactionism as a sociological perspective primarily focuses on:', ['Macro-level economic structures and national fiscal budgets.', 'The subjective meanings, symbols, and interpretations that individuals construct and exchange in face-to-face daily interactions.', 'Demographic laws governing population growth and migration.', 'The biological physiology of human communities.'], 2],
                    'ar' => ['يهتم اتجاه "التفاعلية الرمزية" (Symbolic Interactionism) في علم الاجتماع بدراسة:', ['الهياكل الاقتصادية الكبرى وموازنات الدول المالية.', 'المعاني والرموز والتفسيرات الذاتية التي يضفيها الأفراد على تفاعلاتهم اليومية وجهًا لوجه.', 'القوانين الديموغرافية لنمو السكان وهجرتهم.', 'الوظائف البيولوجية لأعضاء المجتمع البشري.'], 2],
                ],
                [
                    'cat' => 'sociology',
                    'en' => ['What distinguishes secondary socialization from primary socialization?', ['Socialization imparted exclusively by the family and primary caregivers during early childhood at home.', 'Later socialization experiences occurring outside the family through external institutional agents (schools, peer groups, mass media, and workplaces).', 'Complete social isolation and self-reflection.', 'Prenatal biological and genetic transmission.'], 2],
                    'ar' => ['ماذا يُقصد بمفهوم "التنشئة الاجتماعية الثانوية" (Secondary Socialization) مقارنة بالتنشئة الأولية؟', ['التنشئة التي تقدمها الأسرة ومقدمو الرعاية الأوائل للطفل في المنزل خلال سنواته الأولى.', 'العمليات اللاحقة التي يتعرض لها الفرد خارج الأسرة عبر المؤسسات المجتمعية (المدرسة، والأصدقاء، ووسائل الإعلام، ومكان العمل).', 'الانعزال التام عن المجتمع والتفاعل مع الذات فقط.', 'العمليات البيولوجية الوراثية قبل الولادة.'], 2],
                ],
                [
                    'cat' => 'sociology',
                    'en' => ["According to Robert Merton's strain theory (anomie) of deviant behavior, deviance occurs when:", ['Cultural goals and institutionalized means are perfectly aligned for all citizens.', 'There is a structural gap between culturally approved aspirations (e.g., financial success) and unequal access to the legitimate institutional means of achieving them.', 'Societies completely abstain from codifying any laws or behavioral norms.', 'Individuals are thoroughly trained to respect moral traditions.'], 2],
                    'ar' => ['وفقًا لنظرية "الأنوميا" (اللامعيارية / Anomie) لروبرت ميرتون المفسرة للانحراف الاجتماعي، ينشأ السلوك المنحرف عندما:', ['تتطابق الأهداف الثقافية للمجتمع كليًا مع الوسائل المشروعة المتاحة لجميع الأفراد.', 'تحدث فجوة هيكلية بين الأهداف الثقافية المرغوبة (كالثراء والنجاح) وعدم توافر الوسائل المشروعة لتحقيقها أمام بعض الفئات.', 'يمتنع المجتمع عن وضع أي قوانين أو قواعد تنظيمية للسلوك.', 'يتدرب الأفراد جيدًا على الالتزام بالقواعد الأخلاقية والتقاليد.'], 2],
                ],
                [
                    'cat' => 'sociology',
                    'en' => ['In his seminal Muqaddimah, Ibn Khaldun established the science of human civilization (Ilm al-Umran), arguing that it studies:', ['The laws governing the rise, prosperity, cyclical decline, and fall of civilizations, alongside the dynamics of group solidarity (Asabiyyah), nomadism, and sedentary urbanism.', 'Mythological folklore and fictional battle stories exclusively.', 'Astronomical calculations and planetary motion independent of human affairs.', 'Pure natural sciences, medicine, and chemistry.'], 1],
                    'ar' => ['يرى ابن خلدون في مقدمته الشهيرة أن "علم العمران البشري" (علم الاجتماع) يدرس:', ['الظواهر الاجتماعية والعمرانية في طبيعتها وذاتها، باحثًا في قوانين قيام الدول وازدهارها ثم سقوطها، وفي عوامل العصبية والبداوة والحضارة.', 'الأساطير والخرافات القديمة والقصص الحربية الخيالية فحسب.', 'الحسابات الفلكية وحركة الكواكب بعيدًا عن أحوال البشر.', 'العلوم الطبيعية البحتة والطب والكيمياء فقط.'], 1],
                ],
            ],
        ];
    }
}
