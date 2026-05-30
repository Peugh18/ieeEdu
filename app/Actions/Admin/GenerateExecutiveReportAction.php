<?php

namespace App\Actions\Admin;

use App\Models\Book;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WhatsappLead;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateExecutiveReportAction
{
    public function execute(): \Barryvdh\DomPDF\PDF
    {
        $now = now();
        $startOfCurrentMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $currentIncome = (float) Payment::where('status', 'aprobado')->where('created_at', '>=', $startOfCurrentMonth)->sum('amount');
        $lastMonthIncome = (float) Payment::where('status', 'aprobado')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');
        $currentUsers = User::where('created_at', '>=', $startOfCurrentMonth)->count();
        $lastMonthUsers = User::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $stats = [
            'totalIncome' => (float) Payment::where('status', 'aprobado')->sum('amount'),
            'subIncome' => (float) Payment::where('status', 'aprobado')->whereNull('course_id')->sum('amount'),
            'courseIncome' => (float) Payment::where('status', 'aprobado')->whereNotNull('course_id')->sum('amount'),
            'currentMonthIncome' => $currentIncome,
            'incomeGrowth' => $lastMonthIncome > 0 ? (($currentIncome - $lastMonthIncome) / $lastMonthIncome) * 100 : 100,
            'activeUsers' => User::where('status', 'activo')->count(),
            'inactiveUsers' => User::where('status', 'inactivo')->count(),
            'premiumUsers' => Subscription::where('status', Subscription::STATUS_ACTIVE)->where('end_date', '>=', now())->distinct('user_id')->count('user_id'),
            'newUsersMonth' => $currentUsers,
            'userGrowth' => $lastMonthUsers > 0 ? (($currentUsers - $lastMonthUsers) / $lastMonthUsers) * 100 : 0,
            'publishedCourses' => Course::where('status', 'PUBLICADO')->count(),
            'totalUsers' => User::count(),
            'totalBooks' => Book::count(),
            'availableBooks' => Book::where('is_available', true)->count(),
            'certificatesIssued' => Certificate::count(),
            'activeSubs' => Subscription::where('status', Subscription::STATUS_ACTIVE)->where('end_date', '>=', now())->count(),
            'expiredSubs' => Subscription::where('status', Subscription::STATUS_EXPIRED)->count(),
            'pendingPayments' => Payment::where('status', 'pendiente')->count(),
            'approvedPayments' => Payment::where('status', 'aprobado')->count(),
            'approvalRate' => ($e = Enrollment::count()) ? round(Enrollment::where('passed', true)->count() / $e * 100, 2) : 0,
            'whatsappLeadsMonth' => WhatsappLead::whereMonth('created_at', $now->copy()->setTimezone('America/Lima')->month)->whereYear('created_at', $now->copy()->setTimezone('America/Lima')->year)->count(),
        ];

        $courseSales = Course::whereHas('payments', fn ($q) => $q->where('status', 'aprobado'))
            ->withCount(['payments as approved_payments_count' => fn ($q) => $q->where('status', 'aprobado')])
            ->withSum(['payments as total_earned' => fn ($q) => $q->where('status', 'aprobado')], 'amount')
            ->orderByDesc('approved_payments_count')
            ->limit(5)
            ->get(['id', 'title', 'type', 'price']);

        return Pdf::loadView('reports.executive', [
            'stats' => $stats,
            'courseSales' => $courseSales,
            'monthName' => $now->translatedFormat('F Y'),
        ]);
    }
}
