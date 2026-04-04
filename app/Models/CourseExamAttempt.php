<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseExamAttempt extends Model
{
    //
    protected $fillable = [
        'user_id',
        'course_quiz_id',
        'status',
        'score',
        'answers_data',
        'completed_at',
    ];

    protected $casts = [
        'answers_data' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(CourseQuiz::class, 'course_quiz_id');
    }
}
