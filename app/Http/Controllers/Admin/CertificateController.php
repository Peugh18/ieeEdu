<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Certificate::class);

        $perPage = (int) $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50]) ? $perPage : 20;

        $query = Certificate::query()->with(['user:id,name,email', 'course:id,title']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('course', fn ($c) => $c->where('title', 'like', "%{$search}%"))
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $certificates = $query->orderBy('issue_date', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $thisMonth = now()->startOfMonth();

        return Inertia::render('admin/Certificates', [
            'certificates' => $certificates,
            'filters' => $request->only('search', 'per_page'),
            'stats' => [
                'total' => Certificate::count(),
                'this_month' => Certificate::where('issue_date', '>=', $thisMonth)->count(),
                'with_file' => Certificate::whereNotNull('file_path')->count(),
                'unique_students' => Certificate::distinct('user_id')->count('user_id'),
            ],
        ]);
    }

    public function download(Request $request, Certificate $certificate, CertificateService $service)
    {
        $this->authorize('download', $certificate);

        $action = $request->query('action', 'stream');

        return $service->downloadPdf($certificate, $action);
    }
}
