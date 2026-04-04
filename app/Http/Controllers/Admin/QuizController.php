<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseQuiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Course $course)
    {
        return response()->json($course->quizzes()->with(['questions.answers', 'attempts.user'])->get());
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'time_limit' => 'required|integer|min:0',
            'max_attempts' => 'required|integer|min:1',
            'minimum_score' => 'required|numeric|min:0|max:100',
            'randomize_questions' => 'boolean',
        ]);

        $quiz = $course->quizzes()->create($validated);

        return response()->json($quiz, 201);
    }

    public function update(Request $request, CourseQuiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'time_limit' => 'required|integer|min:0',
            'max_attempts' => 'required|integer|min:1',
            'minimum_score' => 'required|numeric|min:0|max:100',
            'randomize_questions' => 'boolean',
        ]);

        $quiz->update($validated);

        return response()->json($quiz);
    }

    public function destroy(CourseQuiz $quiz)
    {
        $quiz->delete();
        return response()->json(['message' => 'Quiz eliminado']);
    }
}
