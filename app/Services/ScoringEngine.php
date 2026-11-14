<?php

namespace App\Services;

use App\Models\TestAttempt;
use Illuminate\Support\Collection;

class ScoringEngine
{
    /**
     * Calculate scores for a completed test attempt.
     * Returns an array with 'summary' (flat scores) and 'details' (structured breakdown).
     */
    public function calculate(TestAttempt $attempt): array
    {
        $test = $attempt->test;
        $responses = $attempt->responses()->with('question')->get();
        // Questions read their test's scale; share the loaded test instead of querying it per question.
        $responses->each(fn ($r) => $r->question?->setRelation('test', $test));
        $scoringType = $test->scoring_type ?? 'simple';
        $scoringConfig = $test->scoring_config ?? [];

        return match ($scoringType) {
            'category' => $this->calculateCategory($test, $responses, $scoringConfig),
            'range' => $this->calculateRange($test, $responses, $scoringConfig),
            'weighted' => $this->calculateWeighted($test, $responses, $scoringConfig),
            default => $this->calculateSimple($test, $responses),
        };
    }

    /**
     * Simple: sum scored values, percentage of max.
     */
    private function calculateSimple($test, Collection $responses): array
    {
        $requiredQuestions = $test->questions()->where('is_required', true)->get()
            ->each(fn ($question) => $question->setRelation('test', $test));
        $requiredCount = $requiredQuestions->count();

        $scoreRaw = $responses->sum('scored_value');
        $scoreMax = $requiredQuestions->sum(fn ($question) => $question->maxScoredValue());
        $scorePercentage = $scoreMax > 0 ? round(($scoreRaw / $scoreMax) * 100, 2) : 0;
        $scoreAverage = $responses->count() > 0 ? round($responses->avg('scored_value'), 2) : 0;

        return [
            'summary' => [
                'score_raw' => $scoreRaw,
                'score_max' => $scoreMax,
                'score_percentage' => $scorePercentage,
                'score_average' => $scoreAverage,
            ],
            'details' => [
                'type' => 'simple',
                'total_questions' => $responses->count(),
                'required_questions' => $requiredCount,
            ],
        ];
    }

    /**
     * Category: group questions by category_key, compute per-category scores.
     * scoring_config.categories = [
     *   { "key": "openness", "label": {"en": "Openness", "ar": "..."}, "interpretation": [{"min":0,"max":50,"label":{"en":"Low",...}}, ...] }
     * ]
     */
    private function calculateCategory($test, Collection $responses, array $config): array
    {
        $categoryDefs = collect($config['categories'] ?? []);

        // Group responses by their question's category_key
        $grouped = $responses->groupBy(fn ($r) => $r->question->category_key ?? '_uncategorized');

        $categories = [];
        $totalRaw = 0;
        $totalMax = 0;

        foreach ($categoryDefs as $catDef) {
            $key = $catDef['key'];
            $catResponses = $grouped->get($key, collect());
            $questionCount = $catResponses->count();

            $raw = $catResponses->sum('scored_value');
            $max = $catResponses->sum(fn ($r) => $r->question->maxScoredValue());
            $percentage = $max > 0 ? round(($raw / $max) * 100, 2) : 0;
            $average = $questionCount > 0 ? round($catResponses->avg('scored_value'), 2) : 0;

            // Find interpretation label based on percentage
            $interpretation = null;
            foreach ($catDef['interpretation'] ?? [] as $range) {
                if ($percentage >= $range['min'] && $percentage <= $range['max']) {
                    $interpretation = $range['label'] ?? null;
                    break;
                }
            }

            $categories[] = [
                'key' => $key,
                'label' => $catDef['label'] ?? $key,
                'score_raw' => $raw,
                'score_max' => $max,
                'score_percentage' => $percentage,
                'score_average' => $average,
                'question_count' => $questionCount,
                'interpretation' => $interpretation,
            ];

            $totalRaw += $raw;
            $totalMax += $max;
        }

        $totalPercentage = $totalMax > 0 ? round(($totalRaw / $totalMax) * 100, 2) : 0;

        $details = [
            'type' => 'category',
            'categories' => $categories,
        ];

        // Report the N highest-scoring categories (e.g. Holland types) with their
        // descriptions. usort is stable, so ties keep the configured category order.
        if (! empty($config['top_categories'])) {
            $ranked = $categories;
            usort($ranked, fn ($a, $b) => $b['score_raw'] <=> $a['score_raw']);
            $descriptions = $categoryDefs->pluck('description', 'key');

            $details['top_categories'] = array_map(fn ($cat) => [
                'key' => $cat['key'],
                'label' => $cat['label'],
                'description' => $descriptions[$cat['key']] ?? null,
                'score_raw' => $cat['score_raw'],
                'score_max' => $cat['score_max'],
            ], array_slice($ranked, 0, (int) $config['top_categories']));
        }

        // Optional overall interpretation of the total, using the same
        // `ranges` / `use_percentage` keys as range scoring.
        if (! empty($config['ranges'])) {
            $usePercentage = $config['use_percentage'] ?? true;
            $details += $this->matchRange($config['ranges'], $usePercentage ? $totalPercentage : $totalRaw);
        }

        return [
            'summary' => [
                'score_raw' => $totalRaw,
                'score_max' => $totalMax,
                'score_percentage' => $totalPercentage,
                'score_average' => $responses->count() > 0 ? round($responses->avg('scored_value'), 2) : 0,
            ],
            'details' => $details,
        ];
    }

