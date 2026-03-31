<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['lesson_id', 'title', 'kind', 'file_path', 'external_url'];

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id');
    }
}
