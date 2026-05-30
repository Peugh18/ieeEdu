<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function __construct(
        protected ProgressService $progressService
    ) {}

    /**
     * Listado de mis Cursos
     */
    public function courses()
    {
        $user = Auth::user();

        // 1. Obtener inscripciones del estudiante (con relaciones eager-loaded)
        $enrollments = Enrollment::where('user_id', $user->id)
            ->visible()
            ->with(['course.category', 'course.modules.lessons'])
            ->get();

        $courseIds = $enrollments->pluck('course_id')->filter()->unique()->toArray();

        // Cálculo de progreso en lote para evitar consultas N+1 en bucles
        $progressMap = $this->progressService->getBatchProgress($user, $courseIds);

        // Sincronizar solo si el progreso calculado es diferente del almacenado en BD
        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            if (! $course) {
                continue;
            }
            $calculated = $progressMap[$course->id] ?? 0;
            if ($enrollment->progress !== $calculated) {
                $data = ['progress' => $calculated];
                if ($calculated >= 100 && ! $enrollment->completed_at) {
                    if (! $course->hasFinalQuiz()) {
                        $data['completed_at'] = now();
                    }
                }
                $enrollment->update($data);
                $enrollment->progress = $calculated;
            }
        }

        $courses = $enrollments->map(function (Enrollment $enrollment) {
            $course = $enrollment->course;

            return [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'image' => $course->image,
                'type' => $course->type,
                'category' => $course->category,
                'pivot' => [
                    'enrolled_at' => $enrollment->enrolled_at,
                    'completed_at' => $enrollment->completed_at,
                    'progress' => $enrollment->progress,
                ],
            ];
        });

        return Inertia::render('student/Courses', ['courses' => $courses]);
    }

    /**
     * Explorar Catálogo
     */
    public function exploreCourses(Request $request)
    {
        $user = Auth::user();
        $query = Course::published()
            ->with(['category', 'instructor'])
            ->withCount('lessons');

        // Filtrar cursos que ya tiene acceso activo
        if ($user->hasSubscriptionActive()) {
            $query->whereRaw('1 = 0');
        } else {
            $visibleCourseIds = Enrollment::where('user_id', $user->id)
                ->visible()
                ->pluck('course_id');
            $query->whereNotIn('id', $visibleCourseIds);
        }

        $courses = $query->paginate(6)
            ->withQueryString();

        $banner = Banner::where('section', 'cursos')->first();

        return Inertia::render('Cursos', [
            'courses' => $courses,
            'categories' => Category::all(),
            'filters' => $request->only(['search', 'modality', 'category']),
            'banner' => $banner,
            'isDashboard' => true,
            'hasActiveSubscription' => $user->hasSubscriptionActive(),
        ]);
    }

    /**
     * Inscribirse a un curso gratuito
     */
    public function enrollFree(Request $request, Course $course, EnrollmentService $enrollmentService)
    {
        $user = Auth::user();

        // Validar que sea gratis
        $price = $course->sale_price > 0 ? $course->sale_price : $course->price;
        if ($price > 0) {
            return redirect()->back()->with('error', 'El curso no es gratuito.');
        }

        // Validar que no esté ya inscrito (puede tener acceso lectivo en gratis sin fila aún)
        $alreadyEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->visible()
            ->exists();

        if ($alreadyEnrolled) {
            return redirect()->route('student.classroom', $course->slug)
                ->with('success', 'Ya tienes acceso a este curso.');
        }

        $enrollmentService->enroll($user, $course);

        return redirect()->route('student.classroom', $course->slug)
            ->with('success', 'Te has inscrito exitosamente.');
    }
}
