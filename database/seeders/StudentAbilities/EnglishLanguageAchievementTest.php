<?php

namespace Database\Seeders\StudentAbilities;

/**
 * Standardized English Language Achievement Test.
 *
 * Source: "Standardized English Language Achievement Test.pdf" (English only).
 * Deliberately NOT translated: every student takes it in English regardless of UI language,
 * so the `ar` side of every field holds the same English text.
 * Questions sharing a passage repeat it, because the participant sees one question per screen.
 */
class EnglishLanguageAchievementTest
{
    private const PASSAGE_ACADEMIC = '"The rapid integration of generative artificial intelligence into higher education has ignited a profound methodological debate. While proponents argue that automated cognitive tools democratize access to complex data and accelerate literature synthesis, critics caution against the insidious erosion of intellectual self-reliance. When students rely systematically on algorithmic outputs to aggregate knowledge, they risk bypassing the laborious yet necessary process of cognitive wrestling—the very crucible in which original synthesis and critical discernment are forged. Consequently, higher education institutions face an existential mandate: to reconfigure assessment frameworks so that they measure non-algorithmic human reasoning rather than passive information retrieval."';

    private const PASSAGE_LITERARY = '"The professor\'s lecture was characterized by an unyielding rigor; yet beneath his austere demeanor lay a profound commitment to scholarly mentorship. He challenged his students not to diminish their ambition, but to ensure that their empirical claims were anchored in unassailable evidence."';

