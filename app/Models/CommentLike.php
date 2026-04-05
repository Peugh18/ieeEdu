<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    protected $fillable = ['user_id', 'lesson_comment_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->belongsTo(LessonComment::class, 'lesson_comment_id');
    }
}
