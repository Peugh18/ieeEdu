<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::where('status', 'PUBLICADO')->with('category');

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

    public function show($slug)
    {
        $course = Course::where('slug', $slug)
            ->where('status', 'PUBLICADO')
            ->with(['category', 'teacher', 'modules.lessons', 'lessons'])
            ->firstOrFail();

        // Calculate some basic stats to display
        $totalDuration = 0;
        $course->loadCount('lessons');

        return Inertia::render('CourseDetail', [
            'course' => $course,
        ]);
    }
}
