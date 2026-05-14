<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use Illuminate\Support\Str;

class CourseCarouselSeeder extends Seeder
{
    public function run()
    {
        // 5 Cursos en vivo
        for ($i = 1; $i <= 5; $i++) {
            Course::create([
                'title' => 'Curso en Vivo de Especialización ' . $i,
                'description' => 'Un curso espectacular en vivo con metodología comprobada para líderes.',
                'price' => 120.00,
                'sale_price' => 90.00,
                'type' => 'en vivo',
                'status' => 'publicado',
                'start_date' => now()->addDays($i * 5)->format('Y-m-d'),
                'is_featured' => true,
                'image' => null, // usa placeholder default
                'certificate_enabled' => true,
            ]);
        }

        // 5 Cursos grabados
        for ($i = 1; $i <= 5; $i++) {
            Course::create([
                'title' => 'Programa Grabado de Gestión ' . $i,
                'description' => 'Aprende a tu propio ritmo con nuestros programas asíncronos.',
                'price' => 150.00,
                'sale_price' => 0.00, // Sin descuento
                'type' => 'grabado',
                'status' => 'publicado',
                'start_date' => now()->subDays($i * 10)->format('Y-m-d'),
                'is_featured' => true,
                'image' => null,
                'certificate_enabled' => true,
            ]);
        }
    }
}
