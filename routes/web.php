<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PublicCourseController;

Route::get('/', [PublicCourseController::class, 'welcome'])->name('home');

Route::get('/cursos', [PublicCourseController::class, 'index'])->name('cursos.index');
Route::get('/masterclass', [PublicCourseController::class, 'masterclasses'])->name('masterclass.index');
Route::get('/cursos/{slug}', [PublicCourseController::class, 'show'])->name('cursos.show');
Route::get('/planes', [PublicCourseController::class, 'planes'])->name('planes');

Route::get('/publicaciones', [App\Http\Controllers\PublicPublicationController::class, 'index'])->name('publicaciones.index');
Route::get('/consultoria', function () {
    $banner = \App\Models\Banner::where('section', 'consultoria')->orderBy('order')->first();
    return Inertia::render('Consultoria', [
        'banner' => $banner,
    ]);
})->name('consultoria');

use App\Http\Controllers\Student\DashboardController as StudentDashboardController;

Route::get('dashboard', [StudentDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::prefix('student')->name('student.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/courses', [StudentDashboardController::class, 'courses'])->name('courses.index');
    Route::get('/live-classes', [StudentDashboardController::class, 'liveClasses'])->name('live-classes.index');
    Route::get('/exams', [StudentDashboardController::class, 'exams'])->name('exams.index');
    Route::get('/exams/{quiz}/take', [StudentDashboardController::class, 'takeExam'])->name('exams.take');
    Route::post('/exams/{quiz}/submit', [StudentDashboardController::class, 'submitExam'])->name('exams.submit');
    Route::get('/certificates', [StudentDashboardController::class, 'certificates'])->name('certificates.index');
    Route::get('/certificates/{certificate}/download', [StudentDashboardController::class, 'downloadCertificate'])->name('certificates.download');
    Route::get('/classroom/{course:slug}/{lesson?}', [\App\Http\Controllers\Student\ClassroomController::class, 'show'])->name('classroom');
    Route::post('/classroom/progress', [\App\Http\Controllers\Student\ClassroomController::class, 'updateProgress'])->name('classroom.progress');

    // Explore Catalog
    Route::get('/explore/courses', [StudentDashboardController::class, 'exploreCourses'])->name('explore.courses');
    Route::get('/explore/publications', [StudentDashboardController::class, 'explorePublications'])->name('explore.publications');
    Route::get('/explore/masterclass', [StudentDashboardController::class, 'exploreMasterclasses'])->name('explore.masterclasses');
    Route::get('/explore/consultoria', [StudentDashboardController::class, 'exploreConsultoria'])->name('explore.consultoria');

    // Lesson Comments
    Route::post('/comments/{lesson}', [\App\Http\Controllers\Student\LessonCommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [\App\Http\Controllers\Student\LessonCommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\Student\LessonCommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/like', [\App\Http\Controllers\Student\LessonCommentController::class, 'toggleLike'])->name('comments.like');

    // Perfil Avanzado
    Route::get('/perfil', [StudentDashboardController::class, 'profile'])->name('profile.index');
    Route::post('/perfil/update', [StudentDashboardController::class, 'updateProfile'])->name('profile.custom.update');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Middleware\EnsureAdmin;



Route::prefix('admin')->name('admin.')->middleware(['auth', EnsureAdmin::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/report', [DashboardController::class, 'downloadReport'])->name('dashboard.report');

    Route::resource('users', UserController::class)->only(['index','store','show','update','destroy']);
    Route::get('users-search', [UserController::class, 'search'])->name('users.search');
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::post('users/{user}/assign-course', [UserController::class, 'assignCourse'])->name('users.assignCourse');
    Route::get('users-export', [UserController::class, 'export'])->name('users.export');
    Route::resource('categories', CategoryController::class)->only(['index','store','update','destroy']);
    Route::resource('courses', CourseController::class)->only(['index','store','update','destroy']);
    Route::get('courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');

    Route::patch('courses/{course}/publish', [CourseController::class, 'publish'])->name('courses.publish');
    Route::patch('courses/{course}/hide', [CourseController::class, 'hide'])->name('courses.hide');
    Route::post('courses/{course}/duplicate', [CourseController::class, 'duplicate'])->name('courses.duplicate');

    Route::resource('books', BookController::class)->only(['index','store','update','destroy']);
    Route::resource('articles', ArticleController::class)->only(['index','store','update','destroy']);

    Route::get('banners', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('banners.index');
    Route::post('banners', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('banners.store');

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
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::patch('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::patch('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

    // Subscriptions (Planes Premium)
    Route::get('subscriptions', [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('subscriptions', [\App\Http\Controllers\Admin\SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::patch('subscriptions/{subscription}/toggle', [\App\Http\Controllers\Admin\SubscriptionController::class, 'toggleStatus'])->name('subscriptions.toggle');
    Route::delete('subscriptions/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

    // Certificate Templates
    Route::get('courses/{course}/certificate-template', [\App\Http\Controllers\Admin\CertificateTemplateController::class, 'edit'])->name('courses.certificate-template.edit');
    Route::post('courses/{course}/certificate-template', [\App\Http\Controllers\Admin\CertificateTemplateController::class, 'update'])->name('courses.certificate-template.update');
});
