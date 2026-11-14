<?php

use App\Models\Test;
use App\Models\TestAttempt;
use Database\Seeders\StudentAbilities\CrossCulturalReadinessBattery;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Client change to the Cross-Cultural Readiness battery: its subscales and overall readiness are
 * rated like the battery's other tests — below 50% Low, 50% Moderate, 75% High readiness — with
 * the source's recommendations kept on the overall bands. Completed attempts are recalculated.
 *
 * Data only: rolling back does not restore the previous bands.
 */
return new class extends Migration
{
    public function up(): void
    {
        $def = CrossCulturalReadinessBattery::definition();
        $expectedKeys = array_keys($def['categories']);

        $tests = Test::where('title->ar', $def['title']['ar'])->get();

        DB::transaction(function () use ($tests, $def, $expectedKeys) {
            foreach ($tests as $test) {
                $config = $test->scoring_config ?? [];
                $keys = array_column($config['categories'] ?? [], 'key');
                if ($keys !== $expectedKeys) {
                    throw new RuntimeException("Cross-cultural test {$test->id} has subscales [" . implode(', ', $keys) . '], expected [' . implode(', ', $expectedKeys) . '].');
                }

                $config['categories'] = array_map(
                    fn ($category) => ['interpretation' => $def['interpretation']] + $category,
                    $config['categories']
                );
                $config['ranges'] = $def['overall']['ranges'];
                $config['use_percentage'] = $def['overall']['use_percentage'];
                $test->update(['scoring_config' => $config]);

                TestAttempt::completed()->where('test_id', $test->id)->each(fn ($attempt) => $attempt->calculateScores());
            }
        });
    }

    public function down(): void
    {
        // Data change only; the previous bands are not restored.
    }
};
