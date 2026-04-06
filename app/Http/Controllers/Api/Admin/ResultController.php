<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParticipantResource;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\TestAttemptResource;
use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Response;
use App\Models\TestAttempt;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    use ApiResponse;

    public function assessmentResults(Request $request, Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $participants = $assessment->links()
            ->with(['participants' => fn ($q) => $q->with(['attempts' => fn ($a) => $a->with('test')])])
            ->get()
            ->pluck('participants')
            ->flatten();

        return $this->success(ParticipantResource::collection($participants));
    }

    public function linkResults(Request $request, Assessment $assessment, AssessmentLink $link)
    {
        $this->authorize('view', $assessment);

        if ($link->assessment_id !== $assessment->id) {
            return $this->error('Link does not belong to this assessment.', 404);
        }

        $participants = $link->participants()
            ->with(['attempts' => fn ($q) => $q->with('test')])
            ->paginate(20);

        return ParticipantResource::collection($participants)->additional([
            'success' => true,
            'message' => 'Success',
        ]);
    }

    public function attemptDetail(Request $request, TestAttempt $attempt)
    {
        $this->authorize('view', $attempt);

        $attempt->load(['test', 'participant']);

        return $this->success([
            'attempt' => new TestAttemptResource($attempt),
            'participant' => new ParticipantResource($attempt->participant),
        ]);
    }

    public function attemptResponses(Request $request, TestAttempt $attempt)
    {
        $this->authorize('view', $attempt);

        $responses = $attempt->responses()->with('question')->get();

        return $this->success(ResponseResource::collection($responses));
    }

    public function assessmentAnalytics(Request $request, Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $attemptsQuery = TestAttempt::completed()->where('assessment_id', $assessment->id);

        // Score Distribution
        $scoreBuckets = (clone $attemptsQuery)
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

        // Test Performance
        $testPerformance = (clone $attemptsQuery)
            ->select(
                'test_id',
                DB::raw('AVG(score_percentage) as avg_score'),
                DB::raw('MIN(score_percentage) as min_score'),
                DB::raw('MAX(score_percentage) as max_score'),
                DB::raw('COUNT(*) as attempts_count')
            )
            ->groupBy('test_id')
            ->get()
            ->map(function ($row) {
                $test = \App\Models\Test::find($row->test_id);
                return [
                    'test_id' => $row->test_id,
                    'test_title' => $test?->getTranslation('title') ?? '—',
                    'avg_score' => round((float) $row->avg_score, 1),
                    'min_score' => round((float) $row->min_score, 1),
                    'max_score' => round((float) $row->max_score, 1),
                    'attempts_count' => (int) $row->attempts_count,
                ];
            });

        // Completion Stats
        $totalStarted = TestAttempt::where('assessment_id', $assessment->id)->count();
        $totalCompleted = (clone $attemptsQuery)->count();
        $completionRate = $totalStarted > 0 ? round(($totalCompleted / $totalStarted) * 100, 1) : 0;
        $avgTime = (clone $attemptsQuery)->avg('time_spent_seconds');

        // Question Stats
        $questionStats = Response::select(
                'question_id',
                DB::raw('AVG(scored_value) as avg_score'),
                DB::raw('COUNT(*) as response_count')
            )
            ->whereHas('attempt', fn ($q) => $q->completed()->where('assessment_id', $assessment->id))
            ->groupBy('question_id')
            ->get()
            ->map(function ($row) {
                $question = \App\Models\Question::find($row->question_id);
                return [
                    'question_id' => $row->question_id,
                    'question_text' => $question?->getTranslation('text') ?? '—',
                    'avg_score' => round((float) $row->avg_score, 2),
                    'response_count' => (int) $row->response_count,
                ];
            });

        $highest = $questionStats->sortByDesc('avg_score')->take(5)->values();
        $lowest = $questionStats->sortBy('avg_score')->take(5)->values();

        return $this->success([
            'score_distribution' => $scoreDistribution,
            'test_performance' => $testPerformance,
            'completion_rate' => $completionRate,
            'total_started' => $totalStarted,
            'total_completed' => $totalCompleted,
            'avg_time_seconds' => (int) ($avgTime ?? 0),
            'question_stats' => [
                'highest' => $highest,
                'lowest' => $lowest,
            ],
        ]);
    }
}
