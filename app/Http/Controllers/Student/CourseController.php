<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use App\Services\ProgressService;
use App\Services\SubscriptionAccessService;
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

        if ($user->hasSubscriptionActive()) {
            app(SubscriptionAccessService::class)->sync($user->id);
        }

        // Inscripciones visibles: compra individual/gratis o suscripción activa
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
            ->where('type', '!=', 'evento')
            ->with(['category', 'instructor'])
            ->withCount('lessons');

        $visibleCourseIds = Enrollment::where('user_id', $user->id)
            ->visible()
            ->pluck('course_id');
        $query->whereNotIn('id', $visibleCourseIds);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('modality') && $request->modality !== 'Todos') {
            $mapType = [
                'Grabado' => 'grabado',
                'En vivo' => 'en vivo',
                'Evento' => 'evento',
            ];

            if (isset($mapType[$request->modality])) {
                $query->where('type', $mapType[$request->modality]);
            }
        }

        if ($request->filled('category') && $request->category !== 'Todas las áreas') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(6)->withQueryString();

        $banner = Banner::where('section', 'cursos')->first();

        return Inertia::render('Cursos', [
            'courses' => $courses,
            'categories' => Category::has('courses')->orderBy('name')->get(),
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

        // Validar que no esté ya inscrito permanentemente
        $alreadyEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('subscription_granted', false)
            ->exists();

        if ($alreadyEnrolled || $user->hasPermanentCourseAccess($course->id)) {
            return redirect()->route('student.classroom', $course->slug)
                ->with('success', 'Ya tienes acceso a este curso.');
        }

        $enrollmentService->enroll($user, $course);

        return redirect()->route('student.classroom', $course->slug)
            ->with('success', 'Te has inscrito exitosamente.');
    }
}
