<?php

namespace App\Http\Controllers\Api\Participant;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestAttemptResource;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\ParticipantAccount;
use App\Services\PdfExportService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParticipantPortalController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:participant_accounts,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:1', 'max:150'],
            'gender' => ['nullable', 'string', 'in:male,female,other,prefer_not_to_say'],
        ]);

        $account = ParticipantAccount::create($validated);

        // Link any existing participant records with this email
        Participant::where('email', $account->email)
            ->whereNull('participant_account_id')
            ->update(['participant_account_id' => $account->id]);

        $token = $account->createToken('portal')->plainTextToken;

        return $this->success([
            'token' => $token,
            'account' => $this->formatAccount($account),
        ], 'Account created.', 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $account = ParticipantAccount::where('email', $request->email)->first();

        if (!$account || !Hash::check($request->password, $account->password)) {
            return $this->error('Invalid email or password.', 401);
        }

        $account->update(['last_login_at' => now()]);

        $token = $account->createToken('portal')->plainTextToken;

        return $this->success([
            'token' => $token,
            'account' => $this->formatAccount($account),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user('participant')->currentAccessToken()->delete();

        return $this->success(null, 'Logged out.');
    }

    public function me(Request $request)
    {
        return $this->success($this->formatAccount($request->user('participant')));
    }

    public function assessments(Request $request)
    {
        $account = $request->user('participant');

        $participants = $account->participants()
            ->with(['assessmentLink.assessment.tests', 'attempts.test'])
            ->get();

        $assessments = $participants->map(function ($participant) {
            $link = $participant->assessmentLink;
            if (!$link || !$link->assessment) return null;

            $assessment = $link->assessment;
            $attempts = $participant->attempts;

            $testsStatus = $assessment->tests->map(function ($test) use ($attempts) {
                $attempt = $attempts->firstWhere('test_id', $test->id);
                return [
                    'test_id' => $test->uuid,
                    'test_title' => $test->getTranslation('title'),
                    'status' => $attempt ? $attempt->status : 'not_started',
                    'score_percentage' => $attempt?->score_percentage,
                ];
            });

            $allCompleted = $testsStatus->every(fn ($t) => $t['status'] === 'completed');

            return [
                'participant_uuid' => $participant->uuid,
                'assessment_title' => $assessment->getTranslation('title'),
                'assessment_description' => $assessment->getTranslation('description'),
                'link_token' => $link->token,
                'tests' => $testsStatus,
                'all_completed' => $allCompleted,
                'total_tests' => $testsStatus->count(),
                'completed_tests' => $testsStatus->where('status', 'completed')->count(),
            ];
        })->filter()->values();

        return $this->success($assessments);
    }

    public function assignLink(Request $request, string $token)
    {
        $account = $request->user('participant');

        $link = AssessmentLink::where('token', $token)
            ->with('assessment')
            ->firstOrFail();

        if (!$link->isAccessible()) {
            return $this->error('This assessment link is no longer accessible.', 403);
        }

        // Check if already assigned
        $existing = $account->participants()
            ->where('assessment_link_id', $link->id)
            ->first();

        if ($existing) {
            return $this->success([
                'participant_uuid' => $existing->uuid,
                'already_assigned' => true,
            ]);
        }

        // Create participant record linked to account
        $participant = Participant::create([
            'assessment_link_id' => $link->id,
            'participant_account_id' => $account->id,
            'name' => $account->name,
            'email' => $account->email,
            'phone' => $account->phone,
            'company' => $account->company,
            'job_title' => $account->job_title,
            'age' => $account->age,
            'gender' => $account->gender,
            'locale' => $account->preferred_locale,
        ]);

        return $this->success([
            'participant_uuid' => $participant->uuid,
            'already_assigned' => false,
        ], 'Assessment assigned.', 201);
    }

    public function combinedResults(Request $request)
    {
        $account = $request->user('participant');

        $participants = $account->participants()
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

        return $this->success($results);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $account = ParticipantAccount::where('email', $request->email)->first();

        if (!$account) {
            return $this->success(null, 'If an account exists with that email, a reset token has been generated.');
        }

        DB::table('participant_password_reset_tokens')->where('email', $request->email)->delete();

        $token = Str::random(64);

        DB::table('participant_password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        return $this->success([
            'token' => $token,
        ], 'Reset token generated.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $record = DB::table('participant_password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return $this->error('Invalid or expired reset token.', 422);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('participant_password_reset_tokens')->where('email', $request->email)->delete();
            return $this->error('Reset token has expired.', 422);
        }

        $account = ParticipantAccount::where('email', $request->email)->first();

        if (!$account) {
            return $this->error('Account not found.', 404);
        }

        $account->update(['password' => $request->password]);

        DB::table('participant_password_reset_tokens')->where('email', $request->email)->delete();

        return $this->success(null, 'Password reset successfully.');
    }

    public function downloadProfile(Request $request, PdfExportService $pdfExportService)
    {
        $account = $request->user('participant');

        return $pdfExportService->participantCombinedReport($account);
    }

    private function formatAccount(ParticipantAccount $account): array
    {
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
        ];
    }
}
