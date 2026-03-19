<?php

namespace App\Services\Permissions;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleService
{
    public function all(): LengthAwarePaginator
    {
        return Role::with('permissions')->paginate(15);
    }

    public function find(int $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function create(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->withProperties(['permissions' => $data['permissions'] ?? []])
            ->log('Rol creado: ' . $role->name);

        return $role;
    }

    public function update(Role $role, array $data): Role
    {
        $role->update(['name' => $data['name']]);

        if (array_key_exists('permissions', $data)) {
            $role->syncPermissions($data['permissions'] ?? []);
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->withProperties(['permissions' => $data['permissions'] ?? []])
            ->log('Rol actualizado: ' . $role->name);

        return $role;
    }

    public function delete(Role $role): void
    {
        // Consulta directa a la tabla pivot para evitar cross-DB join
        // (usuarios vive en conexión timeit, roles en mysql).
        $userCount = \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->where('model_type', \App\Models\User::class)
            ->count();

        if ($userCount > 0) {
            throw new \RuntimeException(
                "No se puede eliminar el rol \"{$role->name}\" porque tiene {$userCount} usuario(s) asignado(s)."
            );
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->log('Rol eliminado: ' . $role->name);

        $role->delete();
    }
}
