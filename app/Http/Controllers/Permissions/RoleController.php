<?php

namespace App\Http\Controllers\Permissions;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\Permissions\PermissionService;
use App\Services\Permissions\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(
        private RoleService $roleService,
        private PermissionService $permissionService,
    ) {}

    public function index(): View
    {
        $roles = $this->roleService->all();

        return view('permissions.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $role = null;
        $groupedPermissions = $this->permissionService->allGrouped();

        return view('permissions.roles.form', compact('role', 'groupedPermissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $this->roleService->create($request->only('name', 'permissions'));

        return redirect()->route('permissions.roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    public function show(int $id): RedirectResponse
    {
        return redirect()->route('permissions.roles.edit', $id);
    }

    public function edit(int $id): View
    {
        $role = $this->roleService->find($id);
        $groupedPermissions = $this->permissionService->allGrouped();

        return view('permissions.roles.form', compact('role', 'groupedPermissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $this->roleService->update($role, $request->only('name', 'permissions'));

        return redirect()->route('permissions.roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        try {
            $this->roleService->delete($role);
            return redirect()->route('permissions.roles.index')
                ->with('success', 'Rol eliminado correctamente.');
        } catch (\RuntimeException $e) {
            return redirect()->route('permissions.roles.index')
                ->with('error', $e->getMessage());
        }
    }
}
