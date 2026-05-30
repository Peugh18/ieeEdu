<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 50, 300),
            'sale_price' => null,
            'discount' => 0.0,
            'duration_weeks' => $this->faker->numberBetween(4, 12),
            'is_featured' => $this->faker->boolean(20),
            'certificate_enabled' => true,
            'type' => $this->faker->randomElement(['grabado', 'en_vivo', 'evento']),
            'status' => 'PUBLICADO',
            'category_id' => Category::factory(),
        ];
    }
}
