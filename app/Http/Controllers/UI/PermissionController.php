<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Muestra la lista de permisos.
     */
    public function index()
    {
        $permissions = Permission::latest()->paginate(10);
        return view('pages.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('pages.permissions.create');
    }

    /**
     * Crea un permiso y limpia la caché de Spatie.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'guard_name' => 'required|string',
        ]);

        try {
            Permission::create($request->all());

            // Limpiar caché para que aparezca en la lista inmediatamente
            $this->clearPermissionCache();

            return redirect()->route('permissions.index')
                ->with('success', 'Permiso creado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('permissions.index')
                ->with('error', 'El permiso ya existe o ocurrió un error');
        }
    }

    public function edit(Permission $permission)
    {
        return view('pages.permissions.edit', compact($permission));
    }

    /**
     * Actualiza un permiso y limpia la caché.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string',
            'guard_name' => 'required|string',
        ]);

        try {
            $permission->update($request->all());
            $this->clearPermissionCache();

            return redirect()->route('permissions.index')
                ->with('success', 'Permiso actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('permissions.index')
                ->with('error', 'Error al actualizar el permiso');
        }
    }

    /**
     * Elimina el permiso, sus relaciones y envía señal 'deleted' para color rojo.
     */
    public function destroy($id)
    {
        try {
            // 1. Limpiamos relaciones en tablas intermedias usando DB
            DB::table('role_has_permissions')->where('permission_id', $id)->delete();
            DB::table('model_has_permissions')->where('permission_id', $id)->delete();

            // 2. Borramos el permiso
            $deleted = DB::table('permissions')->where('id', $id)->delete();

            if ($deleted) {
                // 3. Limpiar caché de Spatie
                $this->clearPermissionCache();

                // 4. Retornamos con 'deleted' para activar el SweetAlert ROJO
                return redirect()->route('permissions.index')
                    ->with('deleted', 'Permiso eliminado correctamente');
            }

            return redirect()->route('permissions.index')
                ->with('error', 'No se encontró el permiso para eliminar.');

        } catch (\Exception $e) {
            return redirect()->route('permissions.index')
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Función privada para resetear la caché de Spatie.
     */
    private function clearPermissionCache()
    {
        if (app()->bound(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
}
