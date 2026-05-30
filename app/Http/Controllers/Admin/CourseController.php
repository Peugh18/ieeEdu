<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Services\CourseDuplicationService;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function __construct(
        protected CourseService $service,
        protected CourseDuplicationService $duplicationService,
    ) {}

    public function index(Request $request)
    {
        $courses = $this->service->list(10, $request->only('status', 'type', 'search'));
        $categories = Category::orderBy('name')->get();

        return Inertia::render('admin/Courses', [
            'courses' => $courses,
            'categories' => ['data' => $categories],
            'filters' => $request->only('status', 'type', 'search'),
            'selected_course' => $request->query('selected_course'),
        ]);
    }

    public function store(CourseRequest $request)
    {
        $this->authorize('create', Course::class);
        $data = $request->validated();

        // Always create as draft for a fast, simple flow
        $data['status'] = 'BORRADOR';
        // DB enum supports "evento" (legacy). Map "masterclass" to "evento".
        if (($data['type'] ?? null) === 'masterclass') {
            $data['type'] = 'evento';
        }

        // ✅ Compute sale_price on the backend (authoritative source)
        // If discount is set, calculate sale_price regardless of what frontend sent
        if (! empty($data['discount']) && (float) $data['discount'] > 0 && ! empty($data['price'])) {
            $data['sale_price'] = round((float) $data['price'] * (1 - (float) $data['discount'] / 100), 2);
        } else {
            $data['sale_price'] = null; // no discount → null
        }

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('courses', 'public');
            $data['image'] = '/storage/'.$path;
        }

        if ($request->hasFile('instructor_image_file')) {
            $path = $request->file('instructor_image_file')->store('instructors', 'public');
            $data['instructor_image'] = '/storage/'.$path;
        }

        $course = $this->service->create($data);

        return redirect()->route('admin.courses.edit', $course->id)->with('success', 'Curso creado.');
    }

    public function edit(Course $course)
    {
        $categories = Category::orderBy('name')->get();
        $course->load(['modules', 'lessons', 'quizzes.questions.answers', 'certificates.user', 'enrollments.user']);

        return Inertia::render('admin/CourseEditor', [
            'course' => $course,
            'categories' => $categories,
        ]);
    }

    public function update(CourseRequest $request, Course $course)
    {
        $this->authorize('update', $course);
        $data = $request->validated();

        if (($data['type'] ?? null) === 'masterclass') {
            $data['type'] = 'evento';
        }

        // ✅ Compute sale_price on the backend (authoritative source)
        if (! empty($data['discount']) && (float) $data['discount'] > 0 && ! empty($data['price'])) {
            $data['sale_price'] = round((float) $data['price'] * (1 - (float) $data['discount'] / 100), 2);
        } else {
            $data['sale_price'] = null;
        }

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('courses', 'public');
            $data['image'] = '/storage/'.$path;
        }

        if ($request->hasFile('instructor_image_file')) {
            $path = $request->file('instructor_image_file')->store('instructors', 'public');
            $data['instructor_image'] = '/storage/'.$path;
        }

        $this->service->update($course, $data);

        return redirect()->back()->with('success', 'Curso actualizado.');
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);
        $this->service->delete($course);

        return redirect()->back()->with('success', 'Curso eliminado.');
    }

    public function publish(Course $course)
    {
        $this->authorize('publish', $course);
        try {
            $this->service->publish($course);

            return redirect()->back()->with('success', 'Curso publicado.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }

    public function hide(Course $course)
    {
        $this->authorize('hide', $course);
        $this->service->hide($course);

        return redirect()->back()->with('success', 'Curso ocultado.');
    }

    public function duplicate(Course $course)
    {
        $this->authorize('create', Course::class);

        $newCourse = $this->duplicationService->duplicate($course);

        return redirect()->back()->with('success', 'Curso clonado con contenido (ID #'.$newCourse->id.')');
    }
}
