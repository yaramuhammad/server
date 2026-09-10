<?php

namespace App\Http\Requests\Api\Participant;

use App\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SubmitResponsesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $test = $this->route('test');

        return [
            'responses' => ['required', 'array', 'min:1'],
            'responses.*.question_id' => [
                'required',
                'integer',
                // Question must belong to the test named in the route, not just
                // exist somewhere in the system.
                Rule::exists('questions', 'id')->where(
                    fn ($query) => $query->where('test_id', $test?->id)
                ),
            ],
            'responses.*.value' => ['required', 'integer'],
        ];
    }

    /**
     * Ensure each submitted value is one of the allowed options for its
     * specific question (scale range, score_map keys, or correct_answer).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $test = $this->route('test');
            if (! $test) {
                return;
            }

            $responses = $this->input('responses');
            if (! is_array($responses)) {
                return;
            }

            $questionIds = collect($responses)
                ->pluck('question_id')
                ->filter()
                ->unique();

            $questions = Question::whereIn('id', $questionIds)
                ->where('test_id', $test->id)
                ->get()
                ->keyBy('id');

            foreach ($responses as $index => $response) {
                $question = $questions->get($response['question_id'] ?? null);
                if (! $question) {
                    continue; // handled by the exists rule
                }

                $value = $response['value'] ?? null;
                if (! is_int($value) || ! $this->valueAllowedForQuestion($question, $value)) {
                    $validator->errors()->add(
                        "responses.{$index}.value",
                        'The selected answer is not valid for this question.'
                    );
                }
            }
        });
    }

    private function valueAllowedForQuestion(Question $question, int $value): bool
    {
        $scale = $question->scale_override ?? $question->test->scale_config ?? [];

        // Explicit score map: only its keys are valid raw values.
        if (! empty($scale['score_map']) && is_array($scale['score_map'])) {
            return array_key_exists((string) $value, $scale['score_map']);
        }

        $min = isset($scale['min']) ? (int) $scale['min'] : 1;
        $max = isset($scale['max']) ? (int) $scale['max'] : 5;

        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        return $value >= $min && $value <= $max;
    }
}
