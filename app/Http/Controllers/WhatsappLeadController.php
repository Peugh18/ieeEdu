<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappLead;

class WhatsappLeadController extends Controller
{
    public function trackWhatsApp(Request $request)
    {
        $validated = $request->validate([
            'courses' => 'required|array',
            'total' => 'required|numeric',
        ]);

        WhatsappLead::create([
            'user_id' => $request->user()->id,
            'courses' => $validated['courses'],
            'total' => $validated['total'],
        ]);

        return response()->json(['success' => true]);
    }
}
