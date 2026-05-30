<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LiveClassController extends Controller
{
    /**
     * Clases en Vivo para el estudiante
     */
    public function liveClasses()
    {
        $user = Auth::user();
        $enrolledIds = Enrollment::where('user_id', $user->id)->visible()->pluck('course_id');

        $liveLessons = CourseLesson::whereIn('course_id', $enrolledIds)
            ->whereNull('video_url') // Si tiene grabación, no va en calendario de vivos
            ->whereNotNull('start_time')
            ->where('start_time', '>=', now()->subHours(5))
            ->with('course')
            ->orderBy('start_time')
            ->get()
            ->map(fn ($l) => [
                'id' => $l->id,
                'title' => $l->title,
                'day' => $l->start_time->format('D'),
                'date' => $l->start_time->format('Y-m-d'),
                'time' => $l->start_time->format('h:i A'),
                'course_title' => $l->course->title ?? '',
                'course_slug' => $l->course->slug ?? '',
                'course_id' => $l->course_id,
                'is_today' => $l->start_time->isToday(),
                'live_link' => $l->live_link,
            ])->values(); // Asegurar que sea array secuencial en JS

        return Inertia::render('student/LiveClasses', [
            'live_classes' => $liveLessons,
        ]);
    }
}
