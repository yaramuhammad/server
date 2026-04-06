<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreTestRequest;
use App\Http\Requests\Api\Admin\UpdateTestRequest;
use App\Http\Resources\TestResource;
use App\Http\Resources\TestSummaryResource;
use App\Models\Test;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TestController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Test::class);

        $query = Test::withCount('questions');

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

        $tests = $query->latest()->paginate(20);

        return TestSummaryResource::collection($tests)->additional([
            'success' => true,
            'message' => 'Success',
        ]);
    }

    public function store(StoreTestRequest $request)
    {
        $this->authorize('create', Test::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $test = Test::create($data);

        return $this->success(new TestResource($test), 'Test created.', 201);
    }

    public function show(Test $test)
    {
        $this->authorize('view', $test);

        $test->load('questions')->loadCount('questions');

        return $this->success(new TestResource($test));
    }

    public function update(UpdateTestRequest $request, Test $test)
    {
        $this->authorize('update', $test);

        $data = $request->validated();
        $test->update($data);

        return $this->success(new TestResource($test), 'Test updated.');
    }

    public function destroy(Test $test)
    {
        $this->authorize('delete', $test);

        $test->delete();

        return $this->success(null, 'Test deleted.');
    }
}
