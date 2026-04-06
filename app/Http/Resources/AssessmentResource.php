<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $request->has('bilingual') ? $this->getTranslations('title') : $this->getTranslation('title'),
            'description' => $request->has('bilingual') ? $this->getTranslations('description') : $this->getTranslation('description'),
            'instructions' => $request->has('bilingual') ? $this->getTranslations('instructions') : $this->getTranslation('instructions'),
            'status' => $this->status,
            'show_results_to_participant' => $this->show_results_to_participant,
            'tests' => TestSummaryResource::collection($this->whenLoaded('tests')),
            'tests_count' => $this->whenCounted('tests'),
            'links_count' => $this->whenCounted('links'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
