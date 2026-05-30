<?php

namespace App\Http\Controllers\Student;

use App\Actions\Student\GetContinueLearningAction;
use App\Actions\Student\GetCourseRecommendationsAction;
use App\Actions\Student\GetDashboardStatsAction;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CourseLesson;
use App\Models\Enrollment;
use App\Services\SubscriptionAccessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Dashboard Principal del Estudiante
     */
    public function index(
        GetDashboardStatsAction $getStats,
        GetContinueLearningAction $getContinue,
        GetCourseRecommendationsAction $getRecommendations
    ) {
        $user = Auth::user();

        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        app(SubscriptionAccessService::class)->sync($user->id);

        $dashboardData = Cache::remember('student_dashboard_'.$user->id, 60, function () use ($user, $getStats, $getContinue, $getRecommendations) {
            // Obtener IDs inscritos filtrados por acceso (pago o suscripción activa)
            $enrolledIds = Enrollment::where('user_id', $user->id)->visible()->pluck('course_id');

            // 1. Sesión en vivo próxima
            $nextLiveClass = CourseLesson::whereIn('course_id', $enrolledIds)
                ->whereNotNull('start_time')
                ->whereNull('video_url')
                ->where('start_time', '>=', now()->subHours(5))
                ->orderBy('start_time')
                ->first();

            return [
                'stats' => $getStats->execute($user),
                'continueLearning' => $getContinue->execute($user),
                'recommendations' => $getRecommendations->execute($user),
                'certificates' => $this->getRecentCertificates($user),
                'nextLiveClass' => $nextLiveClass ? [
                    'id' => $nextLiveClass->id,
                    'title' => $nextLiveClass->title,
                    'course_title' => $nextLiveClass->course?->title ?? 'Curso',
                    'start_time' => $nextLiveClass->start_time->format('Y-m-d H:i:s'),
                    'start_time_human' => $nextLiveClass->start_time->isoFormat('dddd D [de] MMMM [a las] h:mm A'),
                    'course_slug' => $nextLiveClass->course?->slug,
                ] : null,
            ];
        });

        return Inertia::render('Dashboard', $dashboardData);
    }

    protected function getRecentCertificates($user)
    {
        return Certificate::where('user_id', $user->id)
            ->whereHas('course', function ($q) {
                $q->where('certificate_enabled', true);
            })
            ->with('course')
            ->orderByDesc('issue_date')
            ->take(3)
            ->get()
            ->map(fn ($cert) => [
                'id' => $cert->id,
                'title' => 'Certificado de Aprobación',
                'course_title' => $cert->course->title ?? 'Curso',
                'issue_date' => $cert->issue_date->format('d M Y'),
                'image' => $cert->course->image ?? '/images/cert-placeholder.png',
                'code' => $cert->code ?? 'N/A',
                'is_available' => true,
                'download_url' => asset('storage/'.$cert->file_path),
            ]);
    }
}
