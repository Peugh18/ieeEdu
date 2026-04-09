<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PublicCourseController extends Controller
{
    public function welcome()
    {
        // Fetch published courses (not masterclasses)
        $query = Course::where('status', 'PUBLICADO')
            ->whereIn('type', ['grabado', 'en vivo', 'hibrido'])
            ->with('category');

        if (Auth::check()) {
            $enrolledCourseIds = \App\Models\Enrollment::where('user_id', Auth::id())->pluck('course_id');
            $query->whereNotIn('id', $enrolledCourseIds);
        }

        $courses = $query->get();

        // Also if ebooks exist we'll fetch them, but for now we pass empty array or we don't pass them
        // if they are static. The user mentioned "EBOOKS TAMBIÉN", maybe later.
        
        return Inertia::render('Welcome', [
            'dynamicCourses' => collect($courses)->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'category' => $course->category ? $course->category->name : 'General',
                    'instructor' => $course->instructor_name ?: ($course->teacher ? $course->teacher->name : 'Instituto IEE'),
                    'duration' => $course->duration_weeks ? $course->duration_weeks . ' semanas' : 'A tu ritmo',
                    'rating' => 5.0,
                    'reviews' => 120, // default placeholder
                    'type' => $course->type,
                    'price' => (float) $course->price,
                    'sale_price' => (float) $course->sale_price,
                    'start_date' => $course->start_date,
                    'start_time' => $course->start_time,
                    'class_hours' => $course->class_hours,
                    'whatsapp_link' => $course->whatsapp_link,
                    'image' => $course->image,
                    'slug' => $course->slug,
                    'instructor_image' => $course->instructor_image,
                ];
            })
        ]);
    }

    public function index(Request $request)
    {
        // 🚫 MANTENER ARQUITECTURA: NO mostrar eventos (masterclasses) en el catálogo de cursos
        $query = Course::where('status', 'PUBLICADO')
            ->whereIn('type', ['grabado', 'en vivo', 'hibrido'])
            ->with('category');

        if (Auth::check()) {
            $enrolledCourseIds = \App\Models\Enrollment::where('user_id', Auth::id())->pluck('course_id');
            $query->whereNotIn('id', $enrolledCourseIds);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
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

        $courses = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        $categories = Category::has('courses')->orderBy('name')->get();

        return Inertia::render('Cursos', [
            'courses' => $courses,
            'categories' => $categories,
            'filters' => $request->only(['search', 'modality', 'category']),
        ]);
    }

    public function show(Request $request, $slug)
    {
        $course = Course::where('slug', $slug)
            ->where('status', 'PUBLICADO')
            ->with(['category', 'teacher', 'modules.lessons', 'lessons'])
            ->firstOrFail();

        // Calculate some basic stats to display
        $course->loadCount('lessons');

        return Inertia::render('CourseDetail', [
            'course' => $course,
            'isDashboard' => $request->boolean('dashboard'),
            'isEnrolled' => auth()->check() ? auth()->user()->hasAccess($course->id) : false,
        ]);
    }

    public function masterclasses(Request $request)
    {
        $query = Course::where('status', 'PUBLICADO')
            ->where('type', 'evento')
            ->with(['category', 'lessons' => function ($q) {
                // Load lessons to extract dates/times for masterclasses if applicable
                $q->orderBy('start_time', 'asc');
            }]);

        if ($request->filled('category') && $request->category !== 'Todas') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $courses = $query->orderBy('created_at', 'desc')->get(); // we can use get instead of paginate for masterclasses or paginate if preferred. I'll use get().

        $categories = Category::whereHas('courses', function($q){
            $q->where('type', 'evento');
        })->orderBy('name')->get();

        return Inertia::render('Masterclasses', [
            'courses' => $courses,
            'categories' => $categories,
            'filters' => $request->only(['category']),
        ]);
    }

    public function planes()
    {
        return Inertia::render('Planes');
    }
}
