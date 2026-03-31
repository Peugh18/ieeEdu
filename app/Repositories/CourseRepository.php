<?php

namespace App\Repositories;

use App\Models\Course;

class CourseRepository
{
    public function paginate($perPage = 15, $filters = [])
    {
        $query = Course::query()->with(['category', 'teacher']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find($id)
    {
        return Course::with(['category', 'teacher'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Course::create($data);
    }

    public function update(Course $course, array $data)
    {
        $course->update($data);

        return $course;
    }

    public function delete(Course $course)
    {
        return $course->delete();
    }
}
