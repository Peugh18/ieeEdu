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
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
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

        // Weekly (últimos 7 días) — etiquetas en español
        $weeklyChart = collect(range(0, 6))
            ->map(fn($i) => $nowPe->copy()->subDays($i)->format('Y-m-d'))
            ->reverse()
            ->map(fn($date) => [
                'label'   => $esDays[Carbon::parse($date)->dayOfWeek] . ' ' . Carbon::parse($date)->format('d'),
                'total'   => (float) Payment::where('status', 'aprobado')->whereDate('created_at', $date)->sum('amount'),
                'subs'    => (float) Payment::where('status', 'aprobado')->whereNull('course_id')->whereDate('created_at', $date)->sum('amount'),
                'courses' => (float) Payment::where('status', 'aprobado')->whereNotNull('course_id')->whereDate('created_at', $date)->sum('amount'),
            ])->values();

        // Monthly (últimas 4 semanas)
        $monthlyChart = collect(range(0, 3))->map(fn($i) => [
            'label'   => 'Semana ' . (4 - $i),
            'total'   => (float) Payment::where('status', 'aprobado')
                            ->whereBetween('created_at', [$nowPe->copy()->subWeeks($i + 1), $nowPe->copy()->subWeeks($i)])->sum('amount'),
            'subs'    => (float) Payment::where('status', 'aprobado')->whereNull('course_id')
                            ->whereBetween('created_at', [$nowPe->copy()->subWeeks($i + 1), $nowPe->copy()->subWeeks($i)])->sum('amount'),
            'courses' => (float) Payment::where('status', 'aprobado')->whereNotNull('course_id')
                            ->whereBetween('created_at', [$nowPe->copy()->subWeeks($i + 1), $nowPe->copy()->subWeeks($i)])->sum('amount'),
        ])->reverse()->values();

        // Quarterly (últimos 3 meses) — nombres de mes en español
        $quarterlyChart = collect(range(0, 2))->map(fn($i) => [
            'label'   => $esShort[$nowPe->copy()->subMonths($i)->month - 1],
            'total'   => (float) Payment::where('status', 'aprobado')
                            ->whereMonth('created_at', $nowPe->copy()->subMonths($i)->month)
                            ->whereYear('created_at', $nowPe->copy()->subMonths($i)->year)->sum('amount'),
            'subs'    => (float) Payment::where('status', 'aprobado')->whereNull('course_id')
                            ->whereMonth('created_at', $nowPe->copy()->subMonths($i)->month)
                            ->whereYear('created_at', $nowPe->copy()->subMonths($i)->year)->sum('amount'),
            'courses' => (float) Payment::where('status', 'aprobado')->whereNotNull('course_id')
                            ->whereMonth('created_at', $nowPe->copy()->subMonths($i)->month)
                            ->whereYear('created_at', $nowPe->copy()->subMonths($i)->year)->sum('amount'),
        ])->reverse()->values();

        // Annually (últimos 6 meses) — corto en español
        $annuallyChart = collect(range(0, 5))->map(fn($i) => [
            'label'   => $esShort[$nowPe->copy()->subMonths($i)->month - 1] . ' ' . $nowPe->copy()->subMonths($i)->format('y'),
            'total'   => (float) Payment::where('status', 'aprobado')
                            ->whereMonth('created_at', $nowPe->copy()->subMonths($i)->month)
                            ->whereYear('created_at', $nowPe->copy()->subMonths($i)->year)->sum('amount'),
            'subs'    => (float) Payment::where('status', 'aprobado')->whereNull('course_id')
                            ->whereMonth('created_at', $nowPe->copy()->subMonths($i)->month)
                            ->whereYear('created_at', $nowPe->copy()->subMonths($i)->year)->sum('amount'),
            'courses' => (float) Payment::where('status', 'aprobado')->whereNotNull('course_id')
                            ->whereMonth('created_at', $nowPe->copy()->subMonths($i)->month)
                            ->whereYear('created_at', $nowPe->copy()->subMonths($i)->year)->sum('amount'),
        ])->reverse()->values();

        return Inertia::render('admin/Dashboard', [
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
        ]);
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
