<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookDownload;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WhatsappLead;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardStatsService
{
    public function getStats(): array
    {
        $cacheKey = 'admin_dashboard_stats';
        $cacheMinutes = 5;

        return Cache::remember($cacheKey, $cacheMinutes * 60, function () {
            // ─── USUARIOS ────────────────────────────────────────────────
            $totalUsers = User::count();
            $activeUsers = User::where('status', 'activo')->count();
            $inactiveUsers = User::where('status', 'inactivo')->count();
            $premiumUsers = Subscription::where('status', Subscription::STATUS_ACTIVE)
                ->where('end_date', '>=', now())
                ->distinct('user_id')->count('user_id');
            $newUsersThisMonth = User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count();

            // ─── INGRESOS ─────────────────────────────────────────────────
            $totalIncome = (float) Payment::where('status', 'aprobado')->sum('amount');
            $subIncome = (float) Payment::where('status', 'aprobado')
                ->whereNull('course_id')
                ->whereNull('book_id')
                ->sum('amount');
            $courseIncome = (float) Payment::where('status', 'aprobado')->whereNotNull('course_id')->sum('amount');
            $bookIncome = (float) Payment::where('status', 'aprobado')->whereNotNull('book_id')->sum('amount');
            $bookSalesCount = Payment::where('status', 'aprobado')->whereNotNull('book_id')->count();

            $pendingPayments = Payment::where('status', 'pendiente')->count();
            $approvedPayments = Payment::where('status', 'aprobado')->count();

            // ─── CURSOS ───────────────────────────────────────────────────
            $totalCourses = Course::count();
            $publishedCourses = Course::where('status', 'PUBLICADO')->count();
            $draftCourses = Course::where('status', 'BORRADOR')->count();
            $recordedCourses = Course::where('type', 'grabado')->count();
            $liveCourses = Course::where('type', 'en_vivo')->count();

            // Top 5 courses por ventas
            $courseSales = Course::whereHas('payments', fn ($q) => $q->where('status', 'aprobado'))
                ->withCount(['payments as approved_payments_count' => fn ($q) => $q->where('status', 'aprobado')])
                ->withSum(['payments as total_earned' => fn ($q) => $q->where('status', 'aprobado')], 'amount')
                ->with('category:id,name')
                ->orderByDesc('approved_payments_count')
                ->limit(5)
                ->get(['id', 'title', 'type', 'price', 'category_id', 'image']);

            // ─── LIBROS ───────────────────────────────────────────────────
            $totalBooks = Book::count();
            $availableBooks = Book::where('is_available', true)->count();
            $totalBookDownloads = BookDownload::count();
            $bookDownloadsThisMonth = BookDownload::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count();
            $bookWhatsappLeads = BookDownload::where('source', 'whatsapp')->count();

            $topBooks = Book::query()
                ->withCount([
                    'downloads',
                    'payments as approved_sales_count' => fn ($q) => $q->where('status', 'aprobado'),
                ])
                ->withSum(['payments as total_earned' => fn ($q) => $q->where('status', 'aprobado')], 'amount')
                ->orderByDesc('total_earned')
                ->orderByDesc('downloads_count')
                ->limit(5)
                ->get(['id', 'title', 'category', 'price', 'author', 'cover_image']);

            // ─── LEAR ENGAGEMENT ──────────────────────────────────────────
            $totalEnrollments = Enrollment::count();
            $completedCourses = Enrollment::whereNotNull('completed_at')->count();
            $passedEnrollments = Enrollment::where('passed', true)->count();
            $approvalRate = $totalEnrollments ? ($passedEnrollments / $totalEnrollments) * 100 : 0;

            // ─── CERTIFICADOS & LEADS ─────────────────────────────────────
            $certificatesIssued = Certificate::count();
            $whatsappLeads = WhatsappLead::count();
            $whatsappLeadsMonth = WhatsappLead::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count();

            // ─── SUSCRIPCIONES ACTIVAS ────────────────────────────────────
            $activeSubs = Subscription::where('status', Subscription::STATUS_ACTIVE)
                ->where('end_date', '>=', now())->count();
            $expiredSubs = Subscription::where('status', Subscription::STATUS_EXPIRED)->count();

            // ─── CHARTS: Ingresos por periodo ─────────────────────────────
            $esDays = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            $esShort = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $nowPe = now()->setTimezone('America/Lima');

            // Pre-cargar datos agrupados por día para reducir queries N+1
            $startDate = $nowPe->copy()->subDays(6)->startOfDay();
            $endDate = $nowPe->copy()->endOfDay();

            $dailyPayments = Payment::where('status', 'aprobado')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date,
                    SUM(amount) as total,
                    SUM(CASE WHEN course_id IS NULL AND book_id IS NULL THEN amount ELSE 0 END) as subs,
                    SUM(CASE WHEN course_id IS NOT NULL THEN amount ELSE 0 END) as courses,
                    SUM(CASE WHEN book_id IS NOT NULL THEN amount ELSE 0 END) as books')
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            // Weekly (últimos 7 días)
            $weeklyChart = collect(range(0, 6))
                ->map(fn ($i) => $nowPe->copy()->subDays($i)->format('Y-m-d'))
                ->reverse()
                ->map(fn ($date) => [
                    'label' => $esDays[Carbon::parse($date)->dayOfWeek].' '.Carbon::parse($date)->format('d'),
                    'total' => (float) ($dailyPayments[$date]->total ?? 0),
                    'subs' => (float) ($dailyPayments[$date]->subs ?? 0),
                    'courses' => (float) ($dailyPayments[$date]->courses ?? 0),
                    'books' => (float) ($dailyPayments[$date]->books ?? 0),
                ])->values();

            // Monthly (últimas 4 semanas) — precargar rango completo
            $monthStart = $nowPe->copy()->subWeeks(4)->startOfDay();
            $monthEnd = $nowPe->copy()->endOfDay();

            $monthPayments = Payment::where('status', 'aprobado')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->selectRaw('DATE(created_at) as date,
                    SUM(amount) as total,
                    SUM(CASE WHEN course_id IS NULL AND book_id IS NULL THEN amount ELSE 0 END) as subs,
                    SUM(CASE WHEN course_id IS NOT NULL THEN amount ELSE 0 END) as courses,
                    SUM(CASE WHEN book_id IS NOT NULL THEN amount ELSE 0 END) as books')
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            $monthlyChart = collect(range(0, 3))->map(function ($i) use ($nowPe, $monthPayments) {
                $weekStart = $nowPe->copy()->subWeeks($i + 1)->startOfDay();
                $days = collect(range(0, 6))->map(fn ($d) => $weekStart->copy()->addDays($d)->format('Y-m-d'));

                return [
                    'label' => 'Semana '.(4 - $i),
                    'total' => (float) $days->sum(fn ($d) => (float) ($monthPayments[$d]->total ?? 0)),
                    'subs' => (float) $days->sum(fn ($d) => (float) ($monthPayments[$d]->subs ?? 0)),
                    'courses' => (float) $days->sum(fn ($d) => (float) ($monthPayments[$d]->courses ?? 0)),
                    'books' => (float) $days->sum(fn ($d) => (float) ($monthPayments[$d]->books ?? 0)),
                ];
            })->reverse()->values();

            // Quarterly & Annually — agrupado por mes
            $quarterStart = $nowPe->copy()->subMonths(6)->startOfMonth();
            $quarterEnd = $nowPe->copy()->endOfMonth();

            // En SQLite o MySQL la sintaxis del query de fechas varía, conservamos la lógica original pero usamos Carbon en php o raw sql si es compatible.
            // Para mantener compatibilidad con las BD del proyecto:
            $monthlyAggregated = Payment::where('status', 'aprobado')
                ->whereBetween('created_at', [$quarterStart, $quarterEnd])
                ->get()
                ->groupBy(fn ($r) => Carbon::parse($r->created_at)->setTimezone('America/Lima')->format('Y-m'));

            // Quarterly (últimos 3 meses)
            $quarterlyChart = collect(range(0, 2))->map(function ($i) use ($nowPe, $monthlyAggregated, $esShort) {
                $monthKey = $nowPe->copy()->subMonths($i)->format('Y-m');
                $rows = $monthlyAggregated->get($monthKey) ?? collect();

                return [
                    'label' => $esShort[$nowPe->copy()->subMonths($i)->month - 1],
                    'total' => (float) $rows->sum('amount'),
                    'subs' => (float) $rows->filter(fn ($r) => $r->course_id === null && $r->book_id === null)->sum('amount'),
                    'courses' => (float) $rows->whereNotNull('course_id')->sum('amount'),
                    'books' => (float) $rows->whereNotNull('book_id')->sum('amount'),
                ];
            })->reverse()->values();

            // Annually (últimos 6 meses)
            $annuallyChart = collect(range(0, 5))->map(function ($i) use ($nowPe, $monthlyAggregated, $esShort) {
                $monthKey = $nowPe->copy()->subMonths($i)->format('Y-m');
                $rows = $monthlyAggregated->get($monthKey) ?? collect();

                return [
                    'label' => $esShort[$nowPe->copy()->subMonths($i)->month - 1].' '.$nowPe->copy()->subMonths($i)->format('y'),
                    'total' => (float) $rows->sum('amount'),
                    'subs' => (float) $rows->filter(fn ($r) => $r->course_id === null && $r->book_id === null)->sum('amount'),
                    'courses' => (float) $rows->whereNotNull('course_id')->sum('amount'),
                    'books' => (float) $rows->whereNotNull('book_id')->sum('amount'),
                ];
            })->reverse()->values();

            return [
                'stats' => [
                    'totalIncome' => $totalIncome,
                    'subIncome' => $subIncome,
                    'courseIncome' => $courseIncome,
                    'bookIncome' => $bookIncome,
                    'bookSalesCount' => $bookSalesCount,
                    'totalUsers' => $totalUsers,
                    'activeUsers' => $activeUsers,
                    'inactiveUsers' => $inactiveUsers,
                    'premiumUsers' => $premiumUsers,
                    'newUsersThisMonth' => $newUsersThisMonth,
                    'totalCourses' => $totalCourses,
                    'publishedCourses' => $publishedCourses,
                    'draftCourses' => $draftCourses,
                    'recordedCourses' => $recordedCourses,
                    'liveCourses' => $liveCourses,
                    'totalBooks' => $totalBooks,
                    'availableBooks' => $availableBooks,
                    'totalBookDownloads' => $totalBookDownloads,
                    'bookDownloadsThisMonth' => $bookDownloadsThisMonth,
                    'bookWhatsappLeads' => $bookWhatsappLeads,
                    'totalEnrollments' => $totalEnrollments,
                    'completedCourses' => $completedCourses,
                    'approvalRate' => round($approvalRate, 2),
                    'activeSubs' => $activeSubs,
                    'expiredSubs' => $expiredSubs,
                    'pendingPayments' => $pendingPayments,
                    'approvedPayments' => $approvedPayments,
                    'certificatesIssued' => $certificatesIssued,
                    'whatsappLeads' => $whatsappLeads,
                    'whatsappLeadsMonth' => $whatsappLeadsMonth,
                ],
                'charts' => [
                    'weekly' => $weeklyChart,
                    'monthly' => $monthlyChart,
                    'quarterly' => $quarterlyChart,
                    'annually' => $annuallyChart,
                ],
                'courseSales' => $courseSales,
                'topBooks' => $topBooks,
            ];
        });
    }
}
