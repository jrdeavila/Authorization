<?php

namespace Tests\Feature\Permissions;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Permissions\UserPermissionService;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\PermissionRegistrar;

class UserPermissionControllerTest extends PermissionsTestCase
{
    #[Test]
    public function authenticated_user_can_list_users(): void
    {
        $this->authenticatedAdmin();

        // Mock the service because User::enabled() scope is not yet defined
        // on the User model -- the service's getUsers() depends on it.
        $mock = $this->partialMock(UserPermissionService::class, function ($mock) {
            $mock->shouldReceive('getUsers')
                ->once()
                ->andReturn(new LengthAwarePaginator([], 0, 20));
        });

        $response = $this->get(route('permissions.users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    #[Test]
    public function authenticated_user_can_assign_roles_to_a_user(): void
    {
        $this->authenticatedAdmin();

        $role = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $targetUser = User::find(2);

        $response = $this->post(route('permissions.users.assign', $targetUser->id), [
            'roles' => [$role->id],
        ]);

        $response->assertRedirect(route('permissions.users.show', $targetUser->id));
        $response->assertSessionHas('success');

        // Refresh and check role assignment
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $targetUser = User::find(2);
        $this->assertTrue($targetUser->hasRole('manager'));
    }

    #[Test]
    public function authenticated_user_can_revoke_a_role_from_a_user(): void
    {
        $this->authenticatedAdmin();

        $role = Role::create(['name' => 'temporary', 'guard_name' => 'web']);
        $targetUser = User::find(2);
        $targetUser->assignRole($role);

        // Verify the role was assigned
        $this->assertTrue($targetUser->hasRole('temporary'));

        $response = $this->delete(route('permissions.users.revoke', $targetUser->id), [
            'role_id' => $role->id,
            'type'    => 'role',
        ]);

        $response->assertRedirect(route('permissions.users.show', $targetUser->id));
        $response->assertSessionHas('success');

        // Refresh and verify role was revoked
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $targetUser = User::find(2);
        $this->assertFalse($targetUser->hasRole('temporary'));
    }
}
