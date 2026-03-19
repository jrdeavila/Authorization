<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        // Conteo de empleados activos
        $activeEmployees = Employee::where('estado', 'Activo')->count();

        // Conteo de roles y permisos
        $totalRoles = Role::count();
        $totalPermissions = \App\Models\Permission::count();

        // Usuarios con roles asignados
        $usersWithRoles = DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->distinct('model_id')
            ->count('model_id');

        // Últimas 10 actividades
        $recentActivities = Activity::with('causer', 'subject')
            ->latest()
            ->limit(10)
            ->get();

        // Últimos 5 usuarios creados (por created_at del empleado)
        $newUsers = User::whereHas('employee', function ($q) {
                $q->where('estado', 'Activo');
            })
            ->with(['employee.job', 'employee.curriculum', 'roles'])
            ->orderByDesc(
                Employee::select('created_at')
                    ->whereColumn('empleados.id', 'usuarios.Empleados_id')
                    ->limit(1)
            )
            ->limit(5)
            ->get();

        return view('home', compact(
            'activeEmployees',
            'totalRoles',
            'totalPermissions',
            'usersWithRoles',
            'recentActivities',
            'newUsers',
        ));
    }
}
