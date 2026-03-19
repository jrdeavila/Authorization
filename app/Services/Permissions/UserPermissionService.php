<?php

namespace App\Services\Permissions;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class UserPermissionService
{
    public function getUsers(string $search = ''): LengthAwarePaginator
    {
        $query = User::whereHas('employee', function ($query) {
            $query->where('estado', 'Activo')
                ->where('tipofuncionario', 'Funcionario');
        })
            ->with(['employee.job', 'employee.curriculum', 'roles', 'permissions']);

        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                    ->orWhere('apellidos', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc(
            \App\Models\Employee::select('created_at')
                ->whereColumn('empleados.id', 'usuarios.Empleados_id')
                ->limit(1)
        );

        return $query->paginate(5);
    }

    public function getUserWithPermissions(User $user): array
    {
        $user->load(['employee.job', 'employee.curriculum', 'roles.permissions', 'permissions']);

        return [
            'user' => $user,
            'roles' => $user->roles,
            'directPermissions' => $user->getDirectPermissions(),
            'allPermissions' => $user->getAllPermissions(),
        ];
    }

    public function assignRoles(User $user, array $roleIds): void
    {
        $user->syncRoles($roleIds);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties(['roles' => $roleIds])
            ->log('Roles asignados al usuario: ' . ($user->employee->full_name ?? $user->id));
    }

    public function assignDirectPermissions(User $user, array $permissionIds): void
    {
        $user->syncPermissions($permissionIds);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties(['permissions' => $permissionIds])
            ->log('Permisos directos asignados al usuario: ' . ($user->employee->full_name ?? $user->id));
    }

    public function revokeRole(User $user, int $roleId): void
    {
        $role = Role::findOrFail($roleId);
        $user->removeRole($role);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties(['role' => $role->name])
            ->log('Rol revocado: ' . $role->name);
    }

    public function revokePermission(User $user, int $permissionId): void
    {
        $permission = Permission::findOrFail($permissionId);
        $user->revokePermissionTo($permission);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties(['permission' => $permission->name])
            ->log('Permiso revocado: ' . $permission->name);
    }
}
