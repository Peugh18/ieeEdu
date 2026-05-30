<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseQuiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseQuiz>
 */
class CourseQuizFactory extends Factory
{
    protected $model = CourseQuiz::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => 'Examen final',
            'time_limit' => 60,
            'max_attempts' => 2,
            'minimum_score' => 70,
            'randomize_questions' => false,
        ];
    }
}
