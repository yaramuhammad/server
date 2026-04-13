<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $request->has('bilingual') ? $this->getTranslations('title') : $this->getTranslation('title'),
            'description' => $request->has('bilingual') ? $this->getTranslations('description') : $this->getTranslation('description'),
            'instructions' => $request->has('bilingual') ? $this->getTranslations('instructions') : $this->getTranslation('instructions'),
            'status' => $this->status,
            'scale_config' => $this->scale_config,
            'scoring_type' => $this->scoring_type,
            'scoring_config' => $this->scoring_config,
            'chart_type' => $this->chart_type ?? 'bar',
            'time_limit_minutes' => $this->time_limit_minutes,
            'randomize_questions' => $this->randomize_questions,
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
            'questions_count' => $this->whenCounted('questions'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
