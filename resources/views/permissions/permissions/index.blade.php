@extends('permissions.layouts.app')

@section('title', 'Gestión de Permisos')

@section('content_header')
<h1 class="m-0 text-dark">Gestión de Permisos</h1>
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{ route('permissions.roles.index') }}">Permisos</a></li>
    <li class="breadcrumb-item active">Permisos</li>
</ol>
@endsection

@section('module_content')
<div class="container-fluid py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="card-title mb-0"><i class="fas fa-key mr-2"></i>Permisos del Sistema</h3>
            <a href="{{ route('permissions.permissions.create') }}" class="btn btn-light btn-sm"><i class="fas fa-plus mr-1"></i> Nuevo Permiso</a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Nombre</th>
                            <th class="text-center d-none d-md-table-cell">Guard</th>
                            <th class="text-center d-none d-md-table-cell">Roles que lo usan</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td><code>{{ $permission->name }}</code></td>
                                <td class="text-center d-none d-md-table-cell"><span class="badge badge-light">{{ $permission->guard_name }}</span></td>
                                <td class="text-center d-none d-md-table-cell"><span class="badge badge-info">{{ $permission->roles()->count() }}</span></td>
                                <td class="text-right">
                                    <a href="{{ route('permissions.permissions.edit', $permission) }}" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></a>
                                    <form id="delete-perm-{{ $permission->id }}" action="{{ route('permissions.permissions.destroy', $permission) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete('delete-perm-{{ $permission->id }}', '{{ addslashes($permission->name) }}', 'el permiso')" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No hay permisos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $permissions->links('pagination::bootstrap-4') }}</div>
    </div>

</div>
@endsection
