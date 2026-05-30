<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdateProfileRequest;
use App\Models\Enrollment;
use App\Models\Subscription;
use App\Services\ProgressService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function __construct(
        protected ProgressService $progressService
    ) {}

    /**
     * Perfil del Usuario y sus Cursos (Optimizado para evitar N+1)
     */
    public function profile()
    {
        $user = Auth::user();

        $enrollments = Enrollment::where('user_id', $user->id)
            ->visible()
            ->with(['course.category', 'lastLesson'])
            ->get();

        $courseIds = $enrollments->pluck('course_id')->filter()->unique()->toArray();

        // Cálculo de progreso en lote para evitar consultas N+1 en bucles
        $progressMap = $this->progressService->getBatchProgress($user, $courseIds);

        // Sincronizar solo si hay cambios
        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            if (! $course) {
                continue;
            }
            $calculated = $progressMap[$course->id] ?? 0;
            if ($enrollment->progress !== $calculated) {
                $data = ['progress' => $calculated];
                if ($calculated >= 100 && ! $enrollment->completed_at) {
                    if (! $course->hasFinalQuiz()) {
                        $data['completed_at'] = now();
                    }
                }
                $enrollment->update($data);
                $enrollment->progress = $calculated;
            }
        }

        $enrolledCourses = $enrollments->map(function (Enrollment $enrollment) {
            $course = $enrollment->course;

            return [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'image' => $course->image,
                'progress' => $enrollment->progress,
                'last_lesson' => $enrollment->lastLesson ? $enrollment->lastLesson->title : 'Sin empezar',
            ];
        });

        return Inertia::render('student/Profile', [
            'enrolledCourses' => $enrolledCourses,
            'activeSubscription' => $user->subscriptions()->where('status', Subscription::STATUS_ACTIVE)->where('end_date', '>=', now())->first(),
        ]);
    }

    /**
     * Actualizar perfil
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Si viene contraseña, validar y actualizar solo eso
        if ($request->has('password') && $request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return back()->with('success', 'Contraseña actualizada correctamente.');
        }

        // Procesar imagen si se envió
        if ($request->hasFile('avatar')) {
            // Borrar avatar anterior si existe
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Guardar nueva imagen en public/storage/avatars/
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
