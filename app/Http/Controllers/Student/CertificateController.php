<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CertificateController extends Controller
{
    public function __construct(
        protected CertificateService $certificateService
    ) {}

    /**
     * Gestión de Certificados
     */
    public function certificates()
    {
        $user = Auth::user();
        $certificates = Certificate::where('user_id', $user->id)
            ->whereHas('course', function ($q) {
                $q->where('certificate_enabled', true);
            })
            ->with(['course.category', 'course.certificateTemplate'])
            ->orderByDesc('issue_date')
            ->get()
            ->map(fn ($cert) => [
                'id' => $cert->id,
                'title' => 'Certificado de Aprobación',
                'course_title' => $cert->course->title ?? 'Curso',
                'issue_date' => $cert->issue_date->format('d M Y'),
                'image' => $cert->course->image ?? '/images/cert-placeholder.png',
                'code' => $cert->code ?? 'N/A',
                'is_available' => true,
                'download_url' => route('student.certificates.download', ['certificate' => $cert->id]),
            ]);

        return Inertia::render('student/Certificates', [
            'certificates' => $certificates,
        ]);
    }

    /**
     * Descargar certificado
     */
    public function downloadCertificate(Request $request, Certificate $certificate)
    {
        $this->authorize('download', $certificate);

        abort_unless(
            $this->certificateService->checkEligibility(Auth::user(), $certificate->course),
            403,
            'No has completado los requisitos del curso para descargar el certificado.'
        );

        $action = $request->query('action', 'download');

        return $this->certificateService->downloadPdf($certificate, $action);
    }
}
