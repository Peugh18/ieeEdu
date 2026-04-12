<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telefono',
        'role',
        'status',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'docente_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot([
                'enrolled_at', 'completed_at', 'passed',
                'progress', 'last_lesson_id',
                'subscription_granted', 'subscription_active',
            ])
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function hasSubscriptionActive()
    {
        return $this->subscriptions()
                    ->where('status', 'activa')
                    ->where('end_date', '>=', now())
                    ->exists();
    }

    public function hasAccess($courseId)
    {
        // Administradores tienen acceso total
        if ($this->role === 'admin') {
            return true;
        }

        // Si tiene suscripción premium activa, accede a todo
        if ($this->hasSubscriptionActive()) {
            return true;
        }

        // Cursos gratuitos
        $course = Course::find($courseId);
        if ($course && $course->price <= 0) {
            return true;
        }

        // Verificar inscripción (debe ser compra individual o suscripción activa)
        return $this->enrollments()
            ->where('course_id', $courseId)
            ->where(function ($q) {
                $q->where('subscription_granted', false)
                  ->orWhere('subscription_active', true);
            })
            ->exists();
    }
}

