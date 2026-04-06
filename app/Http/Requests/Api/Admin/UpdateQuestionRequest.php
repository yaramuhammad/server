<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => ['sometimes', 'array'],
            'text.en' => ['required_with:text', 'string'],
            'text.ar' => ['required_with:text', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_reverse_scored' => ['sometimes', 'boolean'],
            'scale_override' => ['nullable', 'array'],
            'scale_override.min' => ['sometimes', 'integer', 'min:0'],
            'scale_override.max' => ['sometimes', 'integer', 'min:1'],
            'scale_override.labels' => ['nullable', 'array'],
            'scale_override.score_map' => ['nullable', 'array'],
            'scale_override.score_map.*' => ['integer', 'min:0'],
            'is_required' => ['sometimes', 'boolean'],
            'category_key' => ['nullable', 'string', 'max:100'],
            'weight' => ['sometimes', 'numeric', 'min:0.01', 'max:100'],
            'correct_answer' => ['nullable', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
