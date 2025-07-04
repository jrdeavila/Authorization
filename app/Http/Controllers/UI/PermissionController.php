<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:permissions-read')->only('index');
        $this->middleware('can:permissions-create')->only('create', 'store');
        $this->middleware('can:permissions-update')->only('edit', 'update');
        $this->middleware('can:permissions-delete')->only('destroy');
    }
    public function index()
    {
        $permissions = Permission::paginate(10);
        return view('pages.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('pages.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'guard_name' => 'required|string',
        ]);
        Permission::create($request->all());
        return redirect()->route('permissions.index')->with('success', 'Permiso creado correctamente');
    }

    public function edit(Permission $permission)
    {
        return view('pages.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string',
            'guard_name' => 'required|string',
        ]);
        $permission->update($request->all());
        return redirect()->route('permissions.index')->with('success', 'Permiso actualizado correctamente');
    }


    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permiso eliminado correctamente');
    }
}
