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

        $courses = Course::where('status', 'PUBLICADO')->pluck('id');

        foreach ($courses as $courseId) {
            $enrollment = Enrollment::where('user_id', $userId)
                ->where('course_id', $courseId)
                ->first();

            if ($enrollment) {
                // Si fue comprado individualmente: solo activar el flag de acceso,
                // pero NO cambiar subscription_granted para que no sea revocado después.
                if (in_array($courseId, $individualCourseIds)) {
                    $enrollment->update(['subscription_active' => true]);
                } else {
                    $enrollment->update([
                        'subscription_active'  => true,
                        'subscription_granted' => true,
                    ]);
                }
            } else {
                // Nueva inscripción creada por suscripción
                Enrollment::create([
                    'user_id'              => $userId,
                    'course_id'            => $courseId,
                    'enrolled_at'          => now(),
                    'progress'             => 0,
                    'subscription_granted' => true,
                    'subscription_active'  => true,
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
        // IDs de cursos pagados individualmente → nunca revocar
        $individualCourseIds = Payment::where('user_id', $userId)
            ->where('status', 'aprobado')
            ->whereNotNull('course_id')
            ->pluck('course_id')
            ->toArray();

        // Revocar acceso por suscripción sin borrar el historial de progreso
        DB::table('enrollments')
            ->where('user_id', $userId)
            ->where('subscription_granted', true)
            ->whereNotIn('course_id', $individualCourseIds)
            ->update([
                'subscription_active' => false,
                'updated_at'          => now(),
            ]);
    }

    /**
     * Sincroniza el acceso del usuario según el estado actual de sus suscripciones.
     * Úsalo desde cualquier punto (controller, observer, artisan command).
     */
    public function sync(int $userId): void
    {
        $hasActiveSub = Subscription::where('user_id', $userId)
            ->where('status', 'activa')
            ->where('end_date', '>=', now())
            ->exists();

        if ($hasActiveSub) {
            $this->grantAccess($userId);
        } else {
            $this->revokeAccess($userId);
        }
    }
}
