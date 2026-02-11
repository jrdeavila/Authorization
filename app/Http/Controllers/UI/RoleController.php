<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Permission; // Tu modelo
use App\Models\Role;       // Tu modelo
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roleId = $request->get('role_id');
        $currentRole = $roleId ? Role::with('permissions')->find($roleId) : null;

        // Cambia all() por paginate() para que la vista reciba los datos correctos
        $roles = Role::paginate(7);

        $permissions = Permission::all();

        return view('pages.roles.index', compact('roles', 'permissions', 'currentRole'));
    }

    public function store(Request $request)
    {
        // 1. Validación (quitamos el 'required' de permissions por si quieres crear uno vacío)
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'guard_name' => 'required|string',
            'permissions' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // 2. Crear el rol usando tu modelo
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name
            ]);

            // 3. Sincronizar permisos
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Rol creado correctamente');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('roles.index')->with('error', 'Error al crear: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Role $role)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => 'required|string|unique:roles,name,' . $role->id,
                'guard_name' => 'required|string',
                'permissions' => 'nullable|array',
            ]);

            $role->update($request->only('name', 'guard_name'));
            $role->permissions()->sync($request->permissions ?? []);

            DB::commit();
            return redirect()->route('roles.index', ['role_id' => $role->id])->with('success', 'Rol actualizado');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('roles.index', ['role_id' => $role->id])->with('error', 'Error al actualizar');
        }
    }

    public function destroy($id)
    {
        try {
            // USAMOS DB DIRECTO PARA EVITAR EL ERROR "Class name must be a valid object"
            // Borramos primero las relaciones para que no salte error de integridad
            DB::table('role_has_permissions')->where('role_id', $id)->delete();
            DB::table('model_has_roles')->where('role_id', $id)->delete();

            // Borramos el rol
            DB::table('roles')->where('id', $id)->delete();

            // Limpiamos caché de Spatie de forma manual
            if (app()->bound(\Spatie\Permission\PermissionRegistrar::class)) {
                app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            }

            return redirect()->route('roles.index')->with('deleted', 'Rol eliminado correctamente');
        } catch (Exception $e) {
            return redirect()->route('roles.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
