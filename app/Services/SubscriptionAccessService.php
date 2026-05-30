<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

/**
 * SubscriptionAccessService
 *
 * Único punto de verdad para gestionar el acceso a cursos por suscripción.
 * Separa la responsabilidad de la lógica de acceso del controlador y el observer.
 */
class SubscriptionAccessService
{
    /**
     * Activa el acceso a todos los cursos publicados para un usuario.
     * Preserva el progreso ya existente y no duplica inscripciones individuales.
     */
    public function grantAccess(int $userId): void
    {
        // Cursos pagados individualmente → nunca cambiar su subscription_granted
        $individualCourseIds = Payment::where('user_id', $userId)
            ->where('status', 'aprobado')
            ->whereNotNull('course_id')
            ->pluck('course_id')
            ->toArray();

        $courses = Course::where('status', 'PUBLICADO')->get();

        foreach ($courses as $course) {
            // Masterclass = gancho de marketing, no depende de la suscripción
            if ($course->retainsAccessAfterSubscriptionEnds()) {
                $this->ensurePermanentEnrollment($userId, $course->id);

                continue;
            }

            $enrollment = Enrollment::where('user_id', $userId)
                ->where('course_id', $course->id)
                ->first();

            if ($enrollment) {
                if (in_array($course->id, $individualCourseIds)) {
                    $enrollment->update(['subscription_active' => true]);
                } else {
                    $enrollment->update([
                        'subscription_active' => true,
                        'subscription_granted' => true,
                    ]);
                }
            } else {
                Enrollment::create([
                    'user_id' => $userId,
                    'course_id' => $course->id,
                    'enrolled_at' => now(),
                    'progress' => 0,
                    'subscription_granted' => true,
                    'subscription_active' => true,
                ]);
            }
        }
    }

    /**
     * Revoca el acceso a cursos otorgados por suscripción.
     *
     * ✅ Preserva el progreso (no borra la fila).
     * ✅ Respeta los cursos comprados individualmente.
     * ✅ Solo desactiva el acceso de suscripción marcando subscription_active = false.
     */
    public function revokeAccess(int $userId): void
    {
        $individualCourseIds = Payment::where('user_id', $userId)
            ->where('status', 'aprobado')
            ->whereNotNull('course_id')
            ->pluck('course_id')
            ->toArray();

        $masterclassCourseIds = Course::whereIn('type', ['evento', 'masterclass'])
            ->pluck('id')
            ->toArray();

        // Masterclasses visitadas con Premium → convertir a acceso permanente
        if ($masterclassCourseIds !== []) {
            DB::table('enrollments')
                ->where('user_id', $userId)
                ->where('subscription_granted', true)
                ->whereIn('course_id', $masterclassCourseIds)
                ->update([
                    'subscription_granted' => false,
                    'subscription_active' => true,
                    'updated_at' => now(),
                ]);
        }

        DB::table('enrollments')
            ->where('user_id', $userId)
            ->where('subscription_granted', true)
            ->whereNotIn('course_id', $individualCourseIds)
            ->whereNotIn('course_id', $masterclassCourseIds)
            ->update([
                'subscription_active' => false,
                'updated_at' => now(),
            ]);
    }

    /**
     * Inscripción permanente (no revocable al expirar Premium).
     */
    protected function ensurePermanentEnrollment(int $userId, int $courseId): void
    {
        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $userId, 'course_id' => $courseId],
            [
                'enrolled_at' => now(),
                'progress' => 0,
                'subscription_granted' => false,
            ]
        );

        if ($enrollment->subscription_granted) {
            $enrollment->update([
                'subscription_granted' => false,
                'subscription_active' => true,
            ]);
        }
    }

    /**
     * Sincroniza el acceso del usuario según el estado actual de sus suscripciones.
     * Úsalo desde cualquier punto (controller, observer, artisan command).
     */
    public function sync(int $userId): void
    {
        $hasActiveSub = Subscription::where('user_id', $userId)
            ->where('status', Subscription::STATUS_ACTIVE)
            ->where('end_date', '>=', now())
            ->exists();

        if ($hasActiveSub) {
            $this->grantAccess($userId);
        } else {
            $this->revokeAccess($userId);
        }
    }
}
