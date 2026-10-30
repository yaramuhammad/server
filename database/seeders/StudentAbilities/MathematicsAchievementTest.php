<?php

namespace Database\Seeders\StudentAbilities;

/**
 * Standardized Mathematics Achievement Test / اختبار التحصيل القياسي في الرياضيات.
 *
 * Source: "Standardized Mathematics Achievement Test.pdf" + "اختبار التحصيل القياسي في الرياضيات.pdf".
 * Formulas are written in plain Unicode because question text renders as plain text.
 * Matrices are written row by row: [[a, b], [c, d]].
 */
class MathematicsAchievementTest
{
    public static function definition(): array
    {
        return [
            'title' => [
                'en' => 'Standardized Mathematics Achievement Test',
                'ar' => 'اختبار التحصيل القياسي في الرياضيات',
            ],
            'description' => [
                'en' => 'A 30-item achievement test covering four mathematics domains: algebra & functions, calculus, analytic & solid geometry, and statistics & probability.',
                'ar' => 'اختبار تحصيلي من 30 سؤالًا يغطي أربعة محاور في الرياضيات: الجبر والدوال، والتفاضل والتكامل، والهندسة التحليلية والفضائية، والإحصاء والاحتمالات.',
            ],
            'instructions' => [
                'en' => 'Each question has four options and only one correct answer. You may use scratch paper for calculations. Each correct answer earns one point; there is no penalty for wrong answers.',
                'ar' => 'لكل سؤال أربعة بدائل وإجابة صحيحة واحدة فقط. يمكنك استخدام ورقة خارجية لإجراء الحسابات. تُحتسب درجة واحدة لكل إجابة صحيحة، ولا يُخصم شيء على الإجابة الخاطئة.',
            ],
            'chart' => 'column',
            'categories' => [
                'algebra' => ['en' => 'Algebra & Functions', 'ar' => 'الجبر والدوال'],
                'calculus' => ['en' => 'Calculus', 'ar' => 'التفاضل والتكامل'],
                'geometry' => ['en' => 'Analytic & Solid Geometry', 'ar' => 'الهندسة التحليلية والفضائية'],
                'statistics' => ['en' => 'Statistics & Probability', 'ar' => 'الإحصاء والاحتمالات'],
            ],
            'questions' => [
                // ---------------- Algebra & Functions (1-8) ----------------
                [
                    'cat' => 'algebra',
                    'en' => ['Given f(x) = log₂(x² − 4), what is the domain of the function f(x)?', ['(−∞, −2) ∪ (2, ∞)', '[−2, 2]', '(2, ∞)', '(−∞, ∞)'], 1],
                    'ar' => ['إذا كانت f(x) = log₂(x² − 4)، فما مجال الدالة f(x)؟', ['(−∞, −2) ∪ (2, ∞)', '[−2, 2]', '(2, ∞)', '(−∞, ∞)'], 1],
                ],
                [
                    'cat' => 'algebra',
                    'en' => ['What is the sum of the solutions to the exponential equation 4ˣ − 5·2ˣ + 4 = 0?', ['0', '2', '3', '5'], 2],
                    'ar' => ['ما مجموع حلول المعادلة الأسية التالية: 4ˣ − 5·2ˣ + 4 = 0؟', ['0', '2', '3', '5'], 2],
                ],
                [
                    'cat' => 'algebra',
                    'en' => ['If A is a 2×2 matrix with determinant |A| = 5, what is the determinant of the matrix 3A (i.e., |3A|)?', ['15', '30', '45', '135'], 3],
                    'ar' => ['إذا كانت A مصفوفة من الرتبة 2×2 ومحددها |A| = 5، فما قيمة محدد المصفوفة 3A (أي |3A|)؟', ['15', '30', '45', '135'], 3],
                ],
                [
                    'cat' => 'algebra',
                    'en' => ['What is the multiplicative inverse (A⁻¹) of the matrix A = [[2, 1], [5, 3]]?', ['[[3, −1], [−5, 2]]', '[[−3, 1], [5, −2]]', '[[2, −1], [−5, 3]]', '[[3, 1], [5, 2]]'], 1],
                    'ar' => ['ما المصفوفة المعكوسة ضربيًا (A⁻¹) للمصفوفة A = [[2, 1], [5, 3]]؟', ['[[3, −1], [−5, 2]]', '[[−3, 1], [5, −2]]', '[[2, −1], [−5, 3]]', '[[3, 1], [5, 2]]'], 1],
                ],
                [
                    'cat' => 'algebra',
                    'en' => ['An arithmetic progression has a first term a₁ = 3 and a common difference d = 4. What is the sum of its first 20 terms (S₂₀)?', ['780', '820', '800', '840'], 2],
                    'ar' => ['متتابعة حسابية حدها الأول a₁ = 3 وأساسها d = 4. ما مجموع أول 20 حدًا منها (S₂₀)؟', ['780', '820', '800', '840'], 2],
                ],
                [
                    'cat' => 'algebra',
                    'en' => ['What is the coefficient of the x³ term in the binomial expansion of (2x − 1)⁵?', ['80', '−80', '40', '−40'], 1],
                    'ar' => ['ما معامل الحد الذي يحتوي على x³ في مفكوك ذات الحدين (2x − 1)⁵؟', ['80', '−80', '40', '−40'], 1],
                ],
                [
                    'cat' => 'algebra',
                    'en' => ['If a system of homogeneous linear equations AX = 0 has infinitely many non-trivial solutions, the determinant of the coefficient matrix |A| must be equal to:', ['1', '0', '−1', 'Undefined'], 2],
                    'ar' => ['إذا كان لنظام معادلات خطية متجانسة AX = 0 عدد لا نهائي من الحلول غير الصفرية، فإن محدد مصفوفة المعاملات |A| يجب أن يساوي:', ['1', '0', '−1', 'قيمة غير محددة'], 2],
                ],
                [
                    'cat' => 'algebra',
                    'en' => ['If one root of the quadratic equation x² − 4x + 13 = 0 is x₁ = 2 + 3i, what is the other complex root?', ['−2 + 3i', '2 − 3i', '−2 − 3i', '3 − 2i'], 2],
                    'ar' => ['إذا كان أحد جذري المعادلة التربيعية x² − 4x + 13 = 0 هو x₁ = 2 + 3i، فما الجذر المركب الآخر؟', ['−2 + 3i', '2 − 3i', '−2 − 3i', '3 − 2i'], 2],
                ],

                // ---------------- Calculus (9-18) ----------------
                [
                    'cat' => 'calculus',
                    'en' => ['Evaluate the limit: lim (x→0) of sin(5x) / (3x)', ['0', '1', '5/3', '3/5'], 3],
                    'ar' => ['احسب قيمة النهاية التالية: lim (x→0) لـ sin(5x) / (3x)', ['0', '1', '5/3', '3/5'], 3],
                ],
                [
                    'cat' => 'calculus',
                    'en' => ['What is the derivative of the function f(x) = e^(x²) · ln(x) with respect to x?', ['2x·e^(x²)·ln(x) + e^(x²)/x', 'e^(x²)·(2x + 1/x)', '2x·e^(x²) / x', 'e^(x²)·ln(x)'], 1],
                    'ar' => ['ما مشتقة الدالة f(x) = e^(x²) · ln(x) بالنسبة لـ x؟', ['2x·e^(x²)·ln(x) + e^(x²)/x', 'e^(x²)·(2x + 1/x)', '2x·e^(x²) / x', 'e^(x²)·ln(x)'], 1],
                ],
                [
                    'cat' => 'calculus',
                    'en' => ['What is the slope of the tangent line to the curve y = x³ − 3x + 2 at the point where x = 2?', ['6', '9', '12', '3'], 2],
                    'ar' => ['ما ميل المماس للمنحنى y = x³ − 3x + 2 عند النقطة التي عندها x = 2؟', ['6', '9', '12', '3'], 2],
                ],
                [
                    'cat' => 'calculus',
                    'en' => ['The position function of a moving particle is s(t) = t³ − 6t² + 9t. At what time t is the acceleration of the particle equal to zero?', ['t = 1', 't = 2', 't = 3', 't = 0'], 2],
                    'ar' => ['دالة الموضع لجسيم متحرك هي s(t) = t³ − 6t² + 9t. متى تكون عجلة (تسارع) الجسيم مساوية للصفر؟', ['t = 1', 't = 2', 't = 3', 't = 0'], 2],
                ],
                [
                    'cat' => 'calculus',
                    'en' => ['For the function f(x) = x³ − 12x, the critical point at x = −2 represents a:', ['Local minimum', 'Local maximum', 'Inflection point', 'Point of discontinuity'], 2],
                    'ar' => ['النقطة الحرجة للدالة f(x) = x³ − 12x عند x = −2 تمثل:', ['نهاية صغرى محلية', 'نهاية عظمى محلية', 'نقطة انقلاب فقط', 'نقطة عدم اتصال'], 2],
                ],
                [
                    'cat' => 'calculus',
                    'en' => ['Evaluate the definite integral ∫₀³ (3x² − 2x + 1) dx', ['21', '24', '27', '18'], 1],
                    'ar' => ['احسب قيمة التكامل المحدود: ∫₀³ (3x² − 2x + 1) dx', ['21', '24', '27', '18'], 1],
                ],
                [
                    'cat' => 'calculus',
                    'en' => ['What is the indefinite integral ∫ 2x / (x² + 1) dx?', ['ln(x² + 1) + C', '1 / (x² + 1) + C', 'x²·ln(x) + C', '2·ln(x² + 1) + C'], 1],
                    'ar' => ['ما حل التكامل غير المحدود ∫ 2x / (x² + 1) dx؟', ['ln(x² + 1) + C', '1 / (x² + 1) + C', 'x²·ln(x) + C', '2·ln(x² + 1) + C'], 1],
                ],
                [
                    'cat' => 'calculus',
                    'en' => ['The area bounded by the curve y = x² and the x-axis over the interval [0, 2] is equal to:', ['8/3', '4', '4/3', '2'], 1],
                    'ar' => ['المساحة المحصورة بين منحنى الدالة y = x² ومحور السينات في الفترة [0, 2] تساوي:', ['8/3', '4', '4/3', '2'], 1],
                ],
                [
                    'cat' => 'calculus',
                    'en' => ["If the second derivative of a function is f''(x) = 6x − 4, with initial conditions f'(0) = 2 and f(0) = 5, what is f(x)?", ['f(x) = x³ − 2x² + 2x + 5', 'f(x) = 3x³ − 4x² + 2x + 5', 'f(x) = x³ − 2x² + 5', 'f(x) = 6x³ − 4x² + 2x + 5'], 1],
                    'ar' => ["إذا كانت المشتقة الثانية لدالة هي f''(x) = 6x − 4، وكان f'(0) = 2 و f(0) = 5، فما قاعدة الدالة f(x)؟", ['f(x) = x³ − 2x² + 2x + 5', 'f(x) = 3x³ − 4x² + 2x + 5', 'f(x) = x³ − 2x² + 5', 'f(x) = 6x³ − 4x² + 2x + 5'], 1],
                ],
                [
                    'cat' => 'calculus',
                    'en' => ['Evaluate the integral ∫ e^(3x) dx', ['3e^(3x) + C', '(1/3)·e^(3x) + C', 'e^(3x) + C', '(1/3)·eˣ + C'], 2],
                    'ar' => ['احسب قيمة التكامل ∫ e^(3x) dx', ['3e^(3x) + C', '(1/3)·e^(3x) + C', 'e^(3x) + C', '(1/3)·eˣ + C'], 2],
                ],

                // ---------------- Analytic & Solid Geometry (19-24) ----------------
                [
                    'cat' => 'geometry',
                    'en' => ['What is the equation of the line perpendicular to y = 2x + 3 and passing through the point (0, 4)?', ['y = −(1/2)x + 4', 'y = −2x + 4', 'y = (1/2)x + 4', 'y = 2x + 4'], 1],
                    'ar' => ['ما معادلة الخط المستقيم العمودي على المستقيم y = 2x + 3 والمار بالنقطة (0, 4)؟', ['y = −(1/2)x + 4', 'y = −2x + 4', 'y = (1/2)x + 4', 'y = 2x + 4'], 1],
                ],
                [
                    'cat' => 'geometry',
                    'en' => ['What are the center and radius of the circle x² + y² − 6x + 8y = 0?', ['Center (3, −4), radius r = 5', 'Center (−3, 4), radius r = 25', 'Center (6, −8), radius r = 5', 'Center (3, 4), radius r = 10'], 1],
                    'ar' => ['ما مركز ونصف قطر الدائرة التي معادلتها x² + y² − 6x + 8y = 0؟', ['المركز (3, −4) ونصف القطر r = 5', 'المركز (−3, 4) ونصف القطر r = 25', 'المركز (6, −8) ونصف القطر r = 5', 'المركز (3, 4) ونصف القطر r = 10'], 1],
                ],
                [
                    'cat' => 'geometry',
                    'en' => ['If the vectors u = ⟨3, k, 1⟩ and v = ⟨2, −1, 4⟩ are orthogonal (u ⊥ v), what is the value of k?', ['k = 10', 'k = −10', 'k = 5', 'k = 0'], 1],
                    'ar' => ['إذا كان المتجهان u = ⟨3, k, 1⟩ و v = ⟨2, −1, 4⟩ متعامدين (u ⊥ v)، فما قيمة k؟', ['k = 10', 'k = −10', 'k = 5', 'k = 0'], 1],
                ],
                [
                    'cat' => 'geometry',
                    'en' => ['Calculate the magnitude (length) of the vector A = ⟨2, −3, 6⟩ in 3D space.', ['7', '√11', '49', '11'], 1],
                    'ar' => ['احسب طول المتجه A = ⟨2, −3, 6⟩ في الفراغ ثلاثي الأبعاد.', ['7', '√11', '49', '11'], 1],
                ],
                [
                    'cat' => 'geometry',
                    'en' => ['What is the cross product i × j of the standard unit vectors?', ['k', '−k', '0', '1'], 1],
                    'ar' => ['ما حاصل الضرب الاتجاهي i × j لمتجهي الوحدة القياسيين؟', ['k', '−k', '0', '1'], 1],
                ],
                [
                    'cat' => 'geometry',
                    'en' => ['The perpendicular distance from the point (1, 2) to the line 3x + 4y − 16 = 0 is:', ['1', '2', '5', '3'], 1],
                    'ar' => ['المسافة العمودية بين النقطة (1, 2) والمستقيم 3x + 4y − 16 = 0 تساوي:', ['1', '2', '5', '3'], 1],
                ],

                // ---------------- Statistics & Probability (25-30) ----------------
                [
                    'cat' => 'statistics',
                    'en' => ['If P(A) = 0.6 and P(B) = 0.5, and events A and B are independent, what is P(A ∩ B)?', ['0.3', '0.8', '0.1', '1.1'], 1],
                    'ar' => ['إذا كان P(A) = 0.6 و P(B) = 0.5 وكان الحدثان A و B مستقلين، فما قيمة P(A ∩ B)؟', ['0.3', '0.8', '0.1', '1.1'], 1],
                ],
                [
                    'cat' => 'statistics',
                    'en' => ['A box contains 5 red balls and 3 white balls. If 2 balls are drawn at random without replacement, what is the probability that both balls are red?', ['5/14', '25/64', '15/56', '5/28'], 1],
                    'ar' => ['صندوق يحتوي على 5 كرات حمراء و3 كرات بيضاء. إذا سُحبت كرتان عشوائيًا دون إعادة، فما احتمال أن تكون الكرتان حمراوين؟', ['5/14', '25/64', '15/56', '5/28'], 1],
                ],
                [
                    'cat' => 'statistics',
                    'en' => ['In how many ways can a committee of 3 students be selected from a group of 8 students (where order does not matter)?', ['56', '336', '24', '112'], 1],
                    'ar' => ['بكم طريقة يمكن اختيار لجنة مكونة من 3 طلاب من بين 8 طلاب (دون اعتبار للترتيب)؟', ['56', '336', '24', '112'], 1],
                ],
                [
                    'cat' => 'statistics',
                    'en' => ['For a standard normal distribution, what are the values of the mean (μ) and standard deviation (σ)?', ['μ = 0, σ = 1', 'μ = 1, σ = 0', 'μ = 50, σ = 10', 'μ = 0, σ = 0'], 1],
                    'ar' => ['في التوزيع الطبيعي المعياري، ما قيمة الوسط الحسابي (μ) والانحراف المعياري (σ)؟', ['μ = 0, σ = 1', 'μ = 1, σ = 0', 'μ = 50, σ = 10', 'μ = 0, σ = 0'], 1],
                ],
                [
                    'cat' => 'statistics',
                    'en' => ["If a student's Z-score on an exam is Z = +2.0, with a test standard deviation σ = 5 and mean μ = 70, what is the student's raw score (X)?", ['80', '75', '85', '60'], 1],
                    'ar' => ['إذا كانت الدرجة المعيارية لطالب في اختبار هي Z = +2.0، وكان الانحراف المعياري للاختبار σ = 5 والوسط μ = 70، فما الدرجة الخام للطالب (X)؟', ['80', '75', '85', '60'], 1],
                ],
                [
                    'cat' => 'statistics',
                    'en' => ['If the interquartile range (IQR) of a dataset is 15 and the first quartile Q₁ = 25, what is the third quartile Q₃?', ['40', '35', '10', '50'], 1],
                    'ar' => ['إذا كان المدى الربيعي (IQR) لمجموعة بيانات يساوي 15 وكان الربيع الأول Q₁ = 25، فما قيمة الربيع الثالث Q₃؟', ['40', '35', '10', '50'], 1],
                ],
            ],
        ];
    }
}
