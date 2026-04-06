<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Format scale_override, resolving bilingual labels and preserving object keys.
     */
    private function formatScaleOverride(Request $request): ?object
    {
        $override = $this->scale_override;
        if (!$override) return null;

        $labels = $override['labels'] ?? null;
        $isBilingual = $request->has('bilingual');

        // Resolve bilingual labels: {"1": {"en": "...", "ar": "..."}} → {"1": "..."}
        if ($labels && !$isBilingual) {
            $locale = $request->header('Accept-Language', 'en');
            $locale = in_array($locale, ['en', 'ar']) ? $locale : 'en';

            $resolved = [];
            foreach ($labels as $key => $value) {
                if (is_array($value) && (isset($value['en']) || isset($value['ar']))) {
                    $resolved[$key] = $value[$locale] ?? $value['en'] ?? $value['ar'] ?? '';
                } else {
                    $resolved[$key] = $value;
                }
            }
            $labels = $resolved;
        }

        return (object) [
            'min' => $override['min'] ?? 1,
            'max' => $override['max'] ?? 4,
            'labels' => $labels ? (object) $labels : null,
            'score_map' => !empty($override['score_map']) ? (object) $override['score_map'] : null,
        ];
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'text' => $request->has('bilingual') ? $this->getTranslations('text') : $this->getTranslation('text'),
            'image_url' => $this->image_path ? asset('storage/' . $this->image_path) : null,
            'sort_order' => $this->sort_order,
            'is_reverse_scored' => $this->is_reverse_scored,
            'is_required' => $this->is_required,
            'category_key' => $this->category_key,
            'weight' => (float) $this->weight,
            'correct_answer' => $this->when($request->has('bilingual'), $this->correct_answer),
            'scale_override' => $this->formatScaleOverride($request),
        ];
    }
}
