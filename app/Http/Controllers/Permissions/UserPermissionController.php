<?php

namespace App\Http\Controllers\Permissions;

use App\Exports\PermissionsExport;
use App\Http\Controllers\Controller;
use App\Imports\PermissionsImport;
use App\Models\Role;
use App\Models\User;
use App\Services\Permissions\PermissionService;
use App\Services\Permissions\UserPermissionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class UserPermissionController extends Controller
{
    public function __construct(
        private UserPermissionService $userPermissionService,
        private PermissionService $permissionService,
    ) {}

    public function index(Request $request): View
    {
        $request->validate([
            'search' => 'sometimes|string'
        ]);

        $users = $this->userPermissionService->getUsers($request->get('search', ''));

        return view('permissions.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $data = $this->userPermissionService->getUserWithPermissions($user);
        $allRoles = Role::where('guard_name', 'web')->orderBy('name')->get();
        $groupedPermissions = $this->permissionService->allGrouped();

        return view('permissions.users.assign', array_merge($data, [
            'allRoles' => $allRoles,
            'groupedPermissions' => $groupedPermissions,
        ]));
    }

    public function assign(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if ($request->has('roles')) {
            $this->userPermissionService->assignRoles($user, $request->input('roles', []));
        }

        if ($request->has('permissions')) {
            $this->userPermissionService->assignDirectPermissions($user, $request->input('permissions', []));
        }

        return redirect()->route('permissions.users.show', $user)
            ->with('success', 'Permisos actualizados correctamente.');
    }

    public function revoke(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role_id' => 'required_without:permission_id|integer',
            'permission_id' => 'required_without:role_id|integer',
            'type' => 'required|in:role,permission',
        ]);

        if ($request->type === 'role') {
            $this->userPermissionService->revokeRole($user, $request->role_id);
        } else {
            $this->userPermissionService->revokePermission($user, $request->permission_id);
        }

        return redirect()->route('permissions.users.show', $user)
            ->with('success', 'Acceso revocado correctamente.');
    }

    public function exportPdf(): \Illuminate\Http\Response
    {
        $users = User::with(['employee.job', 'roles.permissions', 'permissions'])->get();

        $pdf = Pdf::loadView('permissions.reports.pdf', compact('users'));

        return $pdf->download('permisos-funcionarios.pdf');
    }

    public function exportExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new PermissionsExport(), 'permisos-funcionarios.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $import = new PermissionsImport();
        Excel::import($import, $request->file('file'));

        return redirect()->route('permissions.users.index')
            ->with('success', "Importación completada. {$import->getImportedCount()} registros procesados.");
    }
}
