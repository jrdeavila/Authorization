<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'roles.manage',
            'permissions.manage',
            'users.assign',
            'audit.view',
            'reports.export',
            'reports.import',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );
        }

        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'web']
        );

        $superAdmin->syncPermissions(Permission::where('guard_name', 'web')->get());
    }
}
