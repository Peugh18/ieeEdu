<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            ['section' => 'home', 'order' => 1, 'heading' => 'Masterclasses en vivo con expertos', 'subheading' => 'Sesiones exclusivas con líderes del sector empresarial y público. Tu primera masterclass es completamente gratuita.', 'button_text' => 'Ver Masterclasses', 'button_link' => '/masterclass', 'position' => 'left', 'image_path' => '/images/empresa/banner-home.jpg'],
            ['section' => 'home', 'order' => 2, 'heading' => 'Fortalece tus competencias profesionales', 'subheading' => 'Cursos en vivo y grabados diseñados por expertos en economía, gestión pública, liderazgo y más — para el profesional peruano.', 'button_text' => 'Explorar Cursos', 'button_link' => '/cursos', 'position' => 'center', 'image_path' => '/images/empresa/banner-home.jpg'],
            ['section' => 'home', 'order' => 3, 'heading' => 'Visión Estratégica y Liderazgo', 'subheading' => 'Programas especializados para impulsar tu crecimiento directivo.', 'button_text' => 'Ver Programas', 'button_link' => '/planes', 'position' => 'right', 'image_path' => '/images/empresa/banner-home.jpg'],
            ['section' => 'consultoria', 'order' => 1, 'heading' => 'Arquitectura Estratégica para Decisiones Críticas', 'subheading' => 'En IEE, transformamos desafíos complejos en ventajas competitivas. Nuestra consultoría técnica especializada combina rigor académico con pragmatismo empresarial.', 'button_text' => 'Agendar Consultoría', 'button_link' => '#', 'position' => 'right', 'image_path' => '/images/empresa/banner-consultoria.jpg'],
            ['section' => 'cursos', 'order' => 1, 'heading' => 'Catálogo de Programas', 'subheading' => 'Encuentra el programa de especialización perfecto para potenciar tu carrera profesional.', 'button_text' => '', 'button_link' => '', 'position' => 'center', 'image_path' => '/images/empresa/banner-cursos.jpg'],
            ['section' => 'publicaciones', 'order' => 1, 'heading' => 'Nuestras Publicaciones', 'subheading' => 'Investigación y análisis de alto impacto para la toma de decisiones.', 'button_text' => '', 'button_link' => '', 'position' => 'center', 'image_path' => '/images/empresa/banner-publicaciones.jpg'],
            ['section' => 'masterclass', 'order' => 1, 'heading' => 'Masterclass Premium', 'subheading' => 'Aprende de los líderes del sector en sesiones magistrales exclusivas.', 'button_text' => 'Ver Sesiones', 'button_link' => '/masterclass', 'position' => 'left', 'image_path' => '/images/empresa/banner-masterclass.jpg']
        ];

        foreach ($banners as $b) {
            Banner::updateOrCreate(
                ['section' => $b['section'], 'order' => $b['order']],
                $b
            );
        }
    }
}
