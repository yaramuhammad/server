<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestRequest extends FormRequest
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
            'scale_config' => ['required', 'array'],
            'scale_config.min' => ['required', 'integer', 'min:0'],
            'scale_config.max' => ['required', 'integer', 'min:1'],
            'scale_config.labels' => ['nullable', 'array'],
            'scoring_type' => ['sometimes', 'in:simple,category,range,weighted'],
            'scoring_config' => ['nullable', 'array'],
            'scoring_config.categories' => ['nullable', 'array'],
            'scoring_config.categories.*.key' => ['required_with:scoring_config.categories', 'string', 'max:100'],
            'scoring_config.categories.*.label' => ['required_with:scoring_config.categories', 'array'],
            'scoring_config.categories.*.interpretation' => ['nullable', 'array'],
            'scoring_config.ranges' => ['nullable', 'array'],
            'scoring_config.ranges.*.min' => ['required_with:scoring_config.ranges', 'numeric'],
            'scoring_config.ranges.*.max' => ['required_with:scoring_config.ranges', 'numeric'],
            'scoring_config.ranges.*.label' => ['required_with:scoring_config.ranges', 'array'],
            'scoring_config.use_percentage' => ['nullable', 'boolean'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1'],
            'randomize_questions' => ['sometimes', 'boolean'],
        ];
    }
}
