<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos para crear mas permisos
        $roles = [
            'admin',
            'superadmin',
            'employee',
            'technical-sheet-manager',
            'activity-manager',
        ];

        // Grouped
        $permissions = [
            'permissions' => [
                'create',
                'read',
                'update',
                'delete',
            ],
            'roles' => [
                'create',
                'read',
                'update',
                'delete',
            ],
            'users' => [
                'create',
                'read',
                'update',
                'delete',
            ],
            'technical-sheets' => [
                'create',
                'read',
                'update',
                'delete',
            ],
            'list-activities',
            'view-activity',
            'edit-activity',
            'delete-activity',
            'show-activity-owner',
            'create-activity',
            'view-activity-report',
            'assign-activity',
        ];
        $rolesWithPermissionsGroup = [
            'admin' => [
                'permissions',
                'roles',
                'users',
                'technical-sheets',
            ],
            'technical-sheet-manager' => [
                'technical-sheets',
            ],
            'activity-manager' => [
                'list-activities',
                'view-activity',
                'edit-activity',
                'delete-activity',
                'show-activity-owner',
                'create-activity',
                'view-activity-report',
                'assign-activity',
            ]
        ];

        // Crear roles
        foreach ($roles as $role) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }

        echo 'Roles creados' . PHP_EOL;

        // Crear permisos
        foreach ($permissions as $group => $records) {
            if (is_array($records)) {
                foreach ($records as $permission) {
                    Permission::create(['name' => $group . '-' . $permission, 'guard_name' => 'web']);
                }
            }
            if (is_string($records)) {
                Permission::create(['name' => $records, 'guard_name' => 'web']);
            }
        }

        echo 'Permisos creados' . PHP_EOL;

        // Asignar permisos
        foreach ($roles as $role) {
            $model = Role::where('name', $role)->first();
            if (array_key_exists($role, $rolesWithPermissionsGroup)) {
                foreach ($rolesWithPermissionsGroup[$role] as $group) {
                    if (is_array($group)) {
                        foreach ($permissions[$group] as $permission) {
                            $p = Permission::where('name', $group . '-' . $permission)->first();
                            $model->givePermissionTo($p);
                        }
                    } else {
                        $p = Permission::where('name', $group)->first();
                        $model->givePermissionTo($p);
                    }
                }
            }
        }

        echo 'Asignar rol de superadmin a usuario con dni 1003316620' . PHP_EOL;
        $user = User::whereHas('employee', function ($query) {
            $query->where('noDocumento', '1003316620');
        })->first();

        echo 'Asignar rol de empleado a todos los usuarios' . PHP_EOL;
        $users = User::whereHas('employee')->get();
        foreach ($users as $u) {
            $u->assignRole('employee');
        }

        $user->assignRole('superadmin');
    }
}
