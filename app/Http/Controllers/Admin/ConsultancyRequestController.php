<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultancyRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConsultancyRequestController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50]) ? $perPage : 20;

        $query = ConsultancyRequest::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $status = $request->input('status');
        if ($status === 'all') {
            // Mostrar todos, no aplicar filtro de status
        } elseif ($status) {
            $query->where('status', $status);
        } else {
            // Por defecto, ocultar los resueltos
            $query->where('status', '!=', 'cerrado');
        }

        $requests = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('admin/ConsultancyRequests', [
            'requests' => $requests,
            'filters'  => $request->only('search', 'status', 'per_page'),
            'stats'    => [
                'total'      => ConsultancyRequest::count(),
                'pending'    => ConsultancyRequest::where('status', 'pendiente')->count(),
                'contacted'  => ConsultancyRequest::where('status', 'en_contacto')->count(),
                'resolved'   => ConsultancyRequest::where('status', 'cerrado')->count(),
            ],
        ]);
    }

    public function updateStatus(Request $request, ConsultancyRequest $consultancyRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pendiente,en_contacto,cerrado',
        ]);

        $consultancyRequest->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Estado de la solicitud actualizado.');
    }
}
