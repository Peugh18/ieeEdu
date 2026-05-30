<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    /**
     * Enroll a student in a course.
     */
    public function enroll(User $user, Course $course, $paymentId = null)
    {
        return DB::transaction(function () use ($user, $course, $paymentId) {
            $enrollment = Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                [
                    'payment_id' => $paymentId,
                    'enrolled_at' => now(),
                    'progress' => 0,
                    'subscription_granted' => false,
                ]
            );

            $updates = [];

            if ($paymentId) {
                $updates['payment_id'] = $paymentId;
                $updates['subscription_granted'] = false;
                $updates['subscription_active'] = true;
            } elseif ($course->retainsAccessAfterSubscriptionEnds() || $course->effectivePrice() <= 0) {
                $updates['subscription_granted'] = false;
                $updates['subscription_active'] = true;
            }

            if (! $enrollment->enrolled_at) {
                $updates['enrolled_at'] = now();
            }

            if ($updates !== []) {
                $enrollment->update($updates);
            }

            return $enrollment->fresh();
        });
    }

    /**
     * Terminate or cancel an enrollment.
     */
    public function unenroll(User $user, Course $course)
    {
        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->delete();
    }
}
