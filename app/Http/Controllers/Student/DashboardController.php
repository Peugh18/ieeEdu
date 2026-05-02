<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseQuiz;
use App\Models\Certificate;
use App\Models\CourseExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use App\Services\CertificateService;
use App\Services\ProgressService;
use App\Actions\Student\GetDashboardStatsAction;
use App\Actions\Student\GetContinueLearningAction;
use App\Actions\Student\GetCourseRecommendationsAction;

class DashboardController extends Controller
{
    public function __construct(
        protected CertificateService $certificateService,
        protected ProgressService $progressService,
        protected \App\Services\ExamService $examService
    ) {}

    /**
     * Dashboard Principal del Estudiante
     */
    public function index(
        GetDashboardStatsAction $getStats,
        GetContinueLearningAction $getContinue,
        GetCourseRecommendationsAction $getRecommendations
    ) {
        $user = Auth::user();

        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $dashboardData = Cache::remember('student_dashboard_' . $user->id, 60, function () use ($user, $getStats, $getContinue, $getRecommendations) {
            // Obtener IDs inscritos filtrados por acceso (pago o suscripción activa)
            $enrolledIds = Enrollment::where('user_id', $user->id)->visible()->pluck('course_id');

            // 1. Sesión en vivo próxima
            $nextLiveClass = CourseLesson::whereIn('course_id', $enrolledIds)
                ->whereNotNull('start_time')
                ->whereNull('video_url')
                ->where('start_time', '>=', now()->subHours(5))
                ->orderBy('start_time')
                ->first();

            return [
                'stats'            => $getStats->execute($user),
                'continueLearning' => $getContinue->execute($user),
                'recommendations'  => $getRecommendations->execute($user),
                'certificates'     => $this->getRecentCertificates($user),
                'nextLiveClass'    => $nextLiveClass ? [
                    'id' => $nextLiveClass->id,
                    'title' => $nextLiveClass->title,
                    'course_title' => $nextLiveClass->course?->title ?? 'Curso',
                    'start_time' => $nextLiveClass->start_time->format('Y-m-d H:i:s'),
                    'start_time_human' => $nextLiveClass->start_time->isoFormat('dddd D [de] MMMM [a las] h:mm A'),
                    'course_slug' => $nextLiveClass->course?->slug,
                ] : null,
            ];
        });

        return Inertia::render('Dashboard', $dashboardData);
    }

    /**
     * Perfil del Usuario y sus Cursos
     */
    public function profile()
    {
        $user = Auth::user();

        $enrolledCourses = Enrollment::where('user_id', $user->id)
            ->visible()
            ->with(['course.category'])
            ->get()
            ->map(function (Enrollment $enrollment) use ($user) {
                $course = $enrollment->course;
                if (!$course) return null;

                return [
                    'id'          => $course->id,
                    'title'       => $course->title,
                    'slug'        => $course->slug,
                    'image'       => $course->image,
                    'progress'    => $this->progressService->calculateCourseProgress($user, $course),
                    'last_lesson' => 'Sin empezar', // Se podría mejorar con last_lesson_id en table
                ];
            })->filter()->values();

        return Inertia::render('student/Profile', [
            'enrolledCourses'    => $enrolledCourses,
            'activeSubscription' => $user->subscriptions()->where('status', 'activa')->where('end_date', '>=', now())->first(),
        ]);
    }

    /**
     * Listado de mis Cursos
     */
    public function courses()
    {
        $user = Auth::user();
        $progressService = app(\App\Services\ProgressService::class);

        // Filtrar: cursos individuales siempre visibles,
        // cursos de suscripción solo si subscription_active = true
        $courses = $user->enrolledCourses()
            ->whereHas('enrollments', function($q) use ($user) {
                $q->where('user_id', $user->id)->visible();
            })
            ->with(['category', 'modules.lessons'])
            ->get()
            ->map(function (Course $course) use ($user, $progressService) {
                $currentProgress = $progressService->calculateCourseProgress($user, $course);
                $progressService->syncProgress($user, $course);

                return [
                    'id'       => $course->id,
                    'title'    => $course->title,
                    'slug'     => $course->slug,
                    'image'    => $course->image,
                    'type'     => $course->type,
                    'category' => $course->category,
                    'pivot'    => [
                        'enrolled_at'  => $course->pivot->enrolled_at,
                        'completed_at' => $course->pivot->completed_at,
                        'progress'     => $currentProgress,
                    ]
                ];
            });

        return Inertia::render('student/Courses', ['courses' => $courses]);
    }

