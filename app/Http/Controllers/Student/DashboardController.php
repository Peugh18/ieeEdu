<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $enrollmentIds = Enrollment::where('user_id', $user->id)->pluck('course_id');
        
        // Fetch real next live class
        $nextLiveClass = \App\Models\CourseLesson::with('course')
            ->whereIn('course_id', $enrollmentIds)
            ->whereNotNull('live_link')
            ->where('start_time', '>=', now()->subHours(1)) 
            ->orderBy('start_time', 'asc')
            ->first();

        $stats = [
            'active_courses' => Enrollment::where('user_id', $user->id)->whereNull('completed_at')->count(),
            'completed_courses' => Enrollment::where('user_id', $user->id)->whereNotNull('completed_at')->count(),
            'upcoming_classes' => \App\Models\CourseLesson::whereIn('course_id', $enrollmentIds)
                ->where('start_time', '>', now())
                ->count(),
            'available_exams' => 0, 
        ];

        $recentCourses = $user->enrolledCourses()->with('category')->latest()->take(2)->get()->map(function($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'image' => $course->image,
                'progress' => $course->pivot->completed_at ? 100 : 45,
            ];
        });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentCourses' => $recentCourses,
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
        // Mock live classes
        $liveClasses = [
            [
                'id' => 1,
                'title' => 'Gestión de Riesgos y Cumplimiento',
                'day' => 'HOY',
                'date' => date('Y-m-d'),
                'time' => '19:00 PM',
                'course_title' => 'Especialización en Gestión Bancaria',
                'is_today' => true,
            ],
            [
                'id' => 2,
                'title' => 'Econometría Aplicada I',
                'day' => 'VIE',
                'date' => date('Y-m-d', strtotime('+2 days')),
                'time' => '18:30 PM',
                'course_title' => 'Análisis Económico Avanzado',
                'is_today' => false,
            ]
        ];

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
        
        $attemptCount = \App\Models\CourseExamAttempt::where('user_id', $user->id)
            ->where('course_quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->count();

        if ($attemptCount >= $quiz->max_attempts) {
             return redirect()->route('student.exams.index')->with('error', 'Has superado el límite de intentos.');
        }

        // Buscamos o creamos el intento actual (en progreso)
        \App\Models\CourseExamAttempt::firstOrCreate(
            ['user_id' => $user->id, 'course_quiz_id' => $quiz->id, 'completed_at' => null],
            ['status' => 'started', 'score' => null]
        );

        $quiz->load(['course', 'questions.answers']);

        return Inertia::render('student/TakeExam', [
            'quiz' => $quiz,
            'current_attempt' => $attemptCount + 1
        ]);
    }

    public function submitExam(\Illuminate\Http\Request $request, \App\Models\CourseQuiz $quiz)
    {
        $user = Auth::user();
        $answers = $request->input('answers', []); // [question_id => answer_id]
        
        $scoreRaw = 0;
        $quiz->load('questions.answers');
        foreach($quiz->questions as $q) {
            if (isset($answers[$q->id])) {
                $selectedAns = $q->answers->where('id', $answers[$q->id])->first();
                if ($selectedAns && $selectedAns->is_correct) {
                    $scoreRaw += 1;
                }
            }
        }
        
        $totalQuestions = $quiz->questions->count();
        $finalScore = $totalQuestions > 0 ? round(($scoreRaw / $totalQuestions) * 20) : 0;
        $status = $finalScore >= 14 ? 'aprobado' : 'reprobado';

        $attempt = \App\Models\CourseExamAttempt::where('user_id', $user->id)
            ->where('course_quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->first();

        if ($attempt) {
            $attempt->update([
                'status' => $status,
                'score' => $finalScore,
                'answers_data' => $answers,
                'completed_at' => now(),
            ]);
        }

        return redirect()->route('student.exams.index');
    }

    public function certificates()
    {
        // Mock data
        $certificates = [
            [
                'id' => 1,
                'title' => 'Diploma de Especialización',
                'course_title' => 'Analista Profesional en Finanzas',
                'issue_date' => '15 Mar 2026',
                'image' => 'https://i.ibb.co/6P6X7p6/cert-placeholder.png',
                'code' => 'IEE-FA-2026-001',
                'is_available' => true,
            ]
        ];

        return Inertia::render('student/Certificates', [
            'certificates' => $certificates
        ]);
    }

    public function exploreCourses(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Course::where('status', 'PUBLICADO')
            ->whereIn('type', ['grabado', 'en vivo', 'hibrido'])
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
