<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BigFivePersonalitySeeder::class,
            PsychologicalHardinessSeeder::class,
            CopingStyleSeeder::class,
            AchievementMotivationSeeder::class,
            MultifactorLeadershipSeeder::class,
            EmotionalIntelligenceSeeder::class,
            MultipleIntelligencesSeeder::class,
            CreativityTestSeeder::class,
            ProfessionalCompetenciesSeeder::class,
            GreatEightCompetenciesSeeder::class,
            RavenAPMTestSeeder::class,
            CreativeLeadershipTestSeeder::class,
            AssessmentSeeder::class,
            DemoAssessmentSeeder::class,
            ParticipantAndResponseSeeder::class,
        ]);
    }
}
