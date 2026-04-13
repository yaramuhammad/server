<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssessmentLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
            'password' => ['nullable', 'string', 'min:4'],
            'collect_name' => ['sometimes', 'boolean'],
            'collect_email' => ['sometimes', 'boolean'],
            'collect_phone' => ['sometimes', 'boolean'],
            'collect_company' => ['sometimes', 'boolean'],
            'collect_job_title' => ['sometimes', 'boolean'],
            'collect_age' => ['sometimes', 'boolean'],
            'collect_gender' => ['sometimes', 'boolean'],
            'custom_fields' => ['nullable', 'array'],
            'welcome_message' => ['nullable', 'array'],
            'welcome_message.en' => ['nullable', 'string'],
            'welcome_message.ar' => ['nullable', 'string'],
            'completion_message' => ['nullable', 'array'],
            'completion_message.en' => ['nullable', 'string'],
            'completion_message.ar' => ['nullable', 'string'],
        ];
    }
}
