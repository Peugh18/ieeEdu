<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $query = Subscription::query()->with('user:id,name,email');

        if ($search = $request->input('search')) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('admin/Subscriptions', [
            'subscriptions' => $subscriptions,
            'filters' => $request->only('search', 'per_page')
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'months' => 'required|integer|min:1',
        ]);

        // Desactivar activas previas
        Subscription::where('user_id', $data['user_id'])->where('status', 'activa')->update(['status' => 'expirada']);

        $startDate = now();
        $endDate = now()->addMonths($data['months']);

        Subscription::create([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'activa',
        ]);

        // 🚀 AUTO-ENROLL: Vincular todos los cursos publicados (incluye Masterclasses/Eventos)
        $courses = Course::where('status', 'PUBLICADO')->get();
        foreach ($courses as $course) {
            Enrollment::firstOrCreate([
                'user_id' => $data['user_id'],
                'course_id' => $course->id
            ], [
                'enrolled_at' => now()
            ]);
        }

        return back()->with('success', 'Membresía ' . $data['type'] . ' activada. Se han habilitado todos los cursos para el alumno.');
    }

    public function toggleStatus(Subscription $subscription)
    {
        $subscription->status = $subscription->status === 'activa' ? 'cancelada' : 'activa';
        $subscription->save();

        return back()->with('success', 'Estado de suscripción actualizado.');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return back()->with('success', 'Suscripción eliminada del historial.');
    }
}
