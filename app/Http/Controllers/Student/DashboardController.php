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
        // Mock data
        $exams = [
            [
                'id' => 1,
                'title' => 'Examen Final de Módulo I',
                'course_title' => 'Especialización en Gestión Bancaria',
                'attempts_left' => 2,
            ]
        ];

        $history = [
            [
                'id' => 2,
                'title' => 'Control de Lectura 1',
                'course_title' => 'Análisis Económico Avanzado',
                'date' => '25 Mar 2026',
                'score' => 18,
                'passing_score' => 14,
                'status' => 'aprobado',
            ]
        ];

        return Inertia::render('student/Exams', [
            'exams' => $exams,
            'history' => $history
        ]);
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
}