    /**
     * Sección de Exámenes
     */
    public function exams()
    {
        $user = Auth::user();
        $enrolledCourseIds = Enrollment::where('user_id', $user->id)->visible()->pluck('course_id');

        $quizzes = CourseQuiz::whereIn('course_id', $enrolledCourseIds)->with('course')->get()
            ->map(function (CourseQuiz $quiz) use ($user) {
                $attempts = CourseExamAttempt::where('user_id', $user->id)
                    ->where('course_quiz_id', $quiz->id)
                    ->whereNotNull('completed_at');

                return [
                    'id'            => $quiz->id,
                    'title'         => $quiz->title,
                    'course_title'  => $quiz->course->title ?? '',
                    'time_limit'    => $quiz->time_limit,
                    'attempts_left' => max(0, $quiz->max_attempts - $attempts->count()),
                    'passed'        => $attempts->clone()->where('status', 'aprobado')->exists(),
                    'progress'      => (int)($quiz->course->enrollments()->where('user_id', $user->id)->first()?->progress ?? 0),
                    'date'          => $attempts->clone()->latest()->first()?->completed_at?->format('d M Y') ?? 'Pendiente',
                    'score'         => (int)($attempts->clone()->latest()->first()?->score ?? 0),
                    'passing_score' => (int)($quiz->minimum_score ?? config('education.passing_score', 14)),
                    'status'        => 'pendiente'
                ];
            });

        return Inertia::render('student/Exams', [
            'exams'   => $quizzes,
            'history' => $this->getExamHistory($user),
            'stats'   => [
                'average_score'     => round(CourseExamAttempt::where('user_id', $user->id)->whereNotNull('score')->avg('score') ?? 0, 1),
                'certificate_count' => Certificate::where('user_id', $user->id)->count()
            ]
        ]);
    }

    // --- Helpers Privados ---

    protected function getRecentCertificates($user)
    {
        return Certificate::where('user_id', $user->id)
            ->with('course')
            ->orderByDesc('issue_date')
            ->take(3)
            ->get()
            ->map(fn($cert) => [
                'id'           => $cert->id,
                'title'        => 'Certificado de Aprobación',
                'course_title' => $cert->course->title ?? 'Curso',
                'issue_date'   => $cert->issue_date->format('d M Y'),
                'image'        => $cert->course->image ?? '/images/cert-placeholder.png',
                'code'         => $cert->code ?? 'N/A',
                'is_available' => true,
                'download_url' => asset('storage/' . $cert->file_path),
            ]);
    }

    protected function getExamHistory($user)
    {
        return CourseExamAttempt::where('user_id', $user->id)
            ->with(['quiz.course'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($a) => [
                'id'            => $a->id,
                'title'         => $a->quiz->title ?? '',
                'course_title'  => $a->quiz->course->title ?? '',
                'date'          => $a->completed_at ? $a->completed_at->format('d M Y') : 'En progreso',
                'score'         => $a->score,
                'passing_score' => $a->quiz->minimum_score ?? config('education.passing_score'),
                'status'        => $a->status
            ]);
    }

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
            ->map(fn($l) => [
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
            'live_classes' => $liveLessons
        ]);
    }

    /**
     * Gestión de Certificados
     */
    public function certificates()
    {
        $user = Auth::user();
        $certificates = Certificate::where('user_id', $user->id)
            ->with(['course.category', 'course.certificateTemplate'])
            ->orderByDesc('issue_date')
            ->get()
            ->map(fn($cert) => [
                'id' => $cert->id,
                'title' => 'Certificado de Aprobación',
                'course_title' => $cert->course->title ?? 'Curso',
                'issue_date' => $cert->issue_date->format('d M Y'),
                'image' => $cert->course->image ?? '/images/cert-placeholder.png',
                'code' => $cert->code ?? 'N/A',
                'is_available' => true,
                'download_url' => route('student.certificates.download', ['certificate' => $cert->id]),
            ]);

        return Inertia::render('student/Certificates', [
            'certificates' => $certificates
        ]);
    }

