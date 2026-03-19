<?php

namespace Tests\Feature\Permissions;

use App\Models\Permission;
use App\Models\Role;
use PHPUnit\Framework\Attributes\Test;

class PermissionControllerTest extends PermissionsTestCase
{
    #[Test]
    public function authenticated_user_can_list_permissions(): void
    {
        $this->authenticatedAdmin();

        Permission::create(['name' => 'users.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.edit', 'guard_name' => 'web']);

        $response = $this->get(route('permissions.permissions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('permissions');
    }

    #[Test]
    public function authenticated_user_can_create_a_permission(): void
    {
        $this->authenticatedAdmin();

        $response = $this->post(route('permissions.permissions.store'), [
            'name' => 'reports.generate',
        ]);

        $response->assertRedirect(route('permissions.permissions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('permissions', ['name' => 'reports.generate']);
    }

    #[Test]
    public function authenticated_user_can_update_a_permission(): void
    {
        $this->authenticatedAdmin();

        $permission = Permission::create(['name' => 'old.permission', 'guard_name' => 'web']);

        $response = $this->put(route('permissions.permissions.update', $permission->id), [
            'name' => 'new.permission',
        ]);

        $response->assertRedirect(route('permissions.permissions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('permissions', ['name' => 'new.permission']);
        $this->assertDatabaseMissing('permissions', ['name' => 'old.permission']);
    }

    #[Test]
    public function cannot_delete_permission_attached_to_a_role(): void
    {
        $this->authenticatedAdmin();

        $permission = Permission::create(['name' => 'protected.perm', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'protector', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $response = $this->delete(route('permissions.permissions.destroy', $permission->id));

        $response->assertRedirect(route('permissions.permissions.index'));
        $response->assertSessionHas('error');

        // Permission should still exist
        $this->assertDatabaseHas('permissions', ['name' => 'protected.perm']);
    }
}
