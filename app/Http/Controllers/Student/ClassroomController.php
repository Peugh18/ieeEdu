<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClassroomController extends Controller
{
    public function show(Course $course, CourseLesson $lesson = null)
    {
        $user = Auth::user();

        // Verificar inscripción
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('cursos.show', $course->slug)
                ->with('error', 'No estás inscrito en este curso.');
        }

        // Cargar módulos y lecciones
        $course->load(['modules.lessons.materials', 'quizzes']);

        // Si no se especifica lección, tomar la primera
        if (!$lesson) {
            $lesson = $course->modules->first()?->lessons->first() 
                      ?? $course->lessons->first();
        }

        // Si aún no hay lección (curso vacío)
        if (!$lesson) {
            return Inertia::render('student/Classroom', [
                'course' => $course,
                'currentLesson' => null,
                'prevLessonId' => null,
                'nextLessonId' => null,
            ]);
        }

        // Encontrar lección previa y siguiente para la navegación
        $allLessons = $course->modules->flatMap->lessons->concat($course->lessons->whereNull('module_id'));
        $currentIndex = $allLessons->search(fn($l) => $l->id === $lesson->id);
        
        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;

        return Inertia::render('student/Classroom', [
            'course' => $course,
            'currentLesson' => $lesson->load('materials'),
            'prevLessonId' => $prevLesson?->id,
            'nextLessonId' => $nextLesson?->id,
            'allLessonsCount' => $allLessons->count(),
            'currentLessonIndex' => $currentIndex + 1,
        ]);
    }
}
