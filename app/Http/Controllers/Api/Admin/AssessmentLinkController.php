<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreAssessmentLinkRequest;
use App\Http\Requests\Api\Admin\UpdateAssessmentLinkRequest;
use App\Http\Resources\AssessmentLinkResource;
use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\TestAttempt;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentLinkController extends Controller
{
    use ApiResponse;

    public function index(Request $request, Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $query = $assessment->links()->withCount('participants');

        if (!$request->user()->isSuperAdmin()) {
            $query->where('created_by', $request->user()->id);
        }

        $links = $query->latest()->get();

        // A participant has "completed" a link's assessment once they have a
        // completed attempt for every test on the assessment. Computed here,
        // once, for all links at once — instead of the previous approach of
        // the client fetching every link's full participant+attempt list
        // just to derive this count (an O(links) fetch waterfall).
        $testCount = $assessment->tests()->count();
        $completedCounts = [];

        if ($testCount > 0 && $links->isNotEmpty()) {
            $completedCounts = TestAttempt::query()
                ->whereIn('assessment_link_id', $links->pluck('id'))
                ->where('status', 'completed')
                ->select('assessment_link_id', 'participant_id')
                ->groupBy('assessment_link_id', 'participant_id')
                ->havingRaw('COUNT(*) >= ?', [$testCount])
                ->get()
                ->groupBy('assessment_link_id')
                ->map(fn ($rows) => $rows->count());
        }

        $links->each(function (AssessmentLink $link) use ($completedCounts) {
            $link->completed_participants_count = $completedCounts->get($link->id, 0);
        });

        return $this->success(AssessmentLinkResource::collection($links));
    }

    public function store(StoreAssessmentLinkRequest $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        $data = $request->validated();
        $data['assessment_id'] = $assessment->id;
        $data['created_by'] = $request->user()->id;

        $link = AssessmentLink::create($data);
        $link->loadCount('participants');

        return $this->success(new AssessmentLinkResource($link), 'Assessment link created.', 201);
    }

    public function show(Assessment $assessment, AssessmentLink $link)
    {
        $this->authorize('view', $link);

        if ($link->assessment_id !== $assessment->id) {
            return $this->error('Link does not belong to this assessment.', 404);
        }

        $link->loadCount('participants');

        return $this->success(new AssessmentLinkResource($link));
    }

    public function update(UpdateAssessmentLinkRequest $request, Assessment $assessment, AssessmentLink $link)
    {
        $this->authorize('update', $link);

        if ($link->assessment_id !== $assessment->id) {
            return $this->error('Link does not belong to this assessment.', 404);
        }

        $link->update($request->validated());
        $link->loadCount('participants');

        return $this->success(new AssessmentLinkResource($link), 'Assessment link updated.');
    }

    public function destroy(Assessment $assessment, AssessmentLink $link)
    {
        $this->authorize('delete', $link);

        if ($link->assessment_id !== $assessment->id) {
            return $this->error('Link does not belong to this assessment.', 404);
        }

        $link->delete();

        return $this->success(null, 'Assessment link deleted.');
    }
}
