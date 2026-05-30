<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\GenerateExecutiveReportAction;
use App\Http\Controllers\Controller;
use App\Services\DashboardStatsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardStatsService $statsService
    ) {}

    public function index(Request $request)
    {
        return Inertia::render('admin/Dashboard', $this->statsService->getStats());
    }

    public function downloadReport(GenerateExecutiveReportAction $action)
    {
        $now = now();
        $pdf = $action->execute();

        return $pdf->download('Reporte_Mensual_iieEdu_'.$now->format('m_Y').'.pdf');
    }
}
