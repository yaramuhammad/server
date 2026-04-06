<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentLinkPublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'assessment_title' => $this->assessment->getTranslation('title'),
            'assessment_description' => $this->assessment->getTranslation('description'),
            'assessment_instructions' => $this->assessment->getTranslation('instructions'),
            'welcome_message' => $this->getTranslation('welcome_message'),
            'has_password' => !is_null($this->getRawOriginal('password')),
            'required_fields' => $this->getRequiredFields(),
            'custom_fields' => $this->custom_fields,
            'tests_count' => $this->assessment->tests()->count(),
        ];
    }
}
