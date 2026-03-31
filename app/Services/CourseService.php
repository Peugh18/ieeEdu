<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\CourseRepository;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CourseService
{
    public function __construct(protected CourseRepository $repo)
    {
    }

    public function list($perPage = 15, $filters = [])
    {
        return $this->repo->paginate($perPage, $filters);
    }

    public function create(array $data): Course
    {
        $data['status'] = $data['status'] ?? 'BORRADOR';
        $data['slug'] = $data['slug'] ?? $this->generateSlug($data['title']);

        return $this->repo->create($data);
    }

    public function update(Course $course, array $data): Course
    {
        if (!empty($data['title']) && empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        }

        return $this->repo->update($course, $data);
    }

    protected function generateSlug(string $title): string
    {
        $slug = \Str::slug($title);
        $base = $slug;
        $count = 1;

        while (Course::where('slug', $slug)->exists()) {
            $slug = "$base-$count";
            $count++;
        }

        return $slug;
    }

    public function delete(Course $course)
    {
        return $this->repo->delete($course);
    }

    public function publish(Course $course)
    {
        $course->loadCount('lessons');

        if (($course->lessons_count ?? 0) < 1) {
            throw ValidationException::withMessages([
                'course' => 'No puedes publicar un curso sin al menos 1 clase.',
            ]);
        }

        // "evento" in DB acts as masterclass / charla (single class, no modules)
        if (in_array($course->type, ['masterclass', 'evento'], true) && ($course->lessons_count ?? 0) !== 1) {
            throw ValidationException::withMessages([
                'course' => 'Masterclass solo permite 1 clase.',
            ]);
        }

        return $this->repo->update($course, ['status' => 'PUBLICADO']);
    }

    public function hide(Course $course)
    {
        return $this->repo->update($course, ['status' => 'OCULTO']);
    }
}
