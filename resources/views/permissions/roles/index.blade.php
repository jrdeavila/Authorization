@extends('permissions.layouts.app')

@section('title', 'Gestión de Roles')

@section('content_header')
<h1 class="m-0 text-dark">Gestión de Roles</h1>
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{ route('permissions.roles.index') }}">Permisos</a></li>
    <li class="breadcrumb-item active">Roles</li>
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
            <h3 class="card-title mb-0"><i class="fas fa-user-tag mr-2"></i>Roles del Sistema</h3>
            <a href="{{ route('permissions.roles.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus mr-1"></i> Nuevo Rol
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Nombre</th>
                            <th class="text-center d-none d-md-table-cell">Permisos</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td class="font-weight-bold">{{ $role->name }}</td>
                                <td class="text-center d-none d-md-table-cell"><span class="badge badge-info">{{ $role->permissions->count() }}</span></td>
                                <td class="text-right">
                                    <a href="{{ route('permissions.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="delete-role-{{ $role->id }}" action="{{ route('permissions.roles.destroy', $role) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete('delete-role-{{ $role->id }}', '{{ addslashes($role->name) }}', 'el rol')"
                                        title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No hay roles registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $roles->links('pagination::bootstrap-4') }}</div>
    </div>

</div>
@endsection
