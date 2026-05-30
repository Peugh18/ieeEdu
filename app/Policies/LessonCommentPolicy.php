<?php

namespace App\Policies;

use App\Models\LessonComment;
use App\Models\User;

class LessonCommentPolicy
{
    public function update(User $user, LessonComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    public function delete(User $user, LessonComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    public function like(User $user, LessonComment $comment): bool
    {
        $comment->loadMissing('lesson.course');
        $course = $comment->lesson?->course;

        return $course && $user->can('viewClassroom', $course);
    }
}
