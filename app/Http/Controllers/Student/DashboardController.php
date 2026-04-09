<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Services\CertificateService;

class DashboardController extends Controller
{
    protected $certificateService;
    protected $examService;

    public function __construct(CertificateService $certificateService, \App\Services\ExamService $examService)
    {
        $this->certificateService = $certificateService;
        $this->examService = $examService;
    }
    public function index()
    {
        $user = Auth::user();
        
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $enrollments = Enrollment::where('user_id', $user->id)->with('course.category')->get();
        $enrollmentIds = $enrollments->pluck('course_id');
        
        // 1. Fetch real next live class
        $nextLiveClass = \App\Models\CourseLesson::with('course')
            ->whereIn('course_id', $enrollmentIds)
            ->whereNotNull('live_link')
            ->where('start_time', '>=', now()->subHours(1)) 
            ->orderBy('start_time', 'asc')
            ->first();

        // 2. Summary stats
        $stats = [
            'active_courses' => $enrollments->whereNull('completed_at')->count(),
            'completed_courses' => $enrollments->whereNotNull('completed_at')->count(),
            'upcoming_classes' => \App\Models\CourseLesson::whereIn('course_id', $enrollmentIds)
                ->where('start_time', '>', now())
                ->count(),
            'available_exams' => \App\Models\CourseQuiz::whereIn('course_id', $enrollmentIds)->count(),
        ];

        // 3. Continue where you left off (MUY IMPORTANTE)
        $lastLessonProgress = \App\Models\LessonProgress::where('user_id', $user->id)
            ->with(['lesson.module.course.category'])
            ->latest('updated_at')
            ->first();

        $continueLearning = null;
        if ($lastLessonProgress && $lastLessonProgress->lesson) {
            $course = $lastLessonProgress->lesson->course 
                      ?? $lastLessonProgress->lesson->module?->course;
            
            if ($course) {
                // Calculate detailed progress for this course
                $allLessonsCount = \App\Models\CourseLesson::whereHas('module', function($q) use ($course) {
                    $q->where('course_id', $course->id);
                })->orWhere(function($q) use ($course) {
                    $q->where('course_id', $course->id)->whereNull('module_id');
                })->count();
                
                $completedCount = \App\Models\LessonProgress::where('user_id', $user->id)
                    ->whereHas('lesson', function($q) use ($course) {
                        $q->where('course_id', $course->id)
                          ->orWhereHas('module', function($m) use ($course) {
                              $m->where('course_id', $course->id);
                          });
                    })
                    ->where('is_completed', true)
                    ->count();
                    
                $progressPercent = $allLessonsCount > 0 ? round(($completedCount / $allLessonsCount) * 100) : 0;

                $continueLearning = [
                    'course_title' => $course->title,
                    'course_slug' => $course->slug,
                    'lesson_title' => $lastLessonProgress->lesson->title,
                    'lesson_id' => $lastLessonProgress->lesson->id,
                    'progress' => $progressPercent,
                    'image' => $course->image,
                ];
            }
        }

        // 4. Recommendation Logic (Misma categoría + Más vendidos)
        $enrolledCategories = $enrollments->pluck('course.category_id')->unique();
        
        $recommendations = \App\Models\Course::whereNotIn('id', $enrollmentIds)
            ->where('status', 'PUBLICADO')
            ->whereIn('category_id', $enrolledCategories)
            ->with('category')
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc') // "Más vendidos" (Simulado por inscritos)
            ->take(3)
            ->get();

        if ($recommendations->count() < 3) {
            $moreRecs = \App\Models\Course::whereNotIn('id', $enrollmentIds)
                ->whereNotIn('id', $recommendations->pluck('id'))
                ->where('status', 'PUBLICADO')
                ->withCount('enrollments')
                ->orderBy('enrollments_count', 'desc')
                ->latest()
                ->take(3 - $recommendations->count())
                ->get();
            $recommendations = $recommendations->concat($moreRecs);
        }

        // 5. Get Certificates
        $certificates = \App\Models\Certificate::where('user_id', $user->id)
            ->with('course.certificateTemplate')
            ->orderByDesc('issue_date')
            ->take(3)
            ->get()
            ->map(function($cert) {
                $template = $cert->course->certificateTemplate;
                return [
                    'id' => $cert->id,
                    'course_title' => $cert->course->title,
                    'issue_date' => $cert->issue_date->format('d M Y'),
                    'code' => $cert->code,
                    'image' => ($template && $template->template_image) 
                               ? '/storage/' . $template->template_image 
                               : 'https://i.ibb.co/6P6X7p6/cert-placeholder.png',
                    'download_url' => '/storage/' . $cert->file_path,
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'continueLearning' => $continueLearning,
            'recommendations' => $recommendations,
            'certificates' => $certificates,
            'nextLiveClass' => $nextLiveClass ? [
                'id' => $nextLiveClass->id,
                'title' => $nextLiveClass->title,
                'course_title' => $nextLiveClass->course->title,
                'start_time' => $nextLiveClass->start_time->format('Y-m-d H:i:s'),
                'start_time_human' => $nextLiveClass->start_time->isoFormat('dddd D [de] MMMM [a las] h:mm A'),
                'course_slug' => $nextLiveClass->course->slug,
            ] : null,
        ]);
    }

    public function profile()
    {
        $user = Auth::user();

        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['course.category'])
            ->get()->map(function($e) use ($user) {
                $course = $e->course;
                if (!$course) return null;
                
                $allLessonsCount = \App\Models\CourseLesson::whereHas('module', function($q) use ($course) {
                    $q->where('course_id', $course->id);
                })->orWhere(function($q) use ($course) {
                    $q->where('course_id', $course->id)->whereNull('module_id');
                })->count();
                
                $completedCount = \App\Models\LessonProgress::where('user_id', $user->id)
                    ->whereHas('lesson', function($q) use ($course) {
                        $q->where('course_id', $course->id)
                          ->orWhereHas('module', function($m) use ($course) {
                              $m->where('course_id', $course->id);
                          });
                    })
                    ->where('is_completed', true)
                    ->count();
                    
                $progressPercent = $allLessonsCount > 0 ? round(($completedCount / $allLessonsCount) * 100) : 0;
                
                $lastProgress = \App\Models\LessonProgress::where('user_id', $user->id)
                    ->whereHas('lesson', function($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })
                    ->latest('updated_at')
                    ->first();
                
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'slug' => $course->slug,
                    'image' => $course->image,
                    'progress' => $progressPercent,
                    'last_lesson' => $lastProgress ? $lastProgress->lesson->title : 'Sin empezar',
                ];
            })->filter(function($item) {
                return $item !== null;
            });

