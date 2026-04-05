<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonComment extends Model
{
    protected $fillable = [
        'user_id',
        'course_lesson_id',
        'parent_id',
        'content',
        'is_edited'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    public function replies()
    {
        return $this->hasMany(LessonComment::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

    public function isLikedBy($user)
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
