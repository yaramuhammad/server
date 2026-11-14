<?php

use App\Models\Response;
use App\Models\Test;
use App\Models\TestAttempt;
use Database\Seeders\CreativityTestSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Client change to "هل أنت مبدع؟": every answer now scores 5 / 3 / 1 (the creative answer,
 * Neutral, the other answer — items where "Disagree" is creative are reverse scored), and the
 * 30-150 total is rated on six new bands. Existing answers are re-scored and every completed
 * attempt is recalculated.
 *
 * Data only: rolling back does not restore the previous scoring.
 */
return new class extends Migration
{
    public function up(): void
    {
        $source = CreativityTestSeeder::questions();
        $normalize = fn (?string $text) => preg_replace('/\s+/u', ' ', trim((string) $text));

        $tests = Test::where('title->ar', CreativityTestSeeder::TITLE_AR)->with('questions')->get();

        DB::transaction(function () use ($tests, $source, $normalize) {
            foreach ($tests as $test) {
                // Scores are assigned by item number, so refuse to touch a test whose questions
                // aren't exactly the source items in source order.
                $questions = $test->questions->keyBy('sort_order');
                if ($questions->count() !== count($source)) {
                    throw new RuntimeException("Creativity test {$test->id} has {$questions->count()} questions, expected " . count($source) . '.');
                }
                foreach ($source as $n => $text) {
                    $question = $questions->get($n);
                    if (! $question || $normalize($question->text['ar'] ?? null) !== $normalize($text['ar'])) {
                        throw new RuntimeException("Creativity test {$test->id}: question {$n} does not match the source item.");
                    }
                }

                foreach ($source as $n => $text) {
                    $question = $questions->get($n);
                    $question->update([
                        'scale_override' => ['score_map' => CreativityTestSeeder::scoreMap($n)] + ($question->scale_override ?? []),
                    ]);
                }

                $test->update([
                    'scoring_config' => ['use_percentage' => false, 'ranges' => CreativityTestSeeder::ranges()],
                ]);

                Response::whereIn('question_id', $test->questions->pluck('id'))->with('question')
                    ->chunkById(500, function ($responses) {
                        foreach ($responses as $response) {
                            $response->update(['scored_value' => $response->question->calculateScoredValue($response->value)]);
                        }
                    });

                TestAttempt::completed()->where('test_id', $test->id)->each(fn ($attempt) => $attempt->calculateScores());
            }
        });
    }

    public function down(): void
    {
        // Data change only; the previous scoring is not restored.
    }
};
