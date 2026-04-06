<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'question_id' => $this->question_id,
            'question_text' => $this->whenLoaded('question', fn () => $this->question->getTranslation('text')),
            'value' => $this->value,
            'scored_value' => $this->scored_value,
            'answered_at' => $this->answered_at?->toISOString(),
        ];
    }
}
