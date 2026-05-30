<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

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
        return ! $user->hasAccess($course->id);
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }

    public function publish(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }

    public function hide(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }
}
