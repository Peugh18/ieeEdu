<?php

namespace App\Http\Controllers;

use App\Models\ConsultancyRequest;
use Illuminate\Http\Request;

class ConsultancyController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:150',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'company' => 'nullable|string|max:150',
            'area'    => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ], [
            'name.required'    => 'Por favor ingresa tu nombre completo.',
            'email.required'   => 'El correo electrónico es requerido.',
            'email.email'      => 'Ingresa un correo válido.',
            'area.required'    => 'Selecciona el área de consultoría de tu interés.',
            'message.required' => 'Por favor describe brevemente tu necesidad.',
        ]);

        ConsultancyRequest::create($validated);

        return back()->with('success', '¡Tu solicitud fue enviada con éxito! Nos pondremos en contacto contigo en las próximas 24 horas.');
    }
}
