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
            'activity-user' => [
                'list-activities',
                'view-activity',
                'create-activity',
            ]
        ];

        foreach ($roles as $role => $permissions) {
            $role = Role::create(['name' => $role, 'guard_name' => 'web']);
            foreach ($permissions as $permission) {
                $p = Permission::firstWhere('name', $permission);
                if (!$p) {
                    $p = Permission::create(['name' => $permission]);
                }
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

        echo "Asignar rol de activity-manager a usuario con dni 1065655810" . PHP_EOL;
        $user = User::whereHas('employee', function ($query) {
            $query->where('noDocumento', '1065655810');
        })->first();
        $user->assignRole('activity-manager');

        echo "Asignado rol de activity-user a todos los usuarios de aseo general" . PHP_EOL;
        $dnis = [
            "57428394",
            "49605295",
            "39464002",
            "39463873",
        ];

        foreach ($dnis as $dni) {
            $user = User::whereHas('employee', function ($query) use ($dni) {
                $query->where('noDocumento', $dni);
            })->first();
            $user->assignRole('activity-user');
        }
    }
}
