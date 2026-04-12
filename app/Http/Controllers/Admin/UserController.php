<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        protected UserService $service,
        protected \App\Services\EnrollmentService $enrollmentService
    ) {
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50]) ? $perPage : 20;

        $query = User::withCount('enrollments');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('admin/Users', [
            'users'   => $users,
            'filters' => $request->only('role', 'status', 'search', 'per_page'),
            'stats'   => [
                'total'    => User::count(),
                'active'   => User::where('status', 'activo')->count(),
                'admins'   => User::where('role', 'admin')->count(),
                'students' => User::where('role', 'usuario')->count(),
            ],
        ]);
    }

    public function show(User $user)
    {
        $user->load([
            'enrollments' => fn ($q) => $q->with('course:id,title,image,type')->latest(),
            'payments'    => fn ($q) => $q->with('course:id,title')->latest(),
        ]);

        $availableCourses = Course::where('status', 'PUBLICADO')
            ->select('id', 'title', 'type', 'price', 'sale_price')
            ->orderBy('title')
            ->get();

        $enrolledIds = $user->enrollments->pluck('course_id')->toArray();

        return Inertia::render('admin/UserDetail', [
            'user'             => $user,
            'enrolledIds'      => $enrolledIds,
            'availableCourses' => $availableCourses,
        ]);
    }

    public function store(UserRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->back()->with('success', 'Usuario creado.');
    }

    public function update(UserRequest $request, User $user)
    {
        $this->service->update($user, $request->validated());
        return redirect()->back()->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $user)
    {
        // Soft delete: mark as inactivo instead of physically deleting
        $user->update(['status' => 'inactivo']);
        return redirect()->back()->with('success', 'Usuario desactivado.');
    }

    public function toggleStatus(User $user)
    {
        $newStatus = $user->status === 'activo' ? 'inactivo' : 'activo';
        $user->update(['status' => $newStatus]);
        return redirect()->back()->with('success', 'Estado actualizado.');
    }

    public function assignCourse(Request $request, User $user)
    {
        $request->validate(['course_id' => 'required|exists:courses,id']);
        
        $course = Course::findOrFail($request->course_id);
        $this->enrollmentService->enroll($user, $course);

        return redirect()->back()->with('success', 'Curso asignado correctamente.');
    }

    /** AJAX endpoint: returns up to 10 users matching the query.  Used by the payment modal. */
    public function search(Request $request)
    {
        $q = trim($request->input('q', ''));

        $users = User::select('id', 'name', 'email')
            ->where('status', 'activo')
            ->when(strlen($q) >= 2, function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    public function export(Request $request)
    {
        $users = User::withCount('enrollments')
            ->when($request->search, function ($q, $s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            })
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderBy('created_at', 'desc')
            ->get();

        $csv = "Nombre,Email,Teléfono,Rol,Estado,Cursos Inscritos,Fecha Registro\n";
        foreach ($users as $u) {
            $csv .= implode(',', [
                '"' . str_replace('"', '""', $u->name) . '"',
                '"' . $u->email . '"',
                '"' . ($u->telefono ?? '') . '"',
                $u->role === 'admin' ? 'Admin' : 'Estudiante',
                ucfirst($u->status),
                $u->enrollments_count,
                '"' . $u->created_at->format('d/m/Y') . '"',
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="usuarios-iee-' . now()->format('Ymd') . '.csv"',
        ]);
    }
}
