<?php

namespace App\Http\Requests\Api\Participant;

use Illuminate\Foundation\Http\FormRequest;

class VerifyLinkPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string'],
        ];
    }
}
