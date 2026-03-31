<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\PublicCourseController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/cursos', [PublicCourseController::class, 'index'])->name('cursos.index');
Route::get('/cursos/{slug}', [PublicCourseController::class, 'show'])->name('cursos.show');

Route::get('/ebooks', function () {
    return Inertia::render('Ebooks');
})->name('ebooks');

Route::get('dashboard', function () {
    $user = Auth::user();

    if ($user && $user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Middleware\EnsureAdmin;

Route::get('/dev/make-admin', function () {
    $admin = App\Models\User::updateOrCreate(
        ['email' => 'admin@example.com'],
        ['name' => 'Admin User', 'password' => bcrypt('password'), 'role' => 'admin', 'status' => 'activo', 'telefono' => '999999999']
    );

    return "Admin user ready: {$admin->email} - password: password";
});

Route::prefix('admin')->name('admin.')->middleware(['auth', EnsureAdmin::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->only(['index','store','update','destroy']);
    Route::resource('courses', CourseController::class)->only(['index','store','update','destroy']);
    Route::get('courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::resource('categories', CategoryController::class)->only(['index','store','update','destroy']);

    Route::patch('courses/{course}/publish', [CourseController::class, 'publish'])->name('courses.publish');
    Route::patch('courses/{course}/hide', [CourseController::class, 'hide'])->name('courses.hide');

    Route::get('courses/{course}/modules', [\App\Http\Controllers\Admin\ModuleController::class, 'index'])->name('courses.modules.index');
    Route::post('courses/{course}/modules', [\App\Http\Controllers\Admin\ModuleController::class, 'store'])->name('courses.modules.store');
    Route::put('modules/{module}', [\App\Http\Controllers\Admin\ModuleController::class, 'update'])->name('modules.update');
    Route::delete('modules/{module}', [\App\Http\Controllers\Admin\ModuleController::class, 'destroy'])->name('modules.destroy');
    Route::patch('courses/{course}/modules/reorder', [\App\Http\Controllers\Admin\ModuleController::class, 'reorder'])->name('courses.modules.reorder');

    Route::get('courses/{course}/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'index'])->name('courses.lessons.index');
    Route::post('courses/{course}/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'store'])->name('courses.lessons.store');
    Route::put('lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'update'])->name('lessons.update');
    Route::delete('lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'destroy'])->name('lessons.destroy');
    Route::patch('courses/{course}/lessons/reorder', [\App\Http\Controllers\Admin\LessonController::class, 'reorder'])->name('courses.lessons.reorder');

    Route::get('lessons/{lesson}/materials', [\App\Http\Controllers\Admin\MaterialController::class, 'index'])->name('lessons.materials.index');
    Route::post('lessons/{lesson}/materials', [\App\Http\Controllers\Admin\MaterialController::class, 'store'])->name('lessons.materials.store');
    Route::put('materials/{material}', [\App\Http\Controllers\Admin\MaterialController::class, 'update'])->name('materials.update');
    Route::delete('materials/{material}', [\App\Http\Controllers\Admin\MaterialController::class, 'destroy'])->name('materials.destroy');

    Route::get('courses/{course}/quizzes', [\App\Http\Controllers\Admin\QuizController::class, 'index'])->name('courses.quizzes.index');
    Route::post('courses/{course}/quizzes', [\App\Http\Controllers\Admin\QuizController::class, 'store'])->name('courses.quizzes.store');
    Route::put('quizzes/{quiz}', [\App\Http\Controllers\Admin\QuizController::class, 'update'])->name('quizzes.update');
    Route::delete('quizzes/{quiz}', [\App\Http\Controllers\Admin\QuizController::class, 'destroy'])->name('quizzes.destroy');

    Route::post('questions', [\App\Http\Controllers\Admin\QuestionController::class, 'store'])->name('questions.store');
    Route::put('questions/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'update'])->name('questions.update');
    Route::delete('questions/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'destroy'])->name('questions.destroy');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::patch('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::patch('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
});
