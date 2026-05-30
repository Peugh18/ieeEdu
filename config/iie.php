<?php

return [
    'whatsapp_sales' => env('IIE_WHATSAPP_SALES', '51959166911'),
    'whatsapp_support' => env('IIE_WHATSAPP_SUPPORT', '51934057867'),

    'planes' => [
        'trimestral' => [
            'id' => 'trimestral',
            'name' => 'Plan Trimestral',
            'badge' => 'Ideal para empezar',
            'badgeIcon' => 'rocket_launch',
            'price' => env('IIE_PLAN_TRIMESTRAL_PRICE', 350),
            'period' => '3 meses',
            'description' => 'Comienza tu desarrollo profesional ahora.',
            'color' => 'from-amber-400 to-amber-600',
        ],
        'semestral' => [
            'id' => 'semestral',
            'name' => 'Plan Semestral',
            'badge' => 'Más popular',
            'badgeIcon' => 'local_fire_department',
            'price' => env('IIE_PLAN_SEMESTRAL_PRICE', 600),
            'period' => '6 meses',
            'description' => 'Mejor relación calidad/precio.',
            'color' => 'from-blue-500 to-indigo-700',
        ],
        'anual' => [
            'id' => 'anual',
            'name' => 'Plan Anual',
            'badge' => 'Máximo ahorro',
            'badgeIcon' => 'savings',
            'price' => env('IIE_PLAN_ANUAL_PRICE', 990),
            'period' => '12 meses',
            'description' => 'Acceso total sin límites todo el año.',
            'color' => 'from-green-500 to-emerald-700',
        ],
    ],
];
