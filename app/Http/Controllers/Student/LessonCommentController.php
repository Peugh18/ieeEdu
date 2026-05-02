<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\LessonComment;
use App\Models\CommentLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonCommentController extends Controller
{
    public function store(Request $request, CourseLesson $lesson)
    {
        $this->authorize('viewClassroom', $lesson->course);

        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:lesson_comments,id'
        ]);

        $comment = LessonComment::create([
            'user_id' => Auth::id(),
            'course_lesson_id' => $lesson->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Comentario publicado.');
    }

    public function update(Request $request, LessonComment $comment)
    {
        $this->authorizeUser($comment);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $request->content,
            'is_edited' => true,
        ]);

        return back()->with('success', 'Comentario actualizado.');
    }

    public function destroy(LessonComment $comment)
    {
        $this->authorizeUser($comment);
        $comment->delete();

        return back()->with('success', 'Comentario eliminado.');
    }

    public function toggleLike(LessonComment $comment)
    {
        $user_id = Auth::id();
        $like = CommentLike::where('user_id', $user_id)
            ->where('lesson_comment_id', $comment->id)
            ->first();

        if ($like) {
            $like->delete();
        } else {
            CommentLike::create([
                'user_id' => $user_id,
                'lesson_comment_id' => $comment->id,
            ]);
        }

        return back();
    }

    protected function authorizeUser(LessonComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }
    }
}
