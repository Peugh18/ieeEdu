<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'sale_price',
        'discount',
        'duration_weeks',
        'is_featured',
        'certificate_enabled',
        'type',
        'status',
        'category_id',
        'docente_id',
        'image',
        'instructor_name',
        'instructor_title',
        'instructor_bio',
        'instructor_image',
        'start_date',
        'start_time',
        'class_hours',
        'whatsapp_link',
        'objectives',
        'requirements',
    ];

    protected $casts = [
        'price'      => 'float',
        'sale_price' => 'float',
        'discount'   => 'float',
        'is_featured'         => 'boolean',
        'certificate_enabled' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order');
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class)->orderBy('sort_order');
    }

    public function quizzes()
    {
        return $this->hasMany(CourseQuiz::class);
    }
}

