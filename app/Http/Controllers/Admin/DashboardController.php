<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'activo')->count();
        $totalCourses = Course::count();
        $publishedCourses = Course::where('status', 'PUBLICADO')->count();
        $totalIncome = Payment::where('status', 'aprobado')->sum('amount');

        $courseSales = Course::whereHas('payments', fn($q) => $q->where('status', 'aprobado'))
            ->withCount(['payments as approved_payments_count' => fn($q) => $q->where('status', 'aprobado')])
            ->orderByDesc('approved_payments_count')
            ->limit(5)
            ->get();

        $totalEnrollments = Enrollment::count();
        $completedEnrollments = Enrollment::whereNotNull('completed_at')->count();
        $passedEnrollments = Enrollment::where('passed', true)->count();

        $completionRate = $totalEnrollments ? (float) ($completedEnrollments / $totalEnrollments) * 100 : 0;
        $approvalRate = $totalEnrollments ? (float) ($passedEnrollments / $totalEnrollments) * 100 : 0;
        $totalIncome = (float) $totalIncome;

        return Inertia::render('admin/Dashboard', compact(
            'totalUsers',
            'activeUsers',
            'totalCourses',
            'publishedCourses',
            'totalIncome',
            'courseSales',
            'completionRate',
            'approvalRate'
        ));
    }
}
