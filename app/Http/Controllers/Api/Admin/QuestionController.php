<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderQuestionsRequest;
use App\Http\Requests\Api\Admin\StoreQuestionRequest;
use App\Http\Requests\Api\Admin\UpdateQuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Test;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    use ApiResponse;

    public function index(Test $test)
    {
        $this->authorize('view', $test);

        $questions = $test->questions()->orderBy('sort_order')->get();

        return $this->success(QuestionResource::collection($questions));
    }

    public function store(StoreQuestionRequest $request, Test $test)
    {
        $this->authorize('update', $test);

        $data = $request->validated();
        $data['test_id'] = $test->id;

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $test->questions()->max('sort_order') + 1;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store("questions/{$test->id}", 'public');
            unset($data['image']);
        }

        $question = Question::create($data);

        return $this->success(new QuestionResource($question), 'Question created.', 201);
    }

    public function update(UpdateQuestionRequest $request, Test $test, Question $question)
    {
        $this->authorize('update', $test);

        if ($question->test_id !== $test->id) {
            return $this->error('Question does not belong to this test.', 404);
        }

        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            $data['image_path'] = $request->file('image')->store("questions/{$test->id}", 'public');
            unset($data['image']);
        }

        $question->update($data);

        return $this->success(new QuestionResource($question), 'Question updated.');
    }

    public function destroy(Test $test, Question $question)
    {
        $this->authorize('update', $test);

        if ($question->test_id !== $test->id) {
            return $this->error('Question does not belong to this test.', 404);
        }

        $question->delete();

        return $this->success(null, 'Question deleted.');
    }

    public function reorder(ReorderQuestionsRequest $request, Test $test)
    {
        $this->authorize('update', $test);

        foreach ($request->validated('questions') as $item) {
            Question::where('id', $item['id'])
                ->where('test_id', $test->id)
                ->update(['sort_order' => $item['sort_order']]);
        }

        return $this->success(null, 'Questions reordered.');
    }
}
