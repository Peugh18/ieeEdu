<?php

namespace App\Models;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'progress',
        'last_lesson_id',
        'enrolled_at',
        'completed_at',
        'passed',
        'subscription_granted',
        'subscription_active',
    ];

    protected $casts = [
        'enrolled_at'          => 'datetime',
        'completed_at'         => 'datetime',
        'passed'               => 'boolean',
        'subscription_granted' => 'boolean',
        'subscription_active'  => 'boolean',
    ];

    public function scopeVisible($query)
    {
        return $query->where(function ($q) {
            $q->where('subscription_granted', false)
              ->orWhere(function ($q2) {
                  $q2->where('subscription_granted', true)
                     ->where('subscription_active', true);
              });
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
