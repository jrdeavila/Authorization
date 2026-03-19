<?php

namespace App\Http\Controllers\Permissions;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\Permissions\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function __construct(
        private PermissionService $permissionService,
    ) {}

    public function index(): View
    {
        $permissions = $this->permissionService->all();

        return view('permissions.permissions.index', compact('permissions'));
    }

    public function create(): View
    {
        $permission = null;

        return view('permissions.permissions.form', compact('permission'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name',
        ]);

        $this->permissionService->create($request->only('name'));

        return redirect()->route('permissions.permissions.index')
            ->with('success', 'Permiso creado correctamente.');
    }

    public function show(int $id): RedirectResponse
    {
        return redirect()->route('permissions.permissions.edit', $id);
    }

    public function edit(int $id): View
    {
        $permission = $this->permissionService->find($id);

        return view('permissions.permissions.form', compact('permission'));
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name,' . $permission->id,
        ]);

        $this->permissionService->update($permission, $request->only('name'));

        return redirect()->route('permissions.permissions.index')
            ->with('success', 'Permiso actualizado correctamente.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        try {
            $this->permissionService->delete($permission);
            return redirect()->route('permissions.permissions.index')
                ->with('success', 'Permiso eliminado correctamente.');
        } catch (\RuntimeException $e) {
            return redirect()->route('permissions.permissions.index')
                ->with('error', $e->getMessage());
        }
    }
}