    /**
     * Iniciar un examen
     */
    public function takeExam(CourseQuiz $quiz)
    {
        $user = Auth::user();
        
        // 1. Verificar acceso al curso
        $this->authorize('viewClassroom', $quiz->course);

        // 2. Verificar progreso (Seguridad adicional)
        $enrollment = $quiz->course->enrollments()->where('user_id', $user->id)->first();
        if (!$enrollment || $enrollment->progress < 100) {
            return redirect()->route('student.exams.index')
                ->with('error', 'Debes completar el 100% del curso para rendir la evaluación final.');
        }

        // 3. Cargar preguntas con sus alternativas (IMPORTANTE)
        return Inertia::render('student/TakeExam', [
            'quiz'            => $quiz->load(['questions.answers', 'course']),
            'current_attempt' => CourseExamAttempt::where('user_id', $user->id)->where('course_quiz_id', $quiz->id)->count() + 1,
        ]);
    }

    /**
     * Enviar examen
     */
    public function submitExam(Request $request, CourseQuiz $quiz)
    {
        $user = Auth::user();
        
        try {
            $result = $this->examService->submit($user, $quiz, $request->answers);
            return back()->with('exam_result', $result);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadCertificate(Request $request, \App\Models\Certificate $certificate)
    {
        // Security check
        if ($certificate->user_id !== Auth::id()) {
            abort(403);
        }

        $action = $request->query('action', 'download');

        $certificateService = app(\App\Services\CertificateService::class);
        return $certificateService->downloadPdf($certificate, $action);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Si viene contraseña, validar y actualizar solo eso
        if ($request->has('password') && $request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);
            return back()->with('success', 'Contraseña actualizada correctamente.');
        }

        // Actualizar perfil normal (nombre, teléfono, avatar)
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Procesar imagen si se envió
        if ($request->hasFile('avatar')) {
            // Borrar avatar anterior si existe
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            // Guardar nueva imagen en public/storage/avatars/
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Perfil actualizado correctamente.');
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

        $courses = $query->paginate(12)
            ->withQueryString();

        $banner = \App\Models\Banner::where('section', 'cursos')->first();

        return Inertia::render('Cursos', [
            'courses' => $courses,
            'categories' => \App\Models\Category::all(),
            'filters' => $request->only(['search', 'modality', 'category']),
            'banner' => $banner,
            'isDashboard' => true
        ]);
    }

    /**
     * Explorar Publicaciones
     */
    public function explorePublications()
    {
        $books = \App\Models\Book::all()->map(fn($b) => [
            'id' => $b->id,
            'category' => $b->category ?? 'Libro',
            'title' => $b->title,
            'price' => $b->price,
            'author' => $b->author ?? 'Instituto IEE',
            'description' => $b->description,
            'cover_image' => $b->cover_image,
            'file_path' => $b->file_path,
            'download_url' => $b->download_url,
            'is_available' => (bool)$b->is_available,
        ]);

        $articles = \App\Models\Article::all()->map(fn($a) => [
            'id' => $a->id,
            'title' => $a->title,
            'media' => $a->media ?? 'Análisis',
            'published_at' => $a->published_at ? $a->published_at->format('Y-m-d') : now()->format('Y-m-d'),
            'thumbnail' => $a->thumbnail,
            'download_url' => $a->download_url,
        ]);

        $banner = \App\Models\Banner::where('section', 'publicaciones')->orderBy('order')->first();

        return Inertia::render('Publications', [
            'books' => $books,
            'articles' => $articles,
            'banner' => $banner,
            'isDashboard' => true
        ]);
    }

    /**
     * Explorar Masterclasses
     */
    public function exploreMasterclasses(Request $request)
    {
        $banner = \App\Models\Banner::where('section', 'masterclass')->first();

        $categories = \App\Models\Category::whereHas('courses', function($q) {
            $q->where('type', 'evento');
        })->orderBy('name')->get();

        return Inertia::render('Masterclasses', [
            'courses' => Course::published()->where('type', 'evento')->get(),
            'categories' => $categories,
            'filters' => $request->only(['category']),
            'banner' => $banner,
            'isDashboard' => true
        ]);
    }

    /**
     * Explorar Consultoría
     */
    public function exploreConsultoria()
    {
        $banner = \App\Models\Banner::where('section', 'consultoria')->orderBy('order')->first();

        return Inertia::render('Consultoria', [
            'banner' => $banner,
            'isDashboard' => true
        ]);
    }

    // Se pueden agregar los demás métodos de exploración (publications, masterclasses) según sea necesario
}
