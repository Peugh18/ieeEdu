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

        // Obtener IDs inscritos (Cache local para el método)
        $enrolledIds = Enrollment::where('user_id', $user->id)->pluck('course_id');

        // 1. Sesión en vivo próxima
        $nextLiveClass = CourseLesson::whereIn('course_id', $enrolledIds)
            ->whereNotNull('start_time')
            ->whereNull('video_url') // Si ya tiene video, no es una "próxima clase"
            ->where('start_time', '>=', now()->subHours(5)) // Margen amplio por posibles desfases de zona horaria
            ->orderBy('start_time')
            ->first();

        return Inertia::render('Dashboard', [
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
        ]);
    }

    /**
     * Perfil del Usuario y sus Cursos
     */
    public function profile()
    {
        $user = Auth::user();

        $enrolledCourses = Enrollment::where('user_id', $user->id)
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
        
        $courses = $user->enrolledCourses()
            ->with(['category', 'modules.lessons'])
            ->get()
            ->map(function (Course $course) use ($user, $progressService) {
                // Sincronización proactiva para corregir datos antiguos desfazados
                $currentProgress = $progressService->calculateCourseProgress($user, $course);
                
                // Actualizamos la base de datos en segundo plano (si es necesario)
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
        $enrolledCourseIds = Enrollment::where('user_id', $user->id)->pluck('course_id');

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
        $enrolledIds = Enrollment::where('user_id', $user->id)->pluck('course_id');

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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
        ]);

        $user->update($data);

        return back()->with('success', 'Perfil actualizado.');
    }

    /**
     * Explorar Catálogo
     */
    public function exploreCourses(Request $request)
    {
        $courses = Course::published()
            ->with(['category', 'instructor'])
            ->withCount('lessons')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Cursos', [
            'courses' => $courses,
            'categories' => \App\Models\Category::all(),
            'filters' => $request->only(['search', 'modality', 'category']),
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

        return Inertia::render('Publications', [
            'books' => $books,
            'articles' => $articles,
            'isDashboard' => true
        ]);
    }

    /**
     * Explorar Masterclasses
     */
    public function exploreMasterclasses(Request $request)
    {
        return Inertia::render('Masterclasses', [
            'courses' => Course::published()->where('type', 'masterclass')->get(),
            'categories' => \App\Models\Category::all(),
            'filters' => $request->only(['category']),
            'isDashboard' => true
        ]);
    }

    /**
     * Explorar Consultoría
     */
    public function exploreConsultoria()
    {
        return Inertia::render('Consultoria', [
            'isDashboard' => true
        ]);
    }

    // Se pueden agregar los demás métodos de exploración (publications, masterclasses) según sea necesario
}
