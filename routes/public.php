<?php

use App\Http\Controllers\ConsultancyController;
use App\Http\Controllers\PublicCourseController;
use App\Http\Controllers\PublicPublicationController;
use App\Http\Controllers\WhatsappLeadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicCourseController::class, 'welcome'])->name('home');

Route::get('/nosotros', function () {
    return Inertia\Inertia::render('Nosotros');
})->name('nosotros');

Route::get('/cursos', [PublicCourseController::class, 'index'])->name('cursos.index');
Route::get('/masterclass', [PublicCourseController::class, 'masterclasses'])->name('masterclass.index');
Route::get('/cursos/{slug}', [PublicCourseController::class, 'show'])->name('cursos.show');
Route::get('/planes', [PublicCourseController::class, 'planes'])->name('planes');

Route::get('/publicaciones', [PublicPublicationController::class, 'index'])->name('publicaciones.index');
Route::get('/consultoria', [ConsultancyController::class, 'index'])->name('consultoria');

Route::post('/consultoria/solicitud', [ConsultancyController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('consultoria.store');

Route::post('/api/leads/whatsapp', [WhatsappLeadController::class, 'trackWhatsApp'])
    ->middleware('throttle:10,1')
    ->name('api.leads.whatsapp');

Route::get('/terminos-y-condiciones', function () {
    return Inertia\Inertia::render('Terms');
})->name('terms');

Route::get('/politica-de-privacidad', function () {
    return Inertia\Inertia::render('Privacy');
})->name('privacy');
