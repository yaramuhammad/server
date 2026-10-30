<?php

namespace Database\Seeders\StudentAbilities;

/**
 * Cross-Cultural Readiness Battery for Studying Abroad / بطارية مقاييس التكيف الثقافي والقيمي.
 *
 * Source: "Cross-Cultural Readiness Battery for Studying Abroad.pdf" + "بطارية مقاييس التكيف الثقافي والقيمي.pdf".
 * 16 positively-worded self-report items on a 1-5 Likert scale, 4 subscales of 4 items each.
 * The Arabic source lists items 15 and 16 in swapped order; here both follow the English order
 * (both items belong to subscale 4, so scoring is unaffected).
 *
 * Scoring (from the source): total = sum of 16 items (16-80) →
 *   68-80 High readiness · 52-67 Moderate readiness · below 52 Low readiness.
 * Subscales (4-20 each) use the same cutoffs scaled to percent of max (85% / 65%).
 */
class CrossCulturalReadinessBattery
{
    public static function definition(): array
    {
        $high = [
            'en' => 'High Readiness',
            'ar' => 'جاهزية عالية',
        ];
        $moderate = [
            'en' => 'Moderate Readiness',
            'ar' => 'جاهزية متوسطة',
        ];
        $low = [
            'en' => 'Low Readiness',
            'ar' => 'جاهزية منخفضة',
        ];

        return [
            'title' => [
                'en' => 'Cross-Cultural Readiness Battery for Studying Abroad',
                'ar' => 'بطارية مقاييس التكيف الثقافي والقيمي',
            ],
            'description' => [
                'en' => "A 16-item self-report battery assessing a student's psychological, social and cultural readiness to study abroad across four subscales: cultural intelligence, autonomy & self-reliance, psychological resilience & culture-shock adaptation, and core values & identity integrity.",
                'ar' => 'بطارية تقرير ذاتي من 16 عبارة تقيس مدى جاهزية الطالب نفسيًا واجتماعيًا وثقافيًا للاغتراب والتكيف مع البيئات الأكاديمية والمجتمعية الجديدة، عبر أربعة مقاييس فرعية: الذكاء الثقافي، والاستقلالية والاعتماد على الذات، والمرونة النفسية والتكيف مع الصدمة الثقافية، والتكيف القيمي والصمود الهوياتي.',
            ],
            'instructions' => [
                'en' => 'Read each statement and indicate how well it describes you: 1 = Does not apply to me at all, 2 = Rarely applies to me, 3 = Sometimes applies to me, 4 = Often applies to me, 5 = Fully applies to me. There are no right or wrong answers; answer honestly.',
                'ar' => 'اقرأ كل عبارة وحدد مدى انطباقها عليك: 1 = لا تنطبق عليّ إطلاقًا، 2 = تنطبق عليّ نادرًا، 3 = تنطبق عليّ أحيانًا، 4 = تنطبق عليّ غالبًا، 5 = تنطبق عليّ تمامًا. لا توجد إجابات صحيحة أو خاطئة؛ أجب بصدق.',
            ],
            'chart' => 'radar',
            'likert' => [
                '1' => ['en' => 'Does not apply to me at all', 'ar' => 'لا تنطبق عليّ إطلاقًا'],
                '2' => ['en' => 'Rarely applies to me', 'ar' => 'تنطبق عليّ نادرًا'],
                '3' => ['en' => 'Sometimes applies to me', 'ar' => 'تنطبق عليّ أحيانًا'],
                '4' => ['en' => 'Often applies to me', 'ar' => 'تنطبق عليّ غالبًا'],
                '5' => ['en' => 'Fully applies to me', 'ar' => 'تنطبق عليّ تمامًا'],
            ],
            // Subscale bands: the source's 68/80 and 52/80 cutoffs expressed as percent of max.
            'interpretation' => [
                ['min' => 0, 'max' => 64.99, 'label' => $low],
                ['min' => 65, 'max' => 84.99, 'label' => $moderate],
                ['min' => 85, 'max' => 100, 'label' => $high],
            ],
            // Overall readiness index on the raw total (16-80), exactly as in the source.
            'overall' => [
                'use_percentage' => false,
                'ranges' => [
                    [
                        'min' => 0, 'max' => 51, 'label' => $low,
                        'description' => [
                            'en' => 'Development needed: the student may experience significant distress, severe culture shock, or academic decline due to underdeveloped autonomy. A local foundation year or a dual-degree domestic pathway is recommended before travel, with training in adaptation skills.',
                            'ar' => 'يحتاج إلى تطوير: قد يتعرض الطالب لضغوط شديدة أو صدمة ثقافية حادة أو تراجع أكاديمي بسبب ضعف الاستقلالية. يوصى بتأجيل الاغتراب المباشر أو الالتحاق ببرنامج جامعي مدمج (سنة داخل الوطن ثم السفر) مع التدريب على مهارات التكيف.',
                        ],
                    ],
                    [
                        'min' => 52, 'max' => 67, 'label' => $moderate,
                        'description' => [
                            'en' => 'Conditional readiness: the student shows adequate foundational skills but would benefit from a targeted Pre-Departure Orientation (PDO) focusing on practical self-reliance, cultural intelligence and culture-shock management.',
                            'ar' => 'جاهزية مشروطة: يمتلك الطالب مهارات أساسية كافية، لكنه يحتاج إلى برامج توجيهية سابقة للسفر (Pre-departure Orientation) تركز على مهارات إدارة الذات والذكاء الثقافي والتعامل مع الصدمة الثقافية.',
                        ],
                    ],
                    [
                        'min' => 68, 'max' => 80, 'label' => $high,
                        'description' => [
                            'en' => 'Optimal readiness: the student has strong autonomy, psychological resilience and cultural flexibility, and is well prepared for immediate international academic placement.',
                            'ar' => 'جاهزية مثلى: يمتلك الطالب مرونة واستقلالية عالية، ومؤهل تمامًا للدراسة في الخارج مع تكيف سريع ومستقل.',
                        ],
                    ],
                ],
            ],
            'categories' => [
                'cultural_intelligence' => ['en' => 'Cultural Intelligence (CQS)', 'ar' => 'الذكاء الثقافي (CQS)'],
                'autonomy' => ['en' => 'Autonomy & Self-Reliance', 'ar' => 'الاستقلالية والاعتماد على الذات'],
                'resilience' => ['en' => 'Psychological Resilience & Shock Adaptation', 'ar' => 'المرونة النفسية والتكيف مع الصدمة الثقافية'],
                'values_identity' => ['en' => 'Core Values & Identity Integrity', 'ar' => 'التكيف القيمي والصمود الهوياتي'],
            ],
            'questions' => [
                // ---------------- Subscale 1: Cultural Intelligence (1-4) ----------------
                ['cat' => 'cultural_intelligence', 'text' => [
                    'en' => 'I enjoy interacting with people from cultural, ethnic, and religious backgrounds that differ significantly from my own.',
                    'ar' => 'أجد متعة وشغفًا حقيقيًا في التفاعل مع أشخاص ينتمون لخلفيات ثقافية ودينية مختلفة عن ثقافتي.',
                ]],
                ['cat' => 'cultural_intelligence', 'text' => [
                    'en' => 'I possess a working knowledge of the social norms, legal systems, and cultural etiquette of the country where I plan to study.',
                    'ar' => 'أمتلك وعيًا كافيًا بالأنظمة الاجتماعية والأعراف والقيم السائدة في الدولة التي أخطط للدراسة فيها.',
                ]],
                ['cat' => 'cultural_intelligence', 'text' => [
                    'en' => 'I continuously re-evaluate my cultural assumptions and stereotypes when engaging with individuals from other backgrounds.',
                    'ar' => 'أراجع باستمرار افتراضاتي وتقييماتي المسبقة حول الشعوب والبيئات المختلفة أثناء تعاملي المباشر معهم.',
                ]],
                ['cat' => 'cultural_intelligence', 'text' => [
                    'en' => 'I can comfortably adjust my tone of voice, body language, and non-verbal communication to fit different cultural expectations.',
                    'ar' => 'أستطيع تعديل نبرة صوتي ولغة جسدي وأسلوب تعبيري بسهولة ليتناسب مع البيئة الثقافية للبلد المضيف.',
                ]],

                // ---------------- Subscale 2: Autonomy & Self-Reliance (5-8) ----------------
                ['cat' => 'autonomy', 'text' => [
                    'en' => 'I can manage my personal monthly budget, meal preparation, and domestic chores without requiring external supervision.',
                    'ar' => 'أستطيع إدارة ميزانيتي المالية الشخصية والطهي ورعاية احتياجاتي اليومية بفاعلية دون توجيه مستمر من أسرتي.',
                ]],
                ['cat' => 'autonomy', 'text' => [
                    'en' => 'When faced with housing, administrative, or bureaucratic challenges, I proactively seek out solutions and contact official channels on my own initiative.',
                    'ar' => 'عندما أواجه معضلة إدارية أو سكنية، أبادر بالبحث عن حلول عملية والتواصل مع الجهات المعنية بمفردي.',
                ]],
                ['cat' => 'autonomy', 'text' => [
                    'en' => 'I discipline myself to maintain an organized academic and personal schedule while balancing competing deadlines.',
                    'ar' => 'أمتلك القدرة على تنظيم جدولي الأكاديمي والحياتي والالتزام بالمواعيد دون حاجة إلى رقابة خارجية.',
                ]],
                ['cat' => 'autonomy', 'text' => [
                    'en' => 'I accept full personal responsibility for my decisions, academic outcomes, and daily behaviors.',
                    'ar' => 'أتحمل المسؤولية الكاملة عن قراراتي الأكاديمية والشخصية ونشاطاتي اليومية ونتائجها.',
                ]],

                // ---------------- Subscale 3: Psychological Resilience & Shock Adaptation (9-12) ----------------
                ['cat' => 'resilience', 'text' => [
                    'en' => 'I can tolerate initial feelings of disorientation or unfamiliarity in a new country without becoming overwhelmed or despondent.',
                    'ar' => 'أستطيع التعامل مع شعور الاختلاف أو عدم الارتياح المبدئي في البيئة الجديدة دون أن يصيبني ذلك بالإحباط.',
                ]],
                ['cat' => 'resilience', 'text' => [
                    'en' => 'When experiencing longing for home or emotional isolation, I use constructive coping strategies to re-engage with my environment.',
                    'ar' => 'عندما أشعر بالحنين إلى الوطن (Homesickness) أو بالوحدة، أستخدم استراتيجيات تكيف إيجابية لتجاوزها بسرعة.',
                ]],
                ['cat' => 'resilience', 'text' => [
                    'en' => 'I adapt easily to new educational paradigms that require self-directed learning, group projects, and independent research.',
                    'ar' => 'أتأقلم بسرعة مع تغير ظروف المعيشة والطقس وأنماط الدراسة القائمة على التعلم الذاتي والتكليفات المركبة.',
                ]],
                ['cat' => 'resilience', 'text' => [
                    'en' => 'I am willing to use university counseling services or academic support centers when experiencing significant stress.',
                    'ar' => 'لا أتردد في طلب الدعم النفسي أو الأكاديمي من المراكز المختصة بالجامعة عند الشعور بالضغط الشديد.',
                ]],

                // ---------------- Subscale 4: Core Values & Identity Integrity (13-16) ----------------
                ['cat' => 'values_identity', 'text' => [
                    'en' => 'I maintain a clear sense of my personal values and cultural heritage, which serves as an internal compass.',
                    'ar' => 'أعتز بهويتي وثقافتي وقيمي الأساسية، وأمتلك مرجعية أخلاقية واضحة تحميني من الانجراف.',
                ]],
                ['cat' => 'values_identity', 'text' => [
                    'en' => 'I respect and peacefully coexist with lifestyles and viewpoints that differ from my own without becoming confrontational.',
                    'ar' => 'أستطيع الاحترام والتعايش السلمي مع الآراء وأنماط المعيشة المخالفة لقيمي دون تبنيها أو الصدام مع أصحابها.',
                ]],
                ['cat' => 'values_identity', 'text' => [
                    'en' => 'I am capable of saying "no" and standing firm in my personal principles when faced with peer pressure to engage in behaviors I disapprove of.',
                    'ar' => 'أمتلك القدرة على قول "لا" والثبات على موقفي عندما تُعرض عليّ سلوكيات تتعارض مع قيم أخلاقية مقتنع بها.',
                ]],
                ['cat' => 'values_identity', 'text' => [
                    'en' => 'I view studying abroad as an opportunity to positively represent my culture and engage in constructive intercultural dialogue.',
                    'ar' => 'أرى في تجربة الاغتراب فرصة لتقديم صورة إيجابية ومشرّفة عن مجتمعي وثقافتي الأصلية.',
                ]],
            ],
        ];
    }
}
