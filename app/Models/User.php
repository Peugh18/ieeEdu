<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
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

    /**
     * Get the user's current active subscription.
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', Subscription::STATUS_ACTIVE)
            ->where('end_date', '>=', now())
            ->latest();
    }

    public function hasSubscriptionActive()
    {
        return $this->subscriptions()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->where('end_date', '>=', now())
            ->exists();
    }

    public function hasAccess($courseId)
    {
        if ($this->role === 'admin') {
            return true;
        }

        if ($this->hasSubscriptionActive()) {
            return true;
        }

        $course = Course::find($courseId);
        if ($course && $course->effectivePrice() <= 0) {
            return true;
        }

        return $this->hasPermanentCourseAccess($courseId);
    }

    /**
     * Acceso que persiste aunque expire Premium (compra, gratis inscrito, masterclass).
     */
    public function hasPermanentCourseAccess(int $courseId): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        if (Payment::where('user_id', $this->id)
            ->where('course_id', $courseId)
            ->where('status', 'aprobado')
            ->exists()) {
            return true;
        }

        return $this->enrollments()
            ->where('course_id', $courseId)
            ->where('subscription_granted', false)
            ->exists();
    }

    public function hasBookAccess(int $bookId): bool
    {
        return $this->hasApprovedBookPayment($bookId);
    }

    public function hasApprovedBookPayment(int $bookId): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        return Payment::where('user_id', $this->id)
            ->where('book_id', $bookId)
            ->where('status', 'aprobado')
            ->exists();
    }

    public function hasPendingBookPayment(int $bookId): bool
    {
        return Payment::where('user_id', $this->id)
            ->where('book_id', $bookId)
            ->whereIn('status', ['pendiente', 'en_revision'])
            ->exists();
    }
}
