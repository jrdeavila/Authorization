<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
   public function index(Request $request)
{
    $query = User::with([
        'employee.job',
        'employee.curriculum',
        'roles',
        'permissions'
    ]);

    if ($request->search) {
        $query->whereHas('employee', function ($q) use ($request) {
            $q->where('full_name', 'like', '%' . $request->search . '%');
        });
    }

    $users = $query->paginate(10);

    $currentUser = null;

    if ($request->user_id) {
        $currentUser = User::with([
            'employee.job',
            'employee.curriculum',
            'roles',
            'permissions'
        ])->find($request->user_id);
    }

    $roles = Role::all();
    $permissions = Permission::all();

    return view('users.index', compact(
        'users',
        'currentUser',
        'roles',
        'permissions'
    ));
}

    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();

            // 🔹 Sincroniza roles
            $user->syncRoles($request->roles ?? []);

            // 🔹 Sincroniza permisos directos
            $user->syncPermissions($request->permissions ?? []);

            DB::commit();

            return redirect()
                ->route('users.index', ['user_id' => $user->id])
                ->with('success', 'Usuario actualizado correctamente');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->route('users.index', ['user_id' => $user->id])
                ->with('error', 'Error al actualizar el usuario');
        }
    }

    public function show(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('pages.users.show', compact('user', 'roles', 'permissions'));
    }

    public function resume(User $user)
    {
        if (!$user->employee) {
            return redirect()
                ->route('users.index', request()->all())
                ->with('error', 'El usuario no tiene un empleado asociado');
        }

        return redirect(env('TIMEIT_CURRICULUM_URL') . $user->employee->id);
    }
}
