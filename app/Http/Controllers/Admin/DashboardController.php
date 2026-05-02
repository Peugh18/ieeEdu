<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WhatsappLead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey     = 'admin_dashboard_stats';
        $cacheMinutes = 5;

        $stats = Cache::remember($cacheKey, $cacheMinutes * 60, function () {
            // ─── USUARIOS ────────────────────────────────────────────────
            $totalUsers         = User::count();
            $activeUsers        = User::where('status', 'activo')->count();
            $inactiveUsers      = User::where('status', 'inactivo')->count();
            $premiumUsers       = Subscription::where('status', 'activa')
                                    ->where('end_date', '>=', now())
                                    ->distinct('user_id')->count('user_id');
            $newUsersThisMonth  = User::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)->count();

            // ─── INGRESOS ─────────────────────────────────────────────────
            $totalIncome    = (float) Payment::where('status', 'aprobado')->sum('amount');
            $subIncome      = (float) Payment::where('status', 'aprobado')->whereNull('course_id')->sum('amount');
            $courseIncome   = (float) Payment::where('status', 'aprobado')->whereNotNull('course_id')->sum('amount');

            $pendingPayments = Payment::where('status', 'pendiente')->count();
            $approvedPayments = Payment::where('status', 'aprobado')->count();

            // ─── CURSOS ───────────────────────────────────────────────────
            $totalCourses     = Course::count();
            $publishedCourses = Course::where('status', 'PUBLICADO')->count();
            $draftCourses     = Course::where('status', 'BORRADOR')->count();
            $recordedCourses  = Course::where('type', 'grabado')->count();
            $liveCourses      = Course::where('type', 'en_vivo')->count();

            // Top 5 courses por ventas
            $courseSales = Course::whereHas('payments', fn($q) => $q->where('status', 'aprobado'))
                ->withCount(['payments as approved_payments_count' => fn($q) => $q->where('status', 'aprobado')])
                ->withSum(['payments as total_earned' => fn($q) => $q->where('status', 'aprobado')], 'amount')
                ->with('category:id,name')
                ->orderByDesc('approved_payments_count')
                ->limit(5)
                ->get(['id', 'title', 'type', 'price', 'category_id', 'image']);

            // ─── LIBROS ───────────────────────────────────────────────────
            $totalBooks     = \App\Models\Book::count();
            $availableBooks = \App\Models\Book::where('is_available', true)->count();

            // ─── LEAR ENGAGEMENT ──────────────────────────────────────────
            $totalEnrollments   = Enrollment::count();
            $completedCourses   = Enrollment::whereNotNull('completed_at')->count();
            $passedEnrollments  = Enrollment::where('passed', true)->count();
            $approvalRate       = $totalEnrollments ? ($passedEnrollments / $totalEnrollments) * 100 : 0;

            // ─── CERTIFICADOS & LEADS ─────────────────────────────────────
            $certificatesIssued = Certificate::count();
            $whatsappLeads      = WhatsappLead::count();
            $whatsappLeadsMonth = WhatsappLead::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)->count();

            // ─── SUSCRIPCIONES ACTIVAS ────────────────────────────────────
            $activeSubs     = Subscription::where('status', 'activa')
                                ->where('end_date', '>=', now())->count();
            $expiredSubs    = Subscription::where('status', 'vencida')->count();

            // ─── CHARTS: Ingresos por periodo ─────────────────────────────
            $esDays   = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
            $esMonths = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
            $esShort  = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
            $nowPe    = now()->setTimezone('America/Lima');

            // Pre-cargar datos agrupados por día para reducir queries N+1
            $startDate = $nowPe->copy()->subDays(6)->startOfDay();
            $endDate   = $nowPe->copy()->endOfDay();

            $dailyPayments = Payment::where('status', 'aprobado')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date,
                    SUM(amount) as total,
                    SUM(CASE WHEN course_id IS NULL THEN amount ELSE 0 END) as subs,
                    SUM(CASE WHEN course_id IS NOT NULL THEN amount ELSE 0 END) as courses')
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            // Weekly (últimos 7 días)
            $weeklyChart = collect(range(0, 6))
                ->map(fn($i) => $nowPe->copy()->subDays($i)->format('Y-m-d'))
                ->reverse()
                ->map(fn($date) => [
                    'label'   => $esDays[Carbon::parse($date)->dayOfWeek] . ' ' . Carbon::parse($date)->format('d'),
                    'total'   => (float) ($dailyPayments[$date]->total ?? 0),
                    'subs'    => (float) ($dailyPayments[$date]->subs ?? 0),
                    'courses' => (float) ($dailyPayments[$date]->courses ?? 0),
                ])->values();

            // Monthly (últimas 4 semanas) — precargar rango completo
            $monthStart = $nowPe->copy()->subWeeks(4)->startOfDay();
            $monthEnd   = $nowPe->copy()->endOfDay();

            $monthPayments = Payment::where('status', 'aprobado')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->selectRaw('DATE(created_at) as date,
                    SUM(amount) as total,
                    SUM(CASE WHEN course_id IS NULL THEN amount ELSE 0 END) as subs,
                    SUM(CASE WHEN course_id IS NOT NULL THEN amount ELSE 0 END) as courses')
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            $monthlyChart = collect(range(0, 3))->map(function ($i) use ($nowPe, $monthPayments) {
                $weekStart = $nowPe->copy()->subWeeks($i + 1)->startOfDay();
                $weekEnd   = $nowPe->copy()->subWeeks($i)->endOfDay();
                $days = collect(range(0, 6))->map(fn($d) => $weekStart->copy()->addDays($d)->format('Y-m-d'));
                return [
                    'label'   => 'Semana ' . (4 - $i),
                    'total'   => (float) $days->sum(fn($d) => (float) ($monthPayments[$d]->total ?? 0)),
                    'subs'    => (float) $days->sum(fn($d) => (float) ($monthPayments[$d]->subs ?? 0)),
                    'courses' => (float) $days->sum(fn($d) => (float) ($monthPayments[$d]->courses ?? 0)),
                ];
            })->reverse()->values();

            // Quarterly & Annually — agrupado por mes
            $quarterStart = $nowPe->copy()->subMonths(6)->startOfMonth();
            $quarterEnd   = $nowPe->copy()->endOfMonth();

            $monthlyAggregated = Payment::where('status', 'aprobado')
                ->whereBetween('created_at', [$quarterStart, $quarterEnd])
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month,
                    SUM(amount) as total,
                    SUM(CASE WHEN course_id IS NULL THEN amount ELSE 0 END) as subs,
                    SUM(CASE WHEN course_id IS NOT NULL THEN amount ELSE 0 END) as courses')
                ->groupBy('year', 'month')
                ->get()
                ->keyBy(fn($r) => $r->year . '-' . str_pad($r->month, 2, '0', STR_PAD_LEFT));

            // Quarterly (últimos 3 meses)
            $quarterlyChart = collect(range(0, 2))->map(function ($i) use ($nowPe, $monthlyAggregated, $esShort) {
                $monthKey = $nowPe->copy()->subMonths($i)->format('Y-m');
                $row = $monthlyAggregated[$monthKey] ?? null;
                return [
                    'label'   => $esShort[$nowPe->copy()->subMonths($i)->month - 1],
                    'total'   => (float) ($row->total ?? 0),
                    'subs'    => (float) ($row->subs ?? 0),
                    'courses' => (float) ($row->courses ?? 0),
                ];
            })->reverse()->values();

            // Annually (últimos 6 meses)
            $annuallyChart = collect(range(0, 5))->map(function ($i) use ($nowPe, $monthlyAggregated, $esShort) {
                $monthKey = $nowPe->copy()->subMonths($i)->format('Y-m');
                $row = $monthlyAggregated[$monthKey] ?? null;
                return [
                    'label'   => $esShort[$nowPe->copy()->subMonths($i)->month - 1] . ' ' . $nowPe->copy()->subMonths($i)->format('y'),
                    'total'   => (float) ($row->total ?? 0),
                    'subs'    => (float) ($row->subs ?? 0),
                    'courses' => (float) ($row->courses ?? 0),
                ];
            })->reverse()->values();

            return [
                'stats' => [
                    // Ingresos
                    'totalIncome'       => $totalIncome,
                    'subIncome'         => $subIncome,
                    'courseIncome'      => $courseIncome,
                    // Usuarios
                    'totalUsers'        => $totalUsers,
                    'activeUsers'       => $activeUsers,
                    'inactiveUsers'     => $inactiveUsers,
                    'premiumUsers'      => $premiumUsers,
                    'newUsersThisMonth' => $newUsersThisMonth,
                    // Cursos
                    'totalCourses'      => $totalCourses,
                    'publishedCourses'  => $publishedCourses,
                    'draftCourses'      => $draftCourses,
                    'recordedCourses'   => $recordedCourses,
                    'liveCourses'       => $liveCourses,
                    // Libros
                    'totalBooks'        => $totalBooks,
                    'availableBooks'    => $availableBooks,
                    // Engagement
                    'totalEnrollments'  => $totalEnrollments,
                    'completedCourses'  => $completedCourses,
                    'approvalRate'      => round($approvalRate, 2),
                    // Suscripciones
                    'activeSubs'        => $activeSubs,
                    'expiredSubs'       => $expiredSubs,
                    // Pagos
                    'pendingPayments'   => $pendingPayments,
                    'approvedPayments'  => $approvedPayments,
                    // Certificados & Leads
                    'certificatesIssued' => $certificatesIssued,
                    'whatsappLeads'      => $whatsappLeads,
                    'whatsappLeadsMonth' => $whatsappLeadsMonth,
                ],
                'charts' => [
                    'weekly'    => $weeklyChart,
                    'monthly'   => $monthlyChart,
                    'quarterly' => $quarterlyChart,
                    'annually'  => $annuallyChart,
                ],
                'courseSales' => $courseSales,
            ];
        });

        return Inertia::render('admin/Dashboard', $stats);
    }

    public function downloadReport(Request $request)
    {
        $now = now();
        $startOfCurrentMonth = $now->copy()->startOfMonth();
        $startOfLastMonth    = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth      = $now->copy()->subMonth()->endOfMonth();

        $currentIncome   = (float) Payment::where('status', 'aprobado')->where('created_at', '>=', $startOfCurrentMonth)->sum('amount');
        $lastMonthIncome = (float) Payment::where('status', 'aprobado')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');
        $currentUsers    = User::where('created_at', '>=', $startOfCurrentMonth)->count();
        $lastMonthUsers  = User::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $stats = [
            'totalIncome'         => (float) Payment::where('status', 'aprobado')->sum('amount'),
            'subIncome'           => (float) Payment::where('status', 'aprobado')->whereNull('course_id')->sum('amount'),
            'courseIncome'        => (float) Payment::where('status', 'aprobado')->whereNotNull('course_id')->sum('amount'),
            'currentMonthIncome'  => $currentIncome,
            'incomeGrowth'        => $lastMonthIncome > 0 ? (($currentIncome - $lastMonthIncome) / $lastMonthIncome) * 100 : 100,
            'activeUsers'         => User::where('status', 'activo')->count(),
            'inactiveUsers'       => User::where('status', 'inactivo')->count(),
            'premiumUsers'        => Subscription::where('status', 'activa')->where('end_date', '>=', now())->distinct('user_id')->count('user_id'),
            'newUsersMonth'       => $currentUsers,
            'userGrowth'          => $lastMonthUsers > 0 ? (($currentUsers - $lastMonthUsers) / $lastMonthUsers) * 100 : 0,
            'publishedCourses'    => Course::where('status', 'PUBLICADO')->count(),
            'totalUsers'          => User::count(),
            'totalBooks'          => \App\Models\Book::count(),
            'availableBooks'      => \App\Models\Book::where('is_available', true)->count(),
            'certificatesIssued'  => Certificate::count(),
            // Suscripciones
            'activeSubs'          => Subscription::where('status', 'activa')->where('end_date', '>=', now())->count(),
            'expiredSubs'         => Subscription::where('status', 'vencida')->count(),
            // Pagos
            'pendingPayments'     => Payment::where('status', 'pendiente')->count(),
            'approvedPayments'    => Payment::where('status', 'aprobado')->count(),
            // Engagement
            'approvalRate'        => ($e = \App\Models\Enrollment::count()) ? round(\App\Models\Enrollment::where('passed', true)->count() / $e * 100, 2) : 0,
            // Leads
            'whatsappLeadsMonth'  => \App\Models\WhatsappLead::whereMonth('created_at', $now->copy()->setTimezone('America/Lima')->month)->whereYear('created_at', $now->copy()->setTimezone('America/Lima')->year)->count(),
        ];

        $courseSales = Course::whereHas('payments', fn($q) => $q->where('status', 'aprobado'))
            ->withCount(['payments as approved_payments_count' => fn($q) => $q->where('status', 'aprobado')])
            ->withSum(['payments as total_earned' => fn($q) => $q->where('status', 'aprobado')], 'amount')
            ->orderByDesc('approved_payments_count')
            ->limit(5)
            ->get(['id', 'title', 'type', 'price']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.executive', [
            'stats'     => $stats,
            'courseSales' => $courseSales,
            'monthName' => $now->translatedFormat('F Y'),
        ]);

        return $pdf->download('Reporte_Mensual_iieEdu_' . $now->format('m_Y') . '.pdf');
    }
}
