<?php

namespace App\Http\Requests\Api\Participant;

use App\Models\AssessmentLink;
use Illuminate\Foundation\Http\FormRequest;

class RegisterParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $link = AssessmentLink::where('token', $this->route('token'))->firstOrFail();
        $rules = [];

        if ($link->collect_name) $rules['name'] = ['required', 'string', 'max:255'];
        if ($link->collect_email) $rules['email'] = ['required', 'email', 'max:255'];
        if ($link->collect_phone) $rules['phone'] = ['nullable', 'string', 'max:50'];
        if ($link->collect_company) $rules['company'] = ['nullable', 'string', 'max:255'];
        if ($link->collect_job_title) $rules['job_title'] = ['nullable', 'string', 'max:255'];
        if ($link->collect_age) $rules['age'] = ['nullable', 'integer', 'min:10', 'max:120'];
        if ($link->collect_gender) $rules['gender'] = ['nullable', 'in:male,female,other,prefer_not_to_say'];
        if ($link->custom_fields) $rules['custom_data'] = ['nullable', 'array'];

        return $rules;
    }
}
