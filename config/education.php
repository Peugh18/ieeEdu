<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Education System Configuration
    |--------------------------------------------------------------------------
    |
    | Aquí se centralizan los parámetros de negocio del sistema educativo.
    |
    */

    'passing_score' => env('EDUCATION_PASSING_SCORE', 14),

    'max_quiz_attempts' => env('EDUCATION_MAX_ATTEMPTS', 3),

    'pagination' => [
        'courses' => 12,
        'records' => 15,
    ],

    'features' => [
        'certificates_enabled' => true,
        'comments_enabled' => true,
    ],
];
