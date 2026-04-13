<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $this->getTranslation('title'),
            'status' => $this->status,
            'scoring_type' => $this->scoring_type,
            'chart_type' => $this->chart_type ?? 'bar',
            'questions_count' => $this->whenCounted('questions'),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
