<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BookOrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\CertificateTemplateController;
use App\Http\Controllers\Admin\CompanySettingsController;
use App\Http\Controllers\Admin\ConsultancyRequestController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', EnsureAdmin::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/report', [DashboardController::class, 'downloadReport'])->name('dashboard.report');

    Route::resource('users', UserController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::get('users-search', [UserController::class, 'search'])->name('users.search');
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::post('users/{user}/assign-course', [UserController::class, 'assignCourse'])->name('users.assignCourse');
    Route::get('users-export', [UserController::class, 'export'])->name('users.export');
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('courses', CourseController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');

    Route::patch('courses/{course}/publish', [CourseController::class, 'publish'])->name('courses.publish');
    Route::patch('courses/{course}/hide', [CourseController::class, 'hide'])->name('courses.hide');
    Route::post('courses/{course}/duplicate', [CourseController::class, 'duplicate'])->name('courses.duplicate');

    Route::resource('books', BookController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('book-orders', [BookOrderController::class, 'index'])->name('book-orders.index');
    Route::patch('book-orders/{bookOrder}', [BookOrderController::class, 'update'])->name('book-orders.update');
    Route::resource('articles', ArticleController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('banners', [BannerController::class, 'index'])->name('banners.index');
    Route::post('banners', [BannerController::class, 'store'])->name('banners.store');

    Route::get('settings/company', [CompanySettingsController::class, 'edit'])->name('settings.company');
    Route::patch('settings/company', [CompanySettingsController::class, 'update'])->name('settings.company.update');
    Route::get('settings/plans', [SubscriptionPlanController::class, 'index'])->name('settings.plans');
    Route::patch('settings/plans/{subscriptionPlan}', [SubscriptionPlanController::class, 'update'])->name('settings.plans.update');

    Route::get('courses/{course}/modules', [ModuleController::class, 'index'])->name('courses.modules.index');
    Route::post('courses/{course}/modules', [ModuleController::class, 'store'])->name('courses.modules.store');
    Route::put('modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
    Route::delete('modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');
    Route::patch('courses/{course}/modules/reorder', [ModuleController::class, 'reorder'])->name('courses.modules.reorder');

    Route::get('courses/{course}/lessons', [LessonController::class, 'index'])->name('courses.lessons.index');
    Route::post('courses/{course}/lessons', [LessonController::class, 'store'])->name('courses.lessons.store');
    Route::put('lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');
    Route::patch('courses/{course}/lessons/reorder', [LessonController::class, 'reorder'])->name('courses.lessons.reorder');

    Route::get('lessons/{lesson}/materials', [MaterialController::class, 'index'])->name('lessons.materials.index');
    Route::post('lessons/{lesson}/materials', [MaterialController::class, 'store'])->name('lessons.materials.store');
    Route::put('materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
    Route::delete('materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

    Route::get('courses/{course}/quizzes', [QuizController::class, 'index'])->name('courses.quizzes.index');
    Route::post('courses/{course}/quizzes', [QuizController::class, 'store'])->name('courses.quizzes.store');
    Route::put('quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::delete('quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');

    Route::post('questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::put('questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::patch('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::patch('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::patch('payments/{payment}/revert', [PaymentController::class, 'revert'])->name('payments.revert');

    // Subscriptions (Planes Premium)
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::patch('subscriptions/{subscription}/toggle', [SubscriptionController::class, 'toggleStatus'])->name('subscriptions.toggle');
    Route::delete('subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

    // Certificates
    Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');

    // Consultancies
    Route::get('consultancies', [ConsultancyRequestController::class, 'index'])->name('consultancies.index');
    Route::patch('consultancies/{consultancyRequest}/status', [ConsultancyRequestController::class, 'updateStatus'])->name('consultancies.status');

    // Certificate Templates
    Route::get('courses/{course}/certificate-template', [CertificateTemplateController::class, 'edit'])->name('courses.certificate-template.edit');
    Route::post('courses/{course}/certificate-template', [CertificateTemplateController::class, 'update'])->name('courses.certificate-template.update');
});
