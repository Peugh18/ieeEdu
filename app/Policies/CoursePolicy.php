<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;

class CoursePolicy
{
    /**
     * Determina si el usuario puede ver el aula virtual del curso.
     */
    public function viewClassroom(User $user, Course $course): bool
    {
        // 1. Administradores tienen acceso total
        if ($user->role === 'admin') {
            return true;
        }

        // 2. Usuarios con suscripción activa tienen acceso
        if ($user->hasSubscriptionActive()) {
            return true;
        }

        // 3. Cursos gratuitos
        if ($course->price <= 0 || $course->sale_price <= 0) {
            return true;
        }

        // 4. Verificar matrícula explícita
        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
    }

    /**
     * Determina si el usuario puede comprar/inscribirse en el curso.
     */
    public function enroll(User $user, Course $course): bool
    {
        return !Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
    }
}