        $activeSubscription = $user->subscriptions()
            ->where('status', 'activa')
            ->where('end_date', '>=', now())
            ->first();

        return Inertia::render('student/Profile', [
            'enrolledCourses' => array_values($enrollments->toArray()),
            'activeSubscription' => $activeSubscription,
        ]);
    }

    public function updateProfile(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:5120',
        ]);

        $user->name = $request->name;

        if ($request->hasFile('avatar')) {
             if ($user->avatar) { Storage::disk('public')->delete($user->avatar); }
             $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado exitosamente.');
    }

    public function courses()
    {
        $user = Auth::user();
        $enrolledCourses = $user->enrolledCourses()->with('category')->get();

        return Inertia::render('student/Courses', [
            'courses' => $enrolledCourses
        ]);
    }

    public function liveClasses()
    {
        $user = Auth::user();
        $enrollmentIds = \App\Models\Enrollment::where('user_id', $user->id)->pluck('course_id');

        // Fetching real live classes from enrolled courses
        $sessions = \App\Models\CourseLesson::with('course')
            ->whereIn('course_id', $enrollmentIds)
            ->whereNotNull('live_link')
            ->where('start_time', '>=', now()->subHours(2)) // Show up to 2h after they started
            ->orderBy('start_time', 'asc')
            ->get();

        $liveClasses = $sessions->map(function($session) {
            return [
                'id' => $session->id,
                'title' => $session->title,
                'day' => $session->start_time->isToday() ? 'HOY' : strtoupper($session->start_time->isoFormat('ddd')),
                'date' => $session->start_time->format('Y-m-d'),
                'time' => $session->start_time->format('H:i A'),
                'course_title' => $session->course->title,
                'course_slug' => $session->course->slug,
                'is_today' => $session->start_time->isToday(),
            ];
        });

        return Inertia::render('student/LiveClasses', [
            'live_classes' => $liveClasses
        ]);
    }

    public function exams()
    {
        $user = Auth::user();

        // Obtener todos los cursos del usuario
        $enrolledCourseIds = \App\Models\Enrollment::where('user_id', $user->id)
            ->pluck('course_id');

        $quizzes = \App\Models\CourseQuiz::whereIn('course_id', $enrolledCourseIds)
            ->with(['course'])
            ->get();
            
        $exams = $quizzes->map(function ($quiz) use ($user) {
            $completedCount = \App\Models\CourseExamAttempt::where('user_id', $user->id)
                ->where('course_quiz_id', $quiz->id)
                ->whereNotNull('completed_at')
                ->count();
            
            $attemptsLeft = max(0, $quiz->max_attempts - $completedCount);

            $passed = \App\Models\CourseExamAttempt::where('user_id', $user->id)
                ->where('course_quiz_id', $quiz->id)
                ->where('status', 'aprobado')
                ->exists();

            return [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'course_title' => $quiz->course->title ?? '',
                'time_limit' => $quiz->time_limit,
                'max_attempts' => $quiz->max_attempts,
                'attempts_left' => $attemptsLeft,
                'passed' => $passed
            ];
        });

        $attempts = \App\Models\CourseExamAttempt::where('user_id', $user->id)
            ->with(['quiz.course'])
            ->orderByDesc('created_at')
            ->get();
            
        $history = $attempts->map(function ($a) {
            return [
                'id' => $a->id,
                'title' => $a->quiz->title ?? '',
                'course_title' => $a->quiz->course->title ?? '',
                'date' => $a->completed_at ? \Carbon\Carbon::parse($a->completed_at)->format('d M Y') : 'En progreso',
                'score' => $a->score,
                'passing_score' => $a->quiz->minimum_score ?? 14,
                'status' => $a->status
            ];
        });

        return Inertia::render('student/Exams', [
            'exams' => $exams,
            'history' => $history
        ]);
    }

    public function takeExam(\App\Models\CourseQuiz $quiz)
    {
        $user = Auth::user();
        
        if (!$this->examService->canTakeQuiz($user, $quiz)) {
             return redirect()->route('student.exams.index')->with('error', 'Has superado el límite de intentos.');
        }

        // Buscamos o creamos el intento actual (en progreso)
        \App\Models\CourseExamAttempt::firstOrCreate(
            ['user_id' => $user->id, 'course_quiz_id' => $quiz->id, 'completed_at' => null],
            ['status' => 'started', 'score' => null]
        );

        $quiz->load(['course', 'questions.answers']);

        $completedCount = \App\Models\CourseExamAttempt::where('user_id', $user->id)
            ->where('course_quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->count();

        return Inertia::render('student/TakeExam', [
            'quiz' => $quiz,
            'current_attempt' => $completedCount + 1
        ]);
    }

    public function submitExam(\Illuminate\Http\Request $request, \App\Models\CourseQuiz $quiz)
    {
        try {
            $attempt = $this->examService->submit(
                Auth::user(), 
                $quiz, 
                $request->input('answers', [])
            );

            // Cargar datos del certificado si aprobó
            $certificate = \App\Models\Certificate::where('user_id', Auth::id())
                ->where('course_id', $quiz->course_id)
                ->first();

            return back()->with('exam_result', [
                'score' => $attempt->score,
                'status' => $attempt->status,
                'passing_score' => $quiz->minimum_score ?? 14,
                'certificate_url' => $certificate ? '/storage/' . $certificate->file_path : null,
            ]);
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function certificates()
    {
        $user = Auth::user();
        
        // Auto-generate missing certificates for completed courses
        $enrollments = Enrollment::where('user_id', $user->id)->get();
        foreach($enrollments as $enrollment) {
            $this->certificateService->generateIfEligible($user, $enrollment->course);
        }

        $certificates = \App\Models\Certificate::where('user_id', $user->id)
            ->with(['course.certificateTemplate'])
            ->orderByDesc('issue_date')
            ->get()
            ->map(function($cert) {
                $template = $cert->course ? $cert->course->certificateTemplate : null;
                return [
                    'id' => $cert->id,
                    'title' => 'Diploma de Finalización',
                    'course_title' => $cert->course ? $cert->course->title : 'Programa Terminado',
                    'issue_date' => $cert->issue_date ? $cert->issue_date->format('d M Y') : 'Fecha no asignada',
                    'image' => ($template && $template->template_image) 
                               ? '/storage/' . $template->template_image 
                               : 'https://i.ibb.co/6P6X7p6/cert-placeholder.png',
                    'code' => $cert->code,
                    'is_available' => true,
                    'download_url' => '/storage/' . $cert->file_path,
                ];
            });

        return Inertia::render('student/Certificates', [
            'certificates' => $certificates
        ]);
    }

    public function exploreCourses(\Illuminate\Http\Request $request)
    {
        $enrolledCourseIds = \App\Models\Enrollment::where('user_id', Auth::id())->pluck('course_id');

        $query = \App\Models\Course::where('status', 'PUBLICADO')
            ->whereIn('type', ['grabado', 'en vivo', 'hibrido'])
            ->whereNotIn('id', $enrolledCourseIds)
            ->with('category');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category') && $request->category !== 'Todas las áreas') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $categories = \App\Models\Category::has('courses')->orderBy('name')->get();

        return Inertia::render('Cursos', [
            'courses' => $courses,
            'categories' => $categories,
            'filters' => $request->only(['search', 'modality', 'category']),
            'isDashboard' => true,
        ]);
    }

    public function explorePublications()
    {
        return Inertia::render('Publications', [
            'books' => \App\Models\Book::latest()->get(),
            'articles' => \App\Models\Article::latest()->get(),
            'isDashboard' => true,
        ]);
    }

    public function exploreMasterclasses(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Course::where('status', 'PUBLICADO')
            ->where('type', 'evento')
            ->with(['category', 'lessons']);

        if ($request->filled('category') && $request->category !== 'Todas') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $courses = $query->orderBy('created_at', 'desc')->get();
        $categories = \App\Models\Category::whereHas('courses', function($q){
            $q->where('type', 'evento');
        })->orderBy('name')->get();

        return Inertia::render('Masterclasses', [
            'courses' => $courses,
            'categories' => $categories,
            'filters' => $request->only(['category']),
            'isDashboard' => true,
        ]);
    }

    public function exploreConsultoria()
    {
        return Inertia::render('Consultoria', [
            'isDashboard' => true,
        ]);
    }
}
