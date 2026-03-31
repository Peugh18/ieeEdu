<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseQuiz extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'title', 'time_limit', 'max_attempts', 'minimum_score', 'randomize_questions'];

    protected $casts = [
        'randomize_questions' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(CourseQuestion::class);
    }
}
