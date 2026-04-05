<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
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
                    'status' => 'activo',
                    'progress' => 0,
                ]
            );

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
