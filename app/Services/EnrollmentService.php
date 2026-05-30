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
                ]
            );

            if ($paymentId && (int) $enrollment->payment_id !== (int) $paymentId) {
                $enrollment->update(['payment_id' => $paymentId]);
            }

            if (! $enrollment->enrolled_at) {
                $enrollment->update(['enrolled_at' => now()]);
            }

            // Potential for Welcome Email or Notification
            // Mail::to($user->email)->send(new CourseWelcomeMail($course));

            return $enrollment;
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
