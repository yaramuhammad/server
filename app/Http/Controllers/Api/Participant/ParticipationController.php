<?php

namespace App\Http\Controllers\Api\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Participant\RegisterParticipantRequest;
use App\Http\Requests\Api\Participant\SubmitResponsesRequest;
use App\Http\Requests\Api\Participant\VerifyLinkPasswordRequest;
use App\Http\Resources\AssessmentLinkPublicResource;
use App\Http\Resources\ParticipantResource;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\TestAttemptResource;
use App\Http\Resources\TestSummaryResource;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\Response;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ParticipationController extends Controller
{
    use ApiResponse;

    /**
     * Resolve bilingual labels in scale_config for the current locale.
     */
    private function resolveScaleConfig(array $scaleConfig): array
    {
        $locale = app()->getLocale();
        $labels = $scaleConfig['labels'] ?? null;

        if ($labels) {
            $resolved = [];
            foreach ($labels as $key => $value) {
                if (is_array($value) && (isset($value['en']) || isset($value['ar']))) {
                    $resolved[$key] = $value[$locale] ?? $value['en'] ?? $value['ar'] ?? '';
                } else {
                    $resolved[$key] = $value;
                }
            }
            $scaleConfig['labels'] = $resolved;
        }

        return $scaleConfig;
    }

    public function showLink(string $token)
    {
        $link = AssessmentLink::where('token', $token)
            ->with('assessment')
            ->firstOrFail();

        if (!$link->isAccessible()) {
            return $this->error('This assessment link is no longer accessible.', 403);
        }

        return $this->success(new AssessmentLinkPublicResource($link));
    }

    public function verifyPassword(VerifyLinkPasswordRequest $request, string $token)
    {
        $link = AssessmentLink::where('token', $token)->firstOrFail();

        if (is_null($link->getRawOriginal('password'))) {
            return $this->success(['verified' => true], 'No password required.');
        }

        if (!password_verify($request->password, $link->getRawOriginal('password'))) {
            return $this->error('Invalid password.', 401);
        }

        return $this->success(['verified' => true], 'Password verified.');
    }

    public function register(RegisterParticipantRequest $request, string $token)
    {
        $link = AssessmentLink::where('token', $token)->firstOrFail();

        if (!$link->isAccessible()) {
            return $this->error('This assessment link is no longer accessible.', 403);
        }

        $participant = Participant::create([
            'assessment_link_id' => $link->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => $request->department,
            'age' => $request->age,
            'gender' => $request->gender,
            'custom_data' => $request->custom_data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'locale' => app()->getLocale(),
        ]);

        return $this->success(new ParticipantResource($participant), 'Registered successfully.', 201);
    }

    public function getSession(Participant $participant)
    {
        $link = $participant->assessmentLink()->with('assessment.tests')->firstOrFail();
        $assessment = $link->assessment;

        // Only consider current attempts (after latest retake grant, if any)
        $attempts = $participant->currentAttempts($assessment->id)->with('test')->get();

        $testsStatus = $assessment->tests->map(function ($test) use ($attempts) {
            $attempt = $attempts->firstWhere('test_id', $test->id);

            return [
                'test' => new TestSummaryResource($test),
                'status' => $attempt ? $attempt->status : 'not_started',
                'attempt_id' => $attempt?->uuid,
            ];
        });

        $allCompleted = $testsStatus->every(fn ($t) => $t['status'] === 'completed');

        return $this->success([
            'participant' => new ParticipantResource($participant),
            'assessment_title' => $assessment->getTranslation('title'),
            'tests' => $testsStatus,
            'all_completed' => $allCompleted,
        ]);
    }

    public function startTest(Participant $participant, Test $test)
    {
        $link = $participant->assessmentLink;
        $assessment = $link->assessment;

        // Verify this test belongs to the assessment
        if (!$assessment->tests()->where('tests.id', $test->id)->exists()) {
            return $this->error('This test is not part of the assessment.', 404);
        }

        // Check for existing attempt (only current round — after latest retake grant)
        $existingAttempt = $participant->currentAttempts($assessment->id)
            ->where('test_id', $test->id)
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->isCompleted()) {
                return $this->error('You have already completed this test.', 409);
            }

            // Check timeout
            if ($existingAttempt->isTimedOut()) {
                $existingAttempt->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                $existingAttempt->calculateScores();

                return $this->error('Time has expired for this test.', 410);
            }

            // Return existing in-progress attempt with saved responses
            $test->load('questions');

            $savedResponses = $existingAttempt->responses()
                ->select('question_id', 'value')
                ->get()
                ->map(fn ($r) => ['question_id' => $r->question_id, 'value' => $r->value]);

            return $this->success([
                'attempt' => new TestAttemptResource($existingAttempt),
                'questions' => QuestionResource::collection(
                    $test->randomize_questions ? $test->questions->shuffle() : $test->questions
                ),
                'remaining_seconds' => $existingAttempt->getRemainingSeconds(),
                'saved_responses' => $savedResponses,
                'scale_config' => $this->resolveScaleConfig($test->scale_config),
            ]);
        }

        // Create new attempt
        $attempt = TestAttempt::create([
            'participant_id' => $participant->id,
            'test_id' => $test->id,
            'assessment_id' => $assessment->id,
            'assessment_link_id' => $link->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $test->load('questions');

        return $this->success([
            'attempt' => new TestAttemptResource($attempt),
            'questions' => QuestionResource::collection(
                $test->randomize_questions ? $test->questions->shuffle() : $test->questions
            ),
            'remaining_seconds' => $attempt->getRemainingSeconds(),
            'saved_responses' => [],
            'scale_config' => $this->resolveScaleConfig($test->scale_config),
        ], 'Test started.', 201);
    }

    public function submitResponses(SubmitResponsesRequest $request, Participant $participant, Test $test)
    {
        $attempt = $participant->attempts()
            ->where('test_id', $test->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        if ($attempt->isTimedOut()) {
            $attempt->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            $attempt->calculateScores();

            return $this->error('Time has expired for this test.', 410);
        }

        foreach ($request->validated('responses') as $responseData) {
            Response::updateOrCreate(
                [
                    'test_attempt_id' => $attempt->id,
                    'question_id' => $responseData['question_id'],
                ],
                [
                    'value' => $responseData['value'],
                    'answered_at' => now(),
                ]
            );
        }

        return $this->success(null, 'Responses saved.');
    }

    public function completeTest(Participant $participant, Test $test)
    {
        $attempt = $participant->attempts()
            ->where('test_id', $test->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        // Validate all required questions have been answered
        $requiredCount = $test->questions()->where('is_required', true)->count();
        $answeredCount = $attempt->responses()->count();

        if ($answeredCount < $requiredCount) {
            return $this->error('Please answer all required questions before submitting.', 422);
        }

        $attempt->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $attempt->calculateScores();

        return $this->success(new TestAttemptResource($attempt->fresh()), 'Test completed.');
    }

    public function getResults(Participant $participant)
    {
        $link = $participant->assessmentLink()->with('assessment')->firstOrFail();
        $assessment = $link->assessment;

        if (!$assessment->show_results_to_participant) {
            return $this->error('Results are not available for this assessment.', 403);
        }

        $attempts = $participant->attempts()
            ->with('test')
            ->completed()
            ->get();

        return $this->success([
            'assessment_title' => $assessment->getTranslation('title'),
            'completion_message' => $link->getTranslation('completion_message'),
            'results' => TestAttemptResource::collection($attempts),
        ]);
    }
}
