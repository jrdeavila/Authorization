<?php

namespace Tests\Feature\Permissions;

use App\Models\Permission;
use App\Models\Role;
use App\Services\Permissions\RoleService;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\Attributes\Test;

class RoleControllerTest extends PermissionsTestCase
{
    #[Test]
    public function guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('permissions.roles.index'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_user_can_list_roles(): void
    {
        $this->authenticatedAdmin();

        // The roles/index view calls $role->users()->count() which does a
        // cross-DB query (usuarios on timeit, model_has_roles on mysql).
        // This is impossible in SQLite. We mock the service to return an
        // empty paginator so the view's @forelse hits the @empty branch,
        // avoiding the cross-DB users()->count() call entirely.
        $this->mock(RoleService::class, function ($mock) {
            $mock->shouldReceive('all')
                ->once()
                ->andReturn(new LengthAwarePaginator([], 0, 15));
        });

        $response = $this->get(route('permissions.roles.index'));

        $response->assertStatus(200);
        $response->assertViewHas('roles');
    }

    #[Test]
    public function authenticated_user_can_create_a_role(): void
    {
        $this->authenticatedAdmin();

        $permission = Permission::create(['name' => 'posts.edit', 'guard_name' => 'web']);

        $response = $this->post(route('permissions.roles.store'), [
            'name'        => 'new-role',
            'permissions' => [$permission->id],
        ]);

        $response->assertRedirect(route('permissions.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', ['name' => 'new-role']);

        $role = Role::where('name', 'new-role')->first();
        $this->assertTrue($role->hasPermissionTo('posts.edit'));
    }

    #[Test]
    public function authenticated_user_can_update_a_role(): void
    {
        $this->authenticatedAdmin();

        $role = Role::create(['name' => 'old-name', 'guard_name' => 'web']);

        $response = $this->put(route('permissions.roles.update', $role->id), [
            'name' => 'updated-name',
        ]);

        $response->assertRedirect(route('permissions.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', ['name' => 'updated-name']);
        $this->assertDatabaseMissing('roles', ['name' => 'old-name']);
    }

    #[Test]
    public function authenticated_user_can_delete_an_empty_role(): void
    {
        $this->authenticatedAdmin();

        $role = Role::create(['name' => 'deletable-role', 'guard_name' => 'web']);

        // Mock the service to avoid the cross-DB users()->count() check
        // that happens in RoleService::delete().
        $this->mock(RoleService::class, function ($mock) {
            $mock->shouldReceive('delete')
                ->once()
                ->andReturnNull();
        });

        $response = $this->delete(route('permissions.roles.destroy', $role->id));

        $response->assertRedirect(route('permissions.roles.index'));
        $response->assertSessionHas('success');
    }
}
