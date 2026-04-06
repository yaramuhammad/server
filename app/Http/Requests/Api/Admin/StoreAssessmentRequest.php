<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.ar' => ['required', 'string', 'max:255'],
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
