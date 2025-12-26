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
    public function __construct()
    {
        $this->middleware('can:users-read')->only('index');
        $this->middleware('can:users-create')->only('create', 'store');
        $this->middleware('can:users-update')->only('update');
        $this->middleware('can:show-activity-owner')->only('show');
        $this->middleware('can:show-user-curriculum')->only('resume');
    }
    public function index(Request $request)
    {
        $userId = $request->get('user_id');
        $search = $request->get('search');
        $currentUser = User::find($userId);
        $users = User::query()
            ->whereHas('employee', function ($query) {
                $query->where('estado', 'Activo');
            })
            ->when($search, function ($query, $search) {
                return $query->whereHas('employee', function ($query) use ($search) {
                    $query->where('noDocumento', 'like', '%' . $search . '%')
                        // Str lower case
                        ->orWhereRaw("LOWER(noDocumento) like '%{$search}%'")
                        ->orWhereRaw("LOWER(nombres) like '%{$search}%'")
                        ->orWhereRaw("LOWER(apellidos) like '%{$search}%'");
                });
            })->paginate(5);
        $roles = Role::all();
        $permissions = Permission::all();
        return view('pages.users.index', compact('users', 'currentUser', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'sometimes|array',
            'roles.*' => 'required|exists:roles,id',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'required|exists:permissions,id',
        ]);
        try {
            DB::beginTransaction();
            $user->roles()->sync($request->roles);
            $user->permissions()->sync($request->permissions);
            DB::commit();
            return redirect()->route('users.index', ['user_id' => $user->id])->with('success', 'Usuario actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('users.index', ['user_id' => $user->id])->with('error', 'Error al actualizar el usuario');
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
        if (! $user->employee) {
            return redirect()->route('users.index', request()->all())->with('error', 'El usuario no tiene un empleado asociado');
        }
        return redirect(env('TIMEIT_CURRICULUM_URL') . $user->employee->id);
    }
}
