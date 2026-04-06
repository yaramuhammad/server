<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Traits\ApiResponse;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ApiResponse;

    public function stats(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdmin();

        $testsQuery = Test::query();
        $assessmentsQuery = Assessment::query();
        $linksQuery = AssessmentLink::query();
        $participantsQuery = Participant::query();

        if (!$isSuperAdmin) {
            $testsQuery->ownedBy($user);
            $assessmentsQuery->ownedBy($user);
            $linksQuery->whereHas('assessment', fn ($q) => $q->ownedBy($user));
            $participantsQuery->whereHas('assessmentLink.assessment', fn ($q) => $q->ownedBy($user));
        }

        return $this->success([
            'total_tests' => $testsQuery->count(),
            'total_assessments' => $assessmentsQuery->count(),
            'active_links' => (clone $linksQuery)->where('is_active', true)->count(),
            'total_participants' => $participantsQuery->count(),
        ]);
    }

    public function recentActivity(Request $request)
    {
        $user = $request->user();

        $query = TestAttempt::with(['participant', 'test'])
            ->where('status', 'completed')
            ->orderByDesc('completed_at')
            ->limit(10);

        if (!$user->isSuperAdmin()) {
            $query->whereHas('participant.assessmentLink.assessment', fn ($q) => $q->ownedBy($user));
        }

        $attempts = $query->get();

        $activity = $attempts->map(fn ($attempt) => [
            'id' => $attempt->uuid,
            'participant_name' => $attempt->participant?->name ?? '—',
            'test_title' => $attempt->test?->getTranslation('title') ?? '—',
            'score' => $attempt->score,
            'completed_at' => $attempt->completed_at?->toISOString(),
        ]);

        return $this->success($activity);
    }

    public function charts(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdmin();

        // Score Distribution
        $scoreQuery = TestAttempt::completed();
        if (!$isSuperAdmin) {
            $scoreQuery->whereHas('participant.assessmentLink.assessment', fn ($q) => $q->ownedBy($user));
        }
        $scoreBuckets = (clone $scoreQuery)
            ->select(DB::raw('FLOOR(score_percentage / 10) * 10 as bucket'), DB::raw('COUNT(*) as count'))
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->pluck('count', 'bucket')
            ->toArray();

        $scoreDistribution = [];
        for ($i = 0; $i < 100; $i += 10) {
            $scoreDistribution[] = [
                'range' => $i . '-' . ($i + 10),
                'count' => $scoreBuckets[$i] ?? 0,
            ];
        }

        // Completion Trend (last 30 days)
        $completionQuery = TestAttempt::completed()
            ->where('completed_at', '>=', now()->subDays(30));
        if (!$isSuperAdmin) {
            $completionQuery->whereHas('participant.assessmentLink.assessment', fn ($q) => $q->ownedBy($user));
        }
        $completionRaw = $completionQuery
            ->select(DB::raw('DATE(completed_at) as date'), DB::raw('COUNT(*) as completions'))
            ->groupBy('date')
            ->pluck('completions', 'date')
            ->toArray();

        $completionTrend = [];
        foreach (CarbonPeriod::create(now()->subDays(29), now()) as $date) {
            $key = $date->format('Y-m-d');
            $completionTrend[] = [
                'date' => $key,
                'completions' => $completionRaw[$key] ?? 0,
            ];
        }

        // Assessment Popularity (top 10)
        $popularQuery = Assessment::query()
            ->select('assessments.*')
            ->selectSub(
                Participant::selectRaw('COUNT(DISTINCT participants.id)')
                    ->join('assessment_links', 'assessment_links.id', '=', 'participants.assessment_link_id')
                    ->whereColumn('assessment_links.assessment_id', 'assessments.id'),
                'participants_count'
            );
        if (!$isSuperAdmin) {
            $popularQuery->ownedBy($user);
        }
        $popular = $popularQuery
            ->orderByDesc('participants_count')
            ->limit(10)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->uuid,
                'title' => $a->getTranslation('title'),
                'participants_count' => (int) $a->participants_count,
            ]);

        // Participant Activity (last 30 days)
        $participantQuery = Participant::where('created_at', '>=', now()->subDays(30));
        if (!$isSuperAdmin) {
            $participantQuery->whereHas('assessmentLink.assessment', fn ($q) => $q->ownedBy($user));
        }
        $participantRaw = $participantQuery
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $participantActivity = [];
        foreach (CarbonPeriod::create(now()->subDays(29), now()) as $date) {
            $key = $date->format('Y-m-d');
            $participantActivity[] = [
                'date' => $key,
                'count' => $participantRaw[$key] ?? 0,
            ];
        }

        return $this->success([
            'score_distribution' => $scoreDistribution,
            'completion_trend' => $completionTrend,
            'assessment_popularity' => $popular,
            'participant_activity' => $participantActivity,
        ]);
    }
}
