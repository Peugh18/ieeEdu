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
        return $user->hasAccess($course->id);
    }

    /**
     * Determina si el usuario puede comprar/inscribirse en el curso.
     */
    public function enroll(User $user, Course $course): bool
    {
        return !$user->hasAccess($course->id);
    }
}
