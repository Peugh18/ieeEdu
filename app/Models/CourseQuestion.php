<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['quiz_id', 'question', 'type', 'points'];

    public function quiz()
    {
        return $this->belongsTo(CourseQuiz::class, 'quiz_id');
    }

    public function answers()
    {
        return $this->hasMany(CourseAnswer::class, 'question_id');
    }
}
