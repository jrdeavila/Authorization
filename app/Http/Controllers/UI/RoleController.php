<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:roles-read')->only('index');
        $this->middleware('can:roles-create')->only('create', 'store');
        $this->middleware('can:roles-update')->only('update');
        $this->middleware('can:roles-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $roleId = $request->get('role_id');
        $currentRole = Role::find($roleId);
        $roles = Role::all();
        $permissions = Permission::all();
        return view('pages.roles.index', compact('roles', 'permissions', 'currentRole'));
    }

    public function store(Request $request)
    {
        try {

            DB::beginTransaction();
            $request->validate([
                'name' => 'required|string',
                'guard_name' => 'required|string',
                'permissions' => 'required|array',
                'permissions.*' => 'required|exists:permissions,id',
            ]);
            $role = Role::create($request->all());
            $role->permissions()->sync($request->permissions);
            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Rol creado correctamente');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('roles.index')->with('error', 'Error al crear el rol');
        }
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string',
            'guard_name' => 'required|string',
            'permissions' => 'required|array',
            'permissions.*' => 'required|exists:permissions,id',
        ]);
        $role->update($request->all());
        $role->permissions()->sync($request->permissions);
        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente');
    }
}
