<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\AttachTestsRequest;
use App\Http\Requests\Api\Admin\StoreAssessmentRequest;
use App\Http\Requests\Api\Admin\UpdateAssessmentRequest;
use App\Http\Resources\AssessmentResource;
use App\Models\Assessment;
use App\Models\Test;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Assessment::class);

        $query = Assessment::withCount(['tests', 'links']);

        if (!$request->user()->isSuperAdmin()) {
            $query->ownedBy($request->user());
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $assessments = $query->latest()->paginate(20);

        return AssessmentResource::collection($assessments)->additional([
            'success' => true,
            'message' => 'Success',
        ]);
    }

    public function store(StoreAssessmentRequest $request)
    {
        $this->authorize('create', Assessment::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $assessment = Assessment::create($data);

        return $this->success(new AssessmentResource($assessment), 'Assessment created.', 201);
    }

    public function show(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $assessment->load(['tests' => fn ($q) => $q->withCount('questions')])
            ->loadCount(['tests', 'links']);

        return $this->success(new AssessmentResource($assessment));
    }

    public function update(UpdateAssessmentRequest $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        $assessment->update($request->validated());
        $assessment->loadCount(['tests', 'links']);

        return $this->success(new AssessmentResource($assessment), 'Assessment updated.');
    }

    public function destroy(Assessment $assessment)
    {
        $this->authorize('delete', $assessment);

        $assessment->delete();

        return $this->success(null, 'Assessment deleted.');
    }

    public function attachTests(AttachTestsRequest $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        $syncData = [];
        foreach ($request->validated('tests') as $item) {
            $testId = Test::where('uuid', $item['uuid'])->value('id');
            if ($testId) {
                $syncData[$testId] = ['sort_order' => $item['sort_order'] ?? 0];
            }
        }

        $assessment->tests()->syncWithoutDetaching($syncData);
        $assessment->load(['tests' => fn ($q) => $q->withCount('questions')])
            ->loadCount('tests');

        return $this->success(new AssessmentResource($assessment), 'Tests attached.');
    }

    public function detachTest(Assessment $assessment, Test $test)
    {
        $this->authorize('update', $assessment);

        $assessment->tests()->detach($test->id);
        $assessment->loadCount('tests');

        return $this->success(null, 'Test detached.');
    }
}
