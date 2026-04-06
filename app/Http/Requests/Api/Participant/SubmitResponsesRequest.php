<?php

namespace App\Http\Requests\Api\Participant;

use Illuminate\Foundation\Http\FormRequest;

class SubmitResponsesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'responses' => ['required', 'array', 'min:1'],
            'responses.*.question_id' => ['required', 'integer', 'exists:questions,id'],
            'responses.*.value' => ['required', 'integer'],
        ];
    }
}
