<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestAttemptResource;
use App\Models\ParticipantAccount;
use App\Models\Participant;
use App\Models\RetakeGrant;
use App\Services\PdfExportService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ParticipantManagementController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = ParticipantAccount::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderByDesc('created_at')->paginate(20);

        // Eager load assessments data for each account
        $accounts->getCollection()->transform(function ($account) {
            $participants = $account->participants()
                ->with(['assessmentLink.assessment.tests', 'attempts'])
                ->get();

            $assessments = $participants->map(function ($participant) {
                $link = $participant->assessmentLink;
                if (!$link || !$link->assessment) return null;

                $assessment = $link->assessment;
                $attempts = $participant->attempts;

                $testsStatus = $assessment->tests->map(function ($test) use ($attempts) {
                    $attempt = $attempts->firstWhere('test_id', $test->id);
                    return [
                        'test_title' => $test->getTranslation('title'),
                        'status' => $attempt ? $attempt->status : 'not_started',
                        'score_percentage' => $attempt?->score_percentage,
                    ];
                });

                $allCompleted = $testsStatus->every(fn ($t) => $t['status'] === 'completed');

                return [
                    'assessment_title' => $assessment->getTranslation('title'),
                    'tests' => $testsStatus,
                    'all_completed' => $allCompleted,
                    'total_tests' => $testsStatus->count(),
                    'completed_tests' => $testsStatus->where('status', 'completed')->count(),
                ];
            })->filter()->values();

            return [
                'id' => $account->uuid,
                'name' => $account->name,
                'email' => $account->email,
                'phone' => $account->phone,
                'company' => $account->company,
                'job_title' => $account->job_title,
                'age' => $account->age,
                'gender' => $account->gender,
                'preferred_locale' => $account->preferred_locale,
                'created_at' => $account->created_at?->toISOString(),
                'last_login_at' => $account->last_login_at?->toISOString(),
                'assessments' => $assessments,
                'total_assessments' => $assessments->count(),
                'completed_assessments' => $assessments->where('all_completed', true)->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => $accounts->items(),
            'meta' => [
                'current_page' => $accounts->currentPage(),
                'last_page' => $accounts->lastPage(),
                'per_page' => $accounts->perPage(),
                'total' => $accounts->total(),
            ],
        ]);
    }

    public function show(ParticipantAccount $participantAccount)
    {
        $participants = $participantAccount->participants()
            ->with(['assessmentLink.assessment.tests', 'attempts.test'])
            ->get();

        $assessments = $participants->map(function ($participant) {
            $link = $participant->assessmentLink;
            if (!$link || !$link->assessment) return null;

            $assessment = $link->assessment;
            $allAttempts = $participant->attempts;

            // Current round attempts (after latest retake grant)
            $latestGrant = $participant->latestRetakeGrant($assessment->id);
            $currentAttempts = $latestGrant
                ? $allAttempts->where('created_at', '>=', $latestGrant->granted_at)
                : $allAttempts;

            // Current round test statuses
            $testsStatus = $assessment->tests->map(function ($test) use ($currentAttempts) {
                $attempt = $currentAttempts->firstWhere('test_id', $test->id);
                return [
                    'attempt_id' => $attempt?->uuid,
                    'test_title' => $test->getTranslation('title'),
                    'status' => $attempt ? $attempt->status : 'not_started',
                    'score_percentage' => $attempt?->score_percentage,
                    'score_raw' => $attempt?->score_raw,
                    'score_max' => $attempt?->score_max,
                    'time_spent_seconds' => $attempt?->time_spent_seconds,
                    'completed_at' => $attempt?->completed_at?->toISOString(),
                ];
            });

            $allCompleted = $testsStatus->every(fn ($t) => $t['status'] === 'completed');

            // Previous rounds (attempts before latest retake grant)
            $previousAttempts = $latestGrant
                ? $allAttempts->where('created_at', '<', $latestGrant->granted_at)->where('status', 'completed')->values()
                : collect();

            $retakeCount = $participant->retakeGrants()
                ->where('assessment_id', $assessment->id)
                ->count();

            return [
                'participant_uuid' => $participant->uuid,
                'assessment_title' => $assessment->getTranslation('title'),
                'tests' => $testsStatus,
                'all_completed' => $allCompleted,
                'total_tests' => $testsStatus->count(),
                'completed_tests' => $testsStatus->where('status', 'completed')->count(),
                'retake_count' => $retakeCount,
                'has_pending_retake' => $latestGrant && !$allCompleted,
                'previous_attempts' => $previousAttempts->map(fn ($a) => [
                    'test_title' => $a->test?->getTranslation('title'),
                    'score_percentage' => $a->score_percentage,
                    'completed_at' => $a->completed_at?->toISOString(),
                ])->values(),
            ];
        })->filter()->values();

        return $this->success([
            'id' => $participantAccount->uuid,
            'name' => $participantAccount->name,
            'email' => $participantAccount->email,
            'phone' => $participantAccount->phone,
            'company' => $participantAccount->company,
            'job_title' => $participantAccount->job_title,
            'age' => $participantAccount->age,
            'gender' => $participantAccount->gender,
            'preferred_locale' => $participantAccount->preferred_locale,
            'created_at' => $participantAccount->created_at?->toISOString(),
            'last_login_at' => $participantAccount->last_login_at?->toISOString(),
            'assessments' => $assessments,
        ]);
    }

    public function grantRetake(Request $request, ParticipantAccount $participantAccount)
    {
        $request->validate([
            'participant_uuid' => ['required', 'string'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $participant = $participantAccount->participants()
            ->where('uuid', $request->input('participant_uuid'))
            ->firstOrFail();

        $assessmentId = $participant->assessmentLink->assessment_id;

        $grant = RetakeGrant::create([
            'participant_id' => $participant->id,
            'assessment_id' => $assessmentId,
            'granted_by' => $request->user()->id,
            'granted_at' => now(),
            'reason' => $request->input('reason'),
        ]);

        return $this->success([
            'id' => $grant->uuid,
            'granted_at' => $grant->granted_at->toISOString(),
        ], 'Retake granted successfully.', 201);
    }

    public function retakeHistory(ParticipantAccount $participantAccount)
    {
        $participantIds = $participantAccount->participants()->pluck('id');

        $grants = RetakeGrant::whereIn('participant_id', $participantIds)
            ->with(['assessment', 'grantedBy'])
            ->orderByDesc('granted_at')
            ->get()
            ->map(fn ($g) => [
                'id' => $g->uuid,
                'assessment_title' => $g->assessment?->getTranslation('title'),
                'granted_by' => $g->grantedBy?->name,
                'granted_at' => $g->granted_at->toISOString(),
                'used_at' => $g->used_at?->toISOString(),
                'reason' => $g->reason,
            ]);

        return $this->success($grants);
    }

    public function combinedResults(ParticipantAccount $participantAccount)
    {
        $participants = $participantAccount->participants()
            ->with(['assessmentLink.assessment', 'attempts' => fn ($q) => $q->completed()->with('test')])
            ->get();

        $results = $participants->map(function ($participant) {
            $link = $participant->assessmentLink;
            if (!$link || !$link->assessment) return null;

            return [
                'assessment_title' => $link->assessment->getTranslation('title'),
                'attempts' => TestAttemptResource::collection($participant->attempts),
            ];
        })->filter()->values();

        return $this->success([
            'participant' => [
                'name' => $participantAccount->name,
                'email' => $participantAccount->email,
                'phone' => $participantAccount->phone,
                'company' => $participantAccount->company,
            'job_title' => $participantAccount->job_title,
                'age' => $participantAccount->age,
                'gender' => $participantAccount->gender,
            ],
            'assessments' => $results,
        ]);
    }

    public function downloadProfile(PdfExportService $pdfExportService, ParticipantAccount $participantAccount)
    {
        return $pdfExportService->participantCombinedReport($participantAccount);
    }

    /**
     * Delete a per-link participant record along with its attempts, responses,
     * and retake grants (cascade). The underlying ParticipantAccount is left
     * intact so the person can still take other assessments.
     */
    public function destroy(Participant $participant)
    {
        $participant->delete();

        return $this->success(['message' => 'Participant deleted.']);
    }

    /**
     * Delete a ParticipantAccount along with all of its per-link Participant
     * records (which cascades through test_attempts, responses, retake_grants).
     */
    public function destroyAccount(ParticipantAccount $participantAccount)
    {
        // Delete each linked Participant so its attempts/responses cascade.
        // The FK on participants.participant_account_id is null-on-delete, so
        // we must do this explicitly instead of relying on the account delete.
        foreach ($participantAccount->participants as $participant) {
            $participant->delete();
        }

        $participantAccount->delete();

        return $this->success(['message' => 'Account deleted.']);
    }
}
