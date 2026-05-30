<?php

use App\Http\Controllers\Student\CatalogController as StudentCatalogController;
use App\Http\Controllers\Student\CertificateController as StudentCertificateController;
use App\Http\Controllers\Student\ClassroomController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\LessonCommentController;
use App\Http\Controllers\Student\LiveClassController as StudentLiveClassController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\PurchaseIntentController;
use App\Http\Controllers\Student\SubscriptionPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard', [StudentDashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::prefix('student')->name('student.')->middleware(['auth'])->group(function () {
    Route::get('/courses', [StudentCourseController::class, 'courses'])->name('courses.index');
    Route::post('/courses/{course:slug}/enroll-free', [StudentCourseController::class, 'enrollFree'])->name('courses.enroll-free');
    Route::get('/live-classes', [StudentLiveClassController::class, 'liveClasses'])->name('live-classes.index');
    Route::get('/exams', [StudentExamController::class, 'exams'])->name('exams.index');
    Route::get('/exams/{quiz}/take', [StudentExamController::class, 'takeExam'])->name('exams.take');
    Route::post('/exams/{quiz}/submit', [StudentExamController::class, 'submitExam'])->name('exams.submit');
    Route::get('/certificates', [StudentCertificateController::class, 'certificates'])->name('certificates.index');
    Route::get('/certificates/{certificate}/download', [StudentCertificateController::class, 'downloadCertificate'])->name('certificates.download');
    Route::get('/classroom/{course:slug}/{lesson?}', [ClassroomController::class, 'show'])->name('classroom');
    Route::post('/classroom/progress', [ClassroomController::class, 'updateProgress'])->name('classroom.progress');

    // Explore Catalog
    Route::get('/explore/courses', [StudentCourseController::class, 'exploreCourses'])->name('explore.courses');
    Route::get('/explore/publications', [StudentCatalogController::class, 'explorePublications'])->name('explore.publications');
    Route::get('/publications/books/{book}/download', [StudentCatalogController::class, 'downloadBook'])->name('publications.books.download');
    Route::get('/publications/books/{book}/interest', [StudentCatalogController::class, 'bookPurchaseInterest'])->name('publications.books.interest');
    Route::get('/publications/articles/{article}/download', [StudentCatalogController::class, 'downloadArticle'])->name('publications.articles.download');
    Route::get('/explore/masterclass', [StudentCatalogController::class, 'exploreMasterclasses'])->name('explore.masterclasses');
    Route::get('/explore/consultoria', [StudentCatalogController::class, 'exploreConsultoria'])->name('explore.consultoria');

    // Lesson Comments (throttled to prevent spam)
    Route::middleware('throttle:30,1')->group(function () {
        Route::post('/comments/{lesson}', [LessonCommentController::class, 'store'])->name('comments.store');
        Route::put('/comments/{comment}', [LessonCommentController::class, 'update'])->name('comments.update');
        Route::delete('/comments/{comment}', [LessonCommentController::class, 'destroy'])->name('comments.destroy');
        Route::post('/comments/{comment}/like', [LessonCommentController::class, 'toggleLike'])->name('comments.like');
    });

    // Pagos Membresías
    Route::post('/subscriptions/payment', [SubscriptionPaymentController::class, 'store'])->name('subscriptions.payment.store');
    Route::get('/subscriptions/payment/status', [SubscriptionPaymentController::class, 'status'])->name('subscriptions.payment.status');

    Route::post('/purchase-intent', [PurchaseIntentController::class, 'store'])->name('purchase-intent.store');

    // Pagos
    Route::get('/payments', [StudentPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [StudentPaymentController::class, 'store'])->name('payments.store');
    Route::post('/payments/{payment}/comprobante', [StudentPaymentController::class, 'updateComprobante'])->name('payments.comprobante');

    // Perfil Avanzado
    Route::get('/perfil', [StudentProfileController::class, 'profile'])->name('profile.index');
    Route::post('/perfil/update', [StudentProfileController::class, 'updateProfile'])->name('profile.custom.update');
});
