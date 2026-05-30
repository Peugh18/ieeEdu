<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuizRequest;
use App\Models\Course;
use App\Models\CourseQuiz;

class QuizController extends Controller
{
    public function index(Course $course)
    {
        return response()->json($course->quizzes()->with(['questions.answers', 'attempts.user'])->get());
    }

    public function store(StoreQuizRequest $request, Course $course)
    {
        $validated = $request->validated();

        $quiz = $course->quizzes()->create($validated);

        return response()->json($quiz, 201);
    }

    public function update(StoreQuizRequest $request, CourseQuiz $quiz)
    {
        $validated = $request->validated();

        $quiz->update($validated);

        return response()->json($quiz);
    }

    public function destroy(CourseQuiz $quiz)
    {
        $quiz->delete();

        return response()->json(['message' => 'Quiz eliminado']);
    }
}
