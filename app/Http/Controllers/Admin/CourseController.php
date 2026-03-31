<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function __construct(protected CourseService $service)
    {
    }

    public function index(Request $request)
    {
        $courses = $this->service->list(15, $request->only('status', 'type', 'search'));
        $categories = \App\Models\Category::orderBy('name')->get();

        return Inertia::render('admin/Courses', [
            'courses' => $courses,
            'categories' => ['data' => $categories],
            'filters' => $request->only('status', 'type', 'search'),
            'selected_course' => $request->query('selected_course'),
        ]);
    }

    public function store(CourseRequest $request)
    {
        $data = $request->validated();

        // Always create as draft for a fast, simple flow
        $data['status'] = 'BORRADOR';
        // DB enum supports "evento" (legacy). Map "masterclass" to "evento".
        if (($data['type'] ?? null) === 'masterclass') {
            $data['type'] = 'evento';
        }

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('courses', 'public');
            $data['image'] = '/storage/' . $path;
        }

        $course = $this->service->create($data);

        return redirect()->route('admin.courses.edit', $course->id)->with('success', 'Curso creado.');
    }

    public function edit(Course $course)
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        $course->load(['modules.lessons.materials', 'quizzes.questions.answers']);

        return Inertia::render('admin/CourseEditor', [
            'course' => $course,
            'categories' => $categories,
        ]);
    }

    public function update(CourseRequest $request, Course $course)
    {
        $data = $request->validated();

        if (($data['type'] ?? null) === 'masterclass') {
            $data['type'] = 'evento';
        }

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('courses', 'public');
            $data['image'] = '/storage/' . $path;
        }

        $this->service->update($course, $data);

        return redirect()->back()->with('success', 'Curso actualizado.');
    }

    public function destroy(Course $course)
    {
        $this->service->delete($course);

        return redirect()->back()->with('success', 'Curso eliminado.');
    }

    public function publish(Course $course)
    {
        try {
            $this->service->publish($course);
            return redirect()->back()->with('success', 'Curso publicado.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }

    public function hide(Course $course)
    {
        $this->service->hide($course);
        return redirect()->back()->with('success', 'Curso ocultado.');
    }
}
