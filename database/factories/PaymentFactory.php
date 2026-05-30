<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'status' => $this->faker->randomElement(['pendiente', 'en_revision', 'aprobado', 'rechazado']),
            'comprobante' => null,
            'amount' => $this->faker->randomFloat(2, 50, 300),
        ];
    }
}
