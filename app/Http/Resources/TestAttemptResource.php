<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'test' => new TestSummaryResource($this->whenLoaded('test')),
            'status' => $this->status,
            'started_at' => $this->started_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'score_raw' => $this->when($this->isCompleted(), $this->score_raw),
            'score_max' => $this->when($this->isCompleted(), $this->score_max),
            'score_percentage' => $this->when($this->isCompleted(), $this->score_percentage),
            'score_average' => $this->when($this->isCompleted(), $this->score_average),
            'score_details' => $this->when($this->isCompleted(), $this->score_details),
            'time_spent_seconds' => $this->time_spent_seconds,
        ];
    }
}
