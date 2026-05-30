<?php

namespace App\Http\Controllers;

use App\Models\WhatsappLead;
use Illuminate\Http\Request;

class WhatsappLeadController extends Controller
{
    public function trackWhatsApp(Request $request)
    {
        $validated = $request->validate([
            'courses' => 'required|array',
            'courses.*' => 'integer|exists:courses,id',
            'total' => 'required|numeric|min:0',
        ]);

        WhatsappLead::create([
            'user_id' => $request->user()?->id,
            'courses' => $validated['courses'],
            'total' => $validated['total'],
        ]);

        return response()->json(['success' => true]);
    }
}
