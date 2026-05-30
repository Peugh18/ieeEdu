<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'payment_id',
        'progress',
        'last_lesson_id',
        'enrolled_at',
        'completed_at',
        'passed',
        'subscription_granted',
        'subscription_active',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'passed' => 'boolean',
        'subscription_granted' => 'boolean',
        'subscription_active' => 'boolean',
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

    public function lastLesson()
    {
        return $this->belongsTo(CourseLesson::class, 'last_lesson_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Determina si esta inscripción otorga acceso real en este momento.
     */
    public function hasActiveAccess(): bool
    {
        // Si no fue otorgada por suscripción, siempre es válida (compra individual)
        if (! $this->subscription_granted) {
            return true;
        }

        // Si fue por suscripción, la suscripción del usuario debe estar activa
        return $this->user && $this->user->hasSubscriptionActive();
    }
}
