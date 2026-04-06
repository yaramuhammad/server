<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentLinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'token' => $this->token,
            'title' => $this->title,
            'url' => $this->getUrl(),
            'starts_at' => $this->starts_at?->toISOString(),
            'expires_at' => $this->expires_at?->toISOString(),
            'max_participants' => $this->max_participants,
            'is_active' => $this->is_active,
            'has_password' => !is_null($this->getRawOriginal('password')),
            'collect_name' => $this->collect_name,
            'collect_email' => $this->collect_email,
            'collect_phone' => $this->collect_phone,
            'collect_department' => $this->collect_department,
            'collect_age' => $this->collect_age,
            'collect_gender' => $this->collect_gender,
            'custom_fields' => $this->custom_fields,
            'welcome_message' => $request->has('bilingual') ? $this->getTranslations('welcome_message') : $this->getTranslation('welcome_message'),
            'completion_message' => $request->has('bilingual') ? $this->getTranslations('completion_message') : $this->getTranslation('completion_message'),
            'participants_count' => $this->whenCounted('participants'),
            'is_accessible' => $this->isAccessible(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
