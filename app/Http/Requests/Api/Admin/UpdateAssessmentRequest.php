<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'array'],
            'title.en' => ['required_with:title', 'string', 'max:255'],
            'title.ar' => ['required_with:title', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'instructions' => ['nullable', 'array'],
            'instructions.en' => ['nullable', 'string'],
            'instructions.ar' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:draft,published,archived'],
            'show_results_to_participant' => ['sometimes', 'boolean'],
        ];
    }
}
