<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Article;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Support\Str;

class PublicationsAndMasterclassSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Asegurar la existencia de algunas categorías de prueba
        $categories = [
            'Economía y Finanzas',
            'Gestión Pública',
            'Estrategia Empresarial',
            'Liderazgo y Habilidades Blandas'
        ];

        $categoryModels = [];
        foreach ($categories as $catName) {
            $categoryModels[] = Category::firstOrCreate(
                ['slug' => Str::slug($catName)],
                ['name' => $catName]
            );
        }
        $catIds = collect($categoryModels)->pluck('id')->toArray();

        // 2. Sembrar 10 Libros y Guías de Prueba
        $bookTemplates = [
            [
                'category' => 'Libro',
                'title' => 'Macroeconomía Aplicada a Mercados Emergentes',
                'price' => 75.00,
                'author' => 'Dr. Alejandro Thorne',
                'description' => 'Un análisis riguroso sobre las políticas macroeconómicas en América Latina y los impactos globales.',
                'cover_image' => null,
                'is_available' => true,
            ],
            [
                'category' => 'Libro',
                'title' => 'Gestión y Finanzas Corporativas Avanzadas',
                'price' => 120.00,
                'author' => 'MBA. Sofía Rodríguez',
                'description' => 'Guía completa sobre valoración de empresas, fusiones y adquisiciones en mercados latinoamericanos.',
                'cover_image' => null,
                'is_available' => true,
            ],
            [
                'category' => 'Guía',
                'title' => 'Guía Básica de Inversión en Bolsa de Valores',
                'price' => 0.00,
                'author' => 'Dr. Carlos Mendoza',
                'description' => 'Manual introductorio para jóvenes profesionales que desean iniciar en el trading y la inversión a largo plazo.',
                'cover_image' => null,
                'is_available' => true,
                'download_url' => 'https://example.com/books/guia-bolsa.pdf',
            ],
            [
                'category' => 'Libro en camino',
                'title' => 'Economía Conductual: Sesgos y Decisiones',
                'price' => 0.00,
                'author' => 'Dra. María Elena Pezo',
                'description' => 'Un libro pionero en español que examina cómo la psicología moldea el comportamiento de consumo y finanzas.',
                'cover_image' => null,
                'is_available' => true,
            ],
            [
                'category' => 'Libro',
                'title' => 'Políticas Públicas para el Desarrollo Sostenible',
                'price' => 95.00,
                'author' => 'Dr. Ricardo Valdivia',
                'description' => 'Análisis y propuestas técnicas para el desarrollo de infraestructura en gobiernos locales.',
                'cover_image' => null,
                'is_available' => true,
            ],
            [
                'category' => 'Guía',
                'title' => 'Guía Práctica del Presupuesto Público por Resultados',
                'price' => 0.00,
                'author' => 'Mg. Fernando Lazo',
                'description' => 'Una guía rápida y técnica para entender cómo el estado asigna y evalúa el dinero fiscal.',
                'cover_image' => null,
                'is_available' => true,
                'download_url' => 'https://example.com/books/guia-presupuesto.pdf',
            ],
            [
                'category' => 'Libro',
                'title' => 'Manual de Negociación Estratégica Internacional',
                'price' => 110.00,
                'author' => 'MBA. John C. Maxwell',
                'description' => 'Estrategias de negociación complejas para gerentes y líderes corporativos en mercados globales.',
                'cover_image' => null,
                'is_available' => false, // Agotado
            ],
            [
                'category' => 'Libro en camino',
                'title' => 'Inteligencia Artificial aplicada al Análisis Financiero',
                'price' => 0.00,
                'author' => 'Dr. Julio Verne',
                'description' => 'La revolución tecnológica y el uso de machine learning para predecir precios de activos y riesgos corporativos.',
                'cover_image' => null,
                'is_available' => true,
            ],
            [
                'category' => 'Libro',
                'title' => 'Teoría de Juegos y Estrategias Competitivas',
                'price' => 85.00,
                'author' => 'Dra. Patricia Alva',
                'description' => 'Modelos matemáticos aplicados a la toma de decisiones empresariales en condiciones de incertidumbre y rivalidad.',
                'cover_image' => null,
                'is_available' => true,
            ],
            [
                'category' => 'Guía',
                'title' => 'Guía Rápida para el Plan de Negocios Corporativo',
                'price' => 0.00,
                'author' => 'Mg. Alberto Fujimoto',
                'description' => 'Estructura paso a paso para formular planes de negocios ganadores enfocados en levantar capital.',
                'cover_image' => null,
                'is_available' => true,
                'download_url' => 'https://example.com/books/guia-plan-negocios.pdf',
            ]
        ];

        foreach ($bookTemplates as $bk) {
            Book::create($bk);
        }

        // 3. Sembrar 10 Artículos de Análisis de Prueba
        $articleTemplates = [
            [
                'title' => 'El impacto del tipo de cambio y las tasas de interés en la canasta básica familiar',
                'media' => 'Diario Gestión',
                'published_at' => '2026-05-10',
                'thumbnail' => null,
                'download_url' => 'https://gestion.pe',
            ],
            [
                'title' => 'Análisis del crecimiento del PBI en el primer trimestre y proyecciones anuales',
                'media' => 'El Peruano',
                'published_at' => '2026-04-18',
                'thumbnail' => null,
                'download_url' => 'https://elperuano.pe',
            ],
            [
                'title' => 'La transformación digital bancaria y el aumento de la inclusión financiera rural',
                'media' => 'Revista América Economía',
                'published_at' => '2026-03-22',
                'thumbnail' => null,
                'download_url' => 'https://americaeconomia.com',
            ],
            [
                'title' => 'Perspectivas de la política monetaria frente al aumento de la inflación subyacente',
                'media' => 'Boletín BCR',
                'published_at' => '2026-02-14',
                'thumbnail' => null,
                'download_url' => 'https://bcrp.gob.pe',
            ],
            [
                'title' => 'Estrategias de internacionalización para empresas agroexportadoras de la región',
                'media' => 'Business News',
                'published_at' => '2026-05-01',
                'thumbnail' => null,
                'download_url' => 'https://example.com',
            ],
            [
                'title' => 'Fintechs y la democratización del acceso al crédito para medianas empresas',
                'media' => 'Diario El Comercio',
                'published_at' => '2026-01-30',
                'thumbnail' => null,
                'download_url' => 'https://elcomercio.pe',
            ],
            [
                'title' => 'Modelos de Alianzas Público-Privadas para destrabar proyectos de infraestructura regional',
                'media' => 'Semana Económica',
                'published_at' => '2026-05-15',
                'thumbnail' => null,
                'download_url' => 'https://semanaeconomica.com',
            ],
            [
                'title' => 'La crisis de la cadena de suministro global y su impacto en las importaciones peruanas',
                'media' => 'Bloomberg Línea',
                'published_at' => '2026-03-10',
                'thumbnail' => null,
                'download_url' => 'https://bloomberglinea.com',
            ],
            [
                'title' => 'El rol del liderazgo femenino en la alta dirección de los grupos corporativos',
                'media' => 'Revista Forbes',
                'published_at' => '2026-04-05',
                'thumbnail' => null,
                'download_url' => 'https://forbes.pe',
            ],
            [
                'title' => 'Desafíos de la descentralización fiscal: ¿Por qué no se ejecuta el presupuesto regional?',
                'media' => 'Instituto IEE Press',
                'published_at' => '2026-05-20',
                'thumbnail' => null,
                'download_url' => 'https://example.com',
            ]
        ];

        foreach ($articleTemplates as $art) {
            Article::create($art);
        }

        // 4. Sembrar 10 Masterclasses (Courses of type 'evento', status 'PUBLICADO')
        $masterclassTemplates = [
            [
                'title' => 'Masterclass: Análisis de la Coyuntura Económica y Financiera',
                'description' => 'Un análisis en profundidad de las tendencias económicas globales, tasas de interés y su repercusión en la rentabilidad empresarial.',
                'price' => 100.00,
                'sale_price' => 0.00,
                'type' => 'evento',
                'status' => 'PUBLICADO',
                'start_date' => '2026-06-15',
                'start_time' => '19:00:00',
                'duration_weeks' => 1,
                'class_hours' => 2.0,
                'is_featured' => true,
                'image' => null,
                'whatsapp_link' => 'https://wa.me/51959166911?text=Hola,%20quiero%20inscribirme%20en%20la%20Masterclass%20de%20Coyuntura%20Econ%C3%B3mica.',
                'certificate_enabled' => false,
            ],
            [
                'title' => 'Masterclass: Estrategias de Inversión y Portafolios de Renta Fija',
                'description' => 'Cómo proteger y hacer crecer el capital corporativo utilizando bonos del tesoro, depósitos y activos de renta fija.',
                'price' => 120.00,
                'sale_price' => 0.00,
                'type' => 'evento',
                'status' => 'PUBLICADO',
                'start_date' => '2026-06-20',
                'start_time' => '18:30:00',
                'duration_weeks' => 1,
                'class_hours' => 2.5,
                'is_featured' => true,
                'image' => null,
                'whatsapp_link' => 'https://wa.me/51959166911?text=Hola,%20quiero%20inscribirme%20en%20la%20Masterclass%20de%20Portafolios%20de%20Renta%20Fija.',
                'certificate_enabled' => false,
            ],
            [
                'title' => 'Masterclass: Liderazgo Disruptivo para Equipos de Alto Rendimiento',
                'description' => 'Técnicas de management modernas inspiradas en Silicon Valley para motivar, retener talento y liderar el cambio.',
                'price' => 150.00,
                'sale_price' => 0.00,
                'type' => 'evento',
                'status' => 'PUBLICADO',
                'start_date' => '2026-06-28',
                'start_time' => '19:00:00',
                'duration_weeks' => 1,
                'class_hours' => 2.0,
                'is_featured' => true,
                'image' => null,
                'whatsapp_link' => 'https://wa.me/51959166911?text=Hola,%20quiero%20inscribirme%20en%20la%20Masterclass%20de%20Liderazgo%20Disruptivo.',
                'certificate_enabled' => false,
            ],
            [
                'title' => 'Masterclass: Estructuración de Fideicomisos y Fondos de Inversión',
                'description' => 'Conceptos avanzados sobre el uso de patrimonios autónomos para financiar megaproyectos y garantizar transacciones complejas.',
                'price' => 180.00,
                'sale_price' => 150.00,
                'type' => 'evento',
                'status' => 'PUBLICADO',
                'start_date' => '2026-07-02',
                'start_time' => '19:00:00',
                'duration_weeks' => 1,
                'class_hours' => 3.0,
                'is_featured' => true,
                'image' => null,
                'whatsapp_link' => 'https://wa.me/51959166911?text=Hola,%20quiero%20inscribirme%20en%20la%20Masterclass%20de%20Fideicomisos.',
                'certificate_enabled' => false,
            ],
            [
                'title' => 'Masterclass: Marketing de Servicios Profesionales y B2B de Consultoría',
                'description' => 'Estrategias comerciales efectivas para conseguir clientes de alto valor y vender proyectos de asesoría corporativa.',
                'price' => 80.00,
                'sale_price' => 0.00,
                'type' => 'evento',
                'status' => 'PUBLICADO',
                'start_date' => '2026-07-10',
                'start_time' => '18:00:00',
                'duration_weeks' => 1,
                'class_hours' => 2.0,
                'is_featured' => false,
                'image' => null,
                'whatsapp_link' => 'https://wa.me/51959166911?text=Hola,%20quiero%20inscribirme%20en%20la%20Masterclass%20de%20Marketing%20B2B.',
                'certificate_enabled' => false,
            ],
            [
                'title' => 'Masterclass: Planificación Financiera y Reducción del Gasto Corriente',
                'description' => 'Aprenda a diseñar presupuestos base cero y planes de ahorro para optimizar las utilidades corporativas en tiempos difíciles.',
                'price' => 110.00,
                'sale_price' => 0.00,
                'type' => 'evento',
                'status' => 'PUBLICADO',
                'start_date' => '2026-07-15',
                'start_time' => '19:30:00',
                'duration_weeks' => 1,
                'class_hours' => 2.0,
                'is_featured' => false,
                'image' => null,
                'whatsapp_link' => 'https://wa.me/51959166911?text=Hola,%20quiero%20inscribirme%20en%20la%20Masterclass%20de%20Planificaci%C3%B3n%20Financiera.',
                'certificate_enabled' => false,
            ],
            [
                'title' => 'Masterclass: Negociación Bajo Presión en Sindicatos y Conflictos Sociales',
                'description' => 'Habilidades gerenciales avanzadas para resolver huelgas, desacuerdos colectivos y coordinar el diálogo comunitario.',
                'price' => 200.00,
                'sale_price' => 0.00,
                'type' => 'evento',
                'status' => 'PUBLICADO',
                'start_date' => '2026-07-22',
                'start_time' => '19:00:00',
                'duration_weeks' => 1,
                'class_hours' => 3.0,
                'is_featured' => true,
                'image' => null,
                'whatsapp_link' => 'https://wa.me/51959166911?text=Hola,%20quiero%20inscribirme%20en%20la%20Masterclass%20de%20Negociaci%C3%B3n%20Bajo%20Presi%C3%B3n.',
                'certificate_enabled' => false,
            ],
            [
                'title' => 'Masterclass: Modelos de Asociaciones Público Privadas (APP)',
                'description' => 'Cómo formular y calificar proyectos de concesión de obras y servicios con el Estado Peruano (ProInversión).',
                'price' => 140.00,
                'sale_price' => 0.00,
                'type' => 'evento',
                'status' => 'PUBLICADO',
                'start_date' => '2026-07-30',
                'start_time' => '18:30:00',
                'duration_weeks' => 1,
                'class_hours' => 2.5,
                'is_featured' => false,
                'image' => null,
                'whatsapp_link' => 'https://wa.me/51959166911?text=Hola,%20quiero%20inscribirme%20en%20la%20Masterclass%20de%20APP.',
                'certificate_enabled' => false,
            ],
            [
                'title' => 'Masterclass: Análisis de la Nueva Ley de Contrataciones del Estado',
                'description' => 'Principales cambios y estrategias para empresas proveedoras del estado peruano bajo el nuevo marco regulatorio.',
                'price' => 130.00,
                'sale_price' => 100.00,
                'type' => 'evento',
                'status' => 'PUBLICADO',
                'start_date' => '2026-08-05',
                'start_time' => '19:00:00',
                'duration_weeks' => 1,
                'class_hours' => 2.0,
                'is_featured' => true,
                'image' => null,
                'whatsapp_link' => 'https://wa.me/51959166911?text=Hola,%20quiero%20inscribirme%20en%20la%20Masterclass%20de%20Ley%20de%20Contrataciones.',
                'certificate_enabled' => false,
            ],
            [
                'title' => 'Masterclass: Dirección Estratégica y OKRs en Organizaciones Complejas',
                'description' => 'Establecimiento de metas trimestrales alineadas para lograr el crecimiento exponencial y la cohesión de equipos operativos.',
                'price' => 160.00,
                'sale_price' => 0.00,
                'type' => 'evento',
                'status' => 'PUBLICADO',
                'start_date' => '2026-08-12',
                'start_time' => '19:00:00',
                'duration_weeks' => 1,
                'class_hours' => 2.0,
                'is_featured' => true,
                'image' => null,
                'whatsapp_link' => 'https://wa.me/51959166911?text=Hola,%20quiero%20inscribirme%20en%20la%20Masterclass%20de%20OKRs.',
                'certificate_enabled' => false,
            ]
        ];

        foreach ($masterclassTemplates as $mc) {
            $mc['category_id'] = $catIds[array_rand($catIds)];
            $mc['slug'] = Str::slug($mc['title']) . '-' . rand(100, 999);
            Course::create($mc);
        }
    }
}
