@extends('permissions.layouts.app')

@section('title', 'Funcionarios - Permisos')

@push('css')
<style>
@media (max-width: 767.98px) {
    .card-header .input-group {
        min-width: 100%;
    }
}
</style>
@endpush

@section('content_header')
<h1 class="m-0 text-dark">Funcionarios</h1>
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{ route('permissions.roles.index') }}">Permisos</a></li>
    <li class="breadcrumb-item active">Funcionarios</li>
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

    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="card-title mb-0"><i class="fas fa-users mr-2"></i>Funcionarios</h3>
            <div class="d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                <form action="{{ route('permissions.users.index') }}" method="GET">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}" aria-label="Buscar funcionarios" style="border-radius: 20px 0 0 20px;">
                        <div class="input-group-append">
                            <button class="btn btn-light" type="submit" style="border-radius: 0 20px 20px 0;"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('permissions.reports.pdf') }}" class="btn btn-light" title="Exportar PDF"><i class="fas fa-file-pdf text-danger"></i></a>
                    <a href="{{ route('permissions.reports.excel') }}" class="btn btn-light" title="Exportar Excel"><i class="fas fa-file-excel text-success"></i></a>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Funcionario</th>
                            <th class="d-none d-md-table-cell">Cargo</th>
                            <th class="text-center">Roles</th>
                            <th class="text-center d-none d-md-table-cell">Permisos Directos</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->employee->curriculum->photo ?? '' }}"
                                             alt="Foto de {{ $user->employee->full_name ?? 'usuario' }}"
                                             class="img-circle elevation-1 mr-3" style="width:40px; height:40px; object-fit:cover;"
                                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->employee->full_name ?? 'U') }}&size=40'">
                                        <div>
                                            <div class="font-weight-bold">{{ $user->employee->full_name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted d-none d-md-table-cell">{{ $user->employee->job->name ?? 'Sin cargo' }}</td>
                                <td class="text-center">
                                    @forelse($user->roles as $role)
                                        <span class="badge badge-primary">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted small">-</span>
                                    @endforelse
                                </td>
                                <td class="text-center d-none d-md-table-cell"><span class="badge badge-info">{{ $user->permissions->count() }}</span></td>
                                <td class="text-right">
                                    <a href="{{ route('permissions.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="Gestionar permisos">
                                        <i class="fas fa-user-shield"></i> Gestionar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No se encontraron funcionarios.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}</div>
    </div>
</div>
@endsection
