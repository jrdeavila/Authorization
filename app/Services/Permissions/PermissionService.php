<?php

namespace App\Services\Permissions;

use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PermissionService
{
    public function all(): LengthAwarePaginator
    {
        return Permission::withCount('roles')->orderBy('name')->paginate(15);
    }

    public function allGrouped(): array
    {
        $permissions = Permission::orderBy('name')->get();

        $grouped = [];
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name, 2);
            $group = count($parts) > 1 ? $parts[0] : 'general';
            $grouped[$group][] = $permission;
        }

        ksort($grouped);

        return $grouped;
    }

    public function find(int $id): Permission
    {
        return Permission::findOrFail($id);
    }

    public function create(array $data): Permission
    {
        $permission = Permission::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($permission)
            ->log('Permiso creado: ' . $permission->name);

        return $permission;
    }

    public function update(Permission $permission, array $data): Permission
    {
        $permission->update(['name' => $data['name']]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($permission)
            ->log('Permiso actualizado: ' . $permission->name);

        return $permission;
    }

    public function delete(Permission $permission): void
    {
        $roleCount = $permission->roles()->count();
        if ($roleCount > 0) {
            throw new \RuntimeException(
                "No se puede eliminar el permiso \"{$permission->name}\" porque está asignado a {$roleCount} rol(es)."
            );
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($permission)
            ->log('Permiso eliminado: ' . $permission->name);

        $permission->delete();
    }
}