    /**
     * Range: simple sum, but interpret the total into a named range.
     * scoring_config.ranges = [
     *   { "min": 0, "max": 20, "label": {"en": "Low", "ar": "..."}, "description": {"en": "...", "ar": "..."} }
     * ]
     */
    private function calculateRange($test, Collection $responses, array $config): array
    {
        $simple = $this->calculateSimple($test, $responses);
        $percentage = $simple['summary']['score_percentage'];
        $usePercentage = $config['use_percentage'] ?? true;
        $compareValue = $usePercentage ? $percentage : $simple['summary']['score_raw'];

        return [
            'summary' => $simple['summary'],
            'details' => ['type' => 'range'] + $this->matchRange($config['ranges'] ?? [], $compareValue),
        ];
    }

    /**
     * Find the range containing $value; returns the `matched_range` / `all_ranges` detail keys.
     */
    private function matchRange(array $ranges, float|int $value): array
    {
        $matchedRange = null;
        foreach ($ranges as $range) {
            if ($value >= $range['min'] && $value <= $range['max']) {
                $matchedRange = $range;
                break;
            }
        }

        return [
            'matched_range' => $matchedRange ? [
                'label' => $matchedRange['label'] ?? null,
                'description' => $matchedRange['description'] ?? null,
                'min' => $matchedRange['min'],
                'max' => $matchedRange['max'],
            ] : null,
            'all_ranges' => array_map(fn ($r) => [
                'label' => $r['label'] ?? null,
                'min' => $r['min'],
                'max' => $r['max'],
            ], $ranges),
        ];
    }

    /**
     * Weighted: each question has a weight multiplier.
     * scoring_config = {} (weights are stored on each question's `weight` column)
     */
    private function calculateWeighted($test, Collection $responses, array $config): array
    {
        $scaleConfig = $test->scale_config ?? [];
        $scaleMax = $scaleConfig['max'] ?? 5;

        $weightedRaw = 0;
        $weightedMax = 0;
        $questionBreakdown = [];

        foreach ($responses as $response) {
            $weight = $response->question->weight ?? 1.0;
            $weightedScore = $response->scored_value * $weight;
            $maxForQuestion = $scaleMax * $weight;

            $weightedRaw += $weightedScore;
            $weightedMax += $maxForQuestion;

            $questionBreakdown[] = [
                'question_id' => $response->question_id,
                'raw_value' => $response->value,
                'scored_value' => $response->scored_value,
                'weight' => $weight,
                'weighted_score' => round($weightedScore, 2),
            ];
        }

        $percentage = $weightedMax > 0 ? round(($weightedRaw / $weightedMax) * 100, 2) : 0;

        return [
            'summary' => [
                'score_raw' => round($weightedRaw, 2),
                'score_max' => round($weightedMax, 2),
                'score_percentage' => $percentage,
                'score_average' => count($questionBreakdown) > 0
                    ? round($weightedRaw / count($questionBreakdown), 2)
                    : 0,
            ],
            'details' => [
                'type' => 'weighted',
                'questions' => $questionBreakdown,
            ],
        ];
    }
}
