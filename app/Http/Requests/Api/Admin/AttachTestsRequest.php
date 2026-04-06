<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AttachTestsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tests' => ['required', 'array', 'min:1'],
            'tests.*.uuid' => ['required', 'exists:tests,uuid'],
            'tests.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