    public static function definition(): array
    {
        $academic = fn (string $q) => "Read the following academic excerpt and answer the question.\n\n" . self::PASSAGE_ACADEMIC . "\n\n" . $q;
        $literary = fn (string $q) => "Read the following literary excerpt and answer the question.\n\n" . self::PASSAGE_LITERARY . "\n\n" . $q;

        return [
            'title' => 'Standardized English Language Achievement Test',
            'description' => 'A 30-item English achievement test covering advanced reading comprehension and critical analysis, grammar and syntax, academic vocabulary, and academic writing.',
            'instructions' => 'This test is in English. Each question has four options and only one correct answer. Some questions refer to a reading passage shown above the question. Each correct answer earns one point; there is no penalty for wrong answers.',
            'chart' => 'column',
            'categories' => [
                'reading' => 'Reading Comprehension & Critical Analysis',
                'grammar' => 'Grammar, Structure & Syntax',
                'vocabulary' => 'Academic Vocabulary & Contextual Diction',
                'writing' => 'Academic Writing, Logic & Sentence Structure',
            ],
            'questions' => [
                // ---------------- Part 1: Reading Comprehension & Critical Analysis (1-8) ----------------
                [
                    'cat' => 'reading',
                    'en' => [$academic('Which of the following best captures the central thesis of the passage?'), ['Generative AI will inevitably replace human reasoning in academic research within the next decade.', 'Educational institutions must redesign assessments to foster critical cognitive processing amidst AI proliferation.', 'Automated tools are inherently detrimental to academic integrity and should be prohibited in universities.', 'Cognitive wrestling is an outdated pedagogical model superseded by modern data aggregation techniques.'], 2],
                ],
                [
                    'cat' => 'reading',
                    'en' => [$academic('The word "crucible" as used in the passage most closely denotes:'), ['A severe test or process through which new and original thinking is developed.', 'A digital database designed for automated data storage.', 'A superficial layer of intellectual consensus among scholars.', 'An obstacle that permanently prevents the acquisition of knowledge.'], 1],
                ],
                [
                    'cat' => 'reading',
                    'en' => [$academic("What is the author's primary stance regarding algorithmic knowledge aggregation?"), ['Unconditionally enthusiastic.', 'Entirely dismissive of its technological utility.', 'Cautiously critical of its impact on autonomous cognitive skills.', 'Indifferent toward its pedagogical implications.'], 3],
                ],
                [
                    'cat' => 'reading',
                    'en' => [$academic('It can be logically inferred from the text that the author views the process of "cognitive wrestling" as:'), ['An inefficient remnant of traditional schooling.', 'Essential for cultivating authentic intellectual synthesis and discernment.', 'Easily replicable by advanced machine-learning algorithms.', 'A secondary skill unnecessary for doctoral-level research.'], 2],
                ],
                [
                    'cat' => 'reading',
                    'en' => [$academic("What structural role does the final sentence play in the passage's overall argument?"), ['It introduces an unrelated counterargument to dilute the main premise.', 'It offers an institutional recommendation based on the preceding critical synthesis.', 'It provides empirical statistical evidence supporting the initial premise.', "It contradicts the author's prior assertions regarding AI utility."], 2],
                ],
                [
                    'cat' => 'reading',
                    'en' => [$literary('The tone of the author toward the professor can best be described as:'), ['Disdainful and critical.', 'Respectful and analytical.', 'Dismissive and satirical.', 'Overly emotional and reverent.'], 2],
                ],
                [
                    'cat' => 'reading',
                    'en' => [$literary('What does the passage imply about the relationship between "austere demeanor" and "scholarly mentorship"?'), ['Surface austerity can coexist with deep dedication to student growth.', 'Rigorous academic standards prevent effective interpersonal mentorship.', 'Mentorship requires the complete elimination of academic austerity.', 'Emotional warmth is more important than empirical evidence in research.'], 1],
                ],
                [
                    'cat' => 'reading',
                    'en' => ['Which of the following sentences demonstrates the most formal academic register suitable for an empirical research publication?', ['"The researchers looked at a bunch of data and figured out that the theory was kind of wrong."', '"Empirical analysis revealed a statistically significant divergence from the hypothesized baseline model."', '"Basically, the results didn\'t turn out like the team thought they would at all."', '"We checked the numbers and found out that the model was totally off the mark."'], 2],
                ],

                // ---------------- Part 2: Grammar, Structure & Syntax (9-20) ----------------
                [
                    'cat' => 'grammar',
                    'en' => ["Select the option that correctly completes the sentence:\n\"Not until the longitudinal dataset was completely anonymized _______ permitted to conduct the cross-sectional statistical analysis.\"", ['the research team was', 'was the research team', 'had the research team', 'the research team had been'], 2],
                ],
                [
                    'cat' => 'grammar',
                    'en' => ["Select the correct subjunctive structure:\n\"The ethics committee insisted that the principal investigator _______ the consent protocol prior to participant recruitment.\"", ['modifies', 'modified', 'modify', 'will modify'], 3],
                ],
                [
                    'cat' => 'grammar',
                    'en' => ["Select the option that maintains precise parallel structure:\n\"The new curriculum aims not only at improving students' quantitative reasoning _______ their capacity for qualitative synthesis.\"", ['but also at enhancing', 'and also enhances', 'but it enhances as well', 'along with expanding'], 1],
                ],
                [
                    'cat' => 'grammar',
                    'en' => ["Identify the grammatically correct conditional sentence:\n\"Had the statistical model been calibrated properly during the pilot study, the researchers _______ erroneous conclusions regarding the mediating variable.\"", ['would not draw', 'would not have drawn', 'will not have drawn', 'did not draw'], 2],
                ],
                [
                    'cat' => 'grammar',
                    'en' => ["Choose the correct relative clause structure:\n\"The psychometric instrument, _______ reliability coefficients exceeded the institutional threshold, was approved for cross-cultural adaptation.\"", ['of which', 'whose', 'that its', 'which'], 2],
                ],
                [
                    'cat' => 'grammar',
                    'en' => ['Choose the sentence with the correct modifier placement:', ['"Having analyzed the survey data, the hypothesis was confirmed by the lead scholar."', '"Having analyzed the survey data, the lead scholar confirmed the hypothesis."', '"Analyzing the survey data, the hypothesis seemed fully substantiated to the team."', '"The hypothesis was confirmed having analyzed the survey data by the team."'], 2],
                ],
                [
                    'cat' => 'grammar',
                    'en' => ["Select the correct advanced modal usage:\n\"Given the stringent control parameters maintained throughout the experiment, any confounding factors _______ minimal impact on the final variance.\"", ['must have had', 'should have', 'ought to have', 'might have been'], 1],
                ],
                [
                    'cat' => 'grammar',
                    'en' => ["Choose the correct form of the verb after a preposed negative phrase:\n\"Under no circumstances _______ research participants be subjected to psychological distress without prior debriefing.\"", ['should', 'must not', 'ought', 'would not'], 1],
                ],
                [
                    'cat' => 'grammar',
                    'en' => ["Complete the sentence with the appropriate participle structure:\n\"_______ between two competing theoretical paradigms, the doctoral committee recommended a mixed-methods design.\"", ['Divided', 'Having divided', 'Division', 'To divide'], 1],
                ],
                [
                    'cat' => 'grammar',
                    'en' => ['Select the sentence that uses the correct agreement with collective nouns and restrictive modifiers:', ['"The panel of academic experts are presenting their individual findings at the conference today."', '"The panel of academic experts is presenting their individual findings at the conference today."', '"The panel of academic experts have presented its individual findings at the conference today."', '"The panel of academic experts were presenting its individual findings at the conference today."'], 1],
                ],
                [
                    'cat' => 'grammar',
                    'en' => ["Identify the correct usage of gerunds vs. infinitives in academic structures:\n\"The research protocol restricts investigators from _______ sensitive demographic identifiers without explicit ethical clearance.\"", ['access', 'accessing', 'to access', 'having accessed'], 2],
                ],
                [
                    'cat' => 'grammar',
                    'en' => ['Which sentence correctly uses ellipsis without creating ambiguity?', ['"The quantitative sample was larger than the qualitative."', '"The quantitative sample was larger than the qualitative sample was."', '"The quantitative sample was larger than that of the qualitative study."', '"The quantitative sample was larger than qualitative ones."'], 3],
                ],

                // ---------------- Part 3: Academic Vocabulary & Contextual Diction (21-25) ----------------
                [
                    'cat' => 'vocabulary',
                    'en' => ["Select the word that best completes the academic statement:\n\"The preliminary findings were considered _______; further empirical validation across independent samples is required before drawing definitive conclusions.\"", ['conclusive', 'tentative', 'immutable', 'dogmatic'], 2],
                ],
                [
                    'cat' => 'vocabulary',
                    'en' => ["Choose the precise academic collocation:\n\"The statistical analysis demonstrated a _______ correlation between transformational leadership behaviors and organizational citizenship.\"", ['strong positive', 'heavy high', 'dense superior', 'thick constructive'], 1],
                ],
                [
                    'cat' => 'vocabulary',
                    'en' => ["Select the word that best fits the formal context:\n\"The author's argument was undermined by several _______ assumptions that failed to withstand rigorous empirical scrutiny.\"", ['tenuous', 'resilient', 'robust', 'pragmatic'], 1],
                ],
                [
                    'cat' => 'vocabulary',
                    'en' => ["Choose the appropriate discourse marker for contrast:\n\"The initial trial showed promising results in vitro; _______, clinical trials revealed substantial side effects that limited systemic application.\"", ['furthermore', 'conversely', 'consequently', 'inadvertently'], 2],
                ],
                [
                    'cat' => 'vocabulary',
                    'en' => ["Select the word that denotes \"the reduction of intensity or severity\":\n\"The introduction of flexible working arrangements served as an effective mechanism for the _______ of occupational burnout.\"", ['exacerbation', 'mitigation', 'proliferation', 'stagnation'], 2],
                ],

                // ---------------- Part 4: Academic Writing, Logic & Sentence Structure (26-30) ----------------
                [
                    'cat' => 'writing',
                    'en' => ['Identify the sentence that suffers from redundancy (wordiness):', ['"The empirical study investigated the underlying factors influencing student retention."', '"In my personal opinion, I think that the future prospects are bright and promising."', '"The results suggest a moderate relationship between stress appraisal and academic focus."', '"Methodological rigor is essential for establishing constructive validity."'], 2],
                ],
                [
                    'cat' => 'writing',
                    'en' => ["Which transition word best fits the logical flow of this argument?\n\"The proposed model accounts for structural variance in performance. _______, it provides a scalable framework for cross-departmental implementation.\"", ['Nonetheless', 'Moreover', 'Conversely', 'On the contrary'], 2],
                ],
                [
                    'cat' => 'writing',
                    'en' => ['Select the sentence that avoids a comma splice or run-on structure:', ['"The literature review was exhaustive, it covered fifty years of empirical studies."', '"The literature review was exhaustive; it covered fifty years of empirical studies."', '"The literature review was exhaustive, covering fifty years of empirical studies, however, some gaps remained."', '"The literature review was exhaustive it covered fifty years of empirical research."'], 2],
                ],
                [
                    'cat' => 'writing',
                    'en' => ["Choose the option that synthesizes the two ideas most concisely:\n• Idea 1: The survey response rate was below 30%.\n• Idea 2: The research team decided to extend the data collection period by two weeks.", ['"Because the survey response rate was below 30%, the research team extended the data collection period by two weeks."', '"The survey response rate was below 30% and so the research team decided they should extend data collection for two weeks."', '"Extending the data collection period by two weeks, the survey response rate was below 30%."', '"The survey response rate being low under 30%, two weeks were extended by the research team."'], 1],
                ],
                [
                    'cat' => 'writing',
                    'en' => ['Identify the sentence that maintains an objective, unbiased academic tone:', ['"Obviously, any researcher who disagrees with this ground-breaking theory is completely mistaken."', '"The data suggests that alternative interpretations may warrant further empirical investigation."', '"It is absolutely ridiculous to claim that social media has no effect on youth psychology."', '"Everyone knows that quantitative research is far superior to qualitative stories."'], 2],
                ],
            ],
        ];
    }
}
