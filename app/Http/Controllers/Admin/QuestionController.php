<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseQuestion;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'quiz_id' => 'required|exists:course_quizzes,id',
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false',
            'points' => 'required|integer|min:1',
            'answers' => 'required|array|min:2',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'boolean',
        ]);

        $question = CourseQuestion::create($request->only(['quiz_id', 'question', 'type', 'points']));
        foreach ($validated['answers'] as $answer) {
            $question->answers()->create($answer);
        }

        return response()->json($question->load('answers'), 201);
    }

    public function update(Request $request, CourseQuestion $question)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false',
            'points' => 'required|integer|min:1',
            'answers' => 'required|array|min:2',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'boolean',
        ]);

        $question->update($request->only(['question', 'type', 'points']));

        $question->answers()->delete();
        foreach ($validated['answers'] as $answer) {
            $question->answers()->create($answer);
        }

        return response()->json($question->load('answers'));
    }

    public function destroy(CourseQuestion $question)
    {
        $question->delete();
        return response()->json(['message' => 'Pregunta eliminada']);
    }
}
