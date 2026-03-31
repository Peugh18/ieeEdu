<?php

namespace App\Repositories;

use App\Models\Enrollment;

class EnrollmentRepository
{
    public function create(array $data)
    {
        return Enrollment::create($data);
    }

    public function find($id)
    {
        return Enrollment::findOrFail($id);
    }

    public function update(Enrollment $enrollment, array $data)
    {
        $enrollment->update($data);

        return $enrollment;
    }

    public function delete(Enrollment $enrollment)
    {
        return $enrollment->delete();
    }
}
