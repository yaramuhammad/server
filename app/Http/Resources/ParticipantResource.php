<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'job_title' => $this->job_title,
            'age' => $this->age,
            'gender' => $this->gender,
            'locale' => $this->locale,
            'attempts' => TestAttemptResource::collection($this->whenLoaded('attempts')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
