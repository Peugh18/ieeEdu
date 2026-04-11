<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Slugifiable;

class Course extends Model
{
    use HasFactory, Slugifiable;

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
        'instructor_id',
        'docente_id',
        'image',
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

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
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

    public function certificateTemplate()
    {
        return $this->hasOne(CertificateTemplate::class);
    }

    /**
     * SCOPES
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'PUBLICADO');
    }
}

