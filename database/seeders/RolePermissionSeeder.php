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

        $roles = [
            'admin' => [
                'list-users',
                'view-user',
                'edit-user',
                'delete-user',
                'create-user',
                'list-roles',
                'view-role',
                'edit-role',
                'delete-role',
                'create-role',
                'list-permissions',
                'view-permission',
                'edit-permission',
                'delete-permission',
                'create-permission',
            ],
            'technical-sheet-manager' => [
                'list-technical-sheets',
                'view-technical-sheet',
                'edit-technical-sheet',
                'delete-technical-sheet',
                'create-technical-sheet',
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
            ],
            'superadmin' => [
                'all-permissions'
            ],
            'employee' => [],
        ];

        foreach ($roles as $role => $permissions) {
            $role = Role::create(['name' => $role, 'guard_name' => 'web']);
            foreach ($permissions as $permission) {
                $permission = Permission::create(['name' => $permission, 'guard_name' => 'web']);
                $role->givePermissionTo($permission);
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
