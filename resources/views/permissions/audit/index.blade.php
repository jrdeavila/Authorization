@extends('permissions.layouts.app')

@section('title', 'Auditoría de Permisos')

@push('css')
<style>
/* Filter form spacing on mobile */
@media (max-width: 767.98px) {
    .filter-form .col-md-3 {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@section('content_header')
<h1 class="m-0 text-dark">Auditoría</h1>
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{ route('permissions.roles.index') }}">Permisos</a></li>
    <li class="breadcrumb-item active">Auditoría</li>
</ol>
@endsection

@section('module_content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h3 class="card-title mb-0"><i class="fas fa-history mr-2"></i>Registro de Auditoría</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('permissions.audit.index') }}" method="GET" class="mb-4 filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <label for="subject_type" class="small font-weight-bold">Tipo de Recurso</label>
                        <select name="subject_type" id="subject_type" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            <option value="App\Models\Role" {{ request('subject_type') == 'App\Models\Role' ? 'selected' : '' }}>Roles</option>
                            <option value="App\Models\Permission" {{ request('subject_type') == 'App\Models\Permission' ? 'selected' : '' }}>Permisos</option>
                            <option value="App\Models\User" {{ request('subject_type') == 'App\Models\User' ? 'selected' : '' }}>Usuarios</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="small font-weight-bold">Desde</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="small font-weight-bold">Hasta</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="fas fa-filter mr-1"></i> Filtrar</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="thead-light">
                        <tr><th>Fecha</th><th>Acción</th><th class="d-none d-md-table-cell">Recurso</th><th>Ejecutado por</th></tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            <tr>
                                <td class="text-muted small">{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $activity->description }}</td>
                                <td class="d-none d-md-table-cell">
                                    @if($activity->subject_type)
                                        <span class="badge badge-light">{{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($activity->causer)
                                        {{ $activity->causer->employee->full_name ?? 'Usuario #' . $activity->causer_id }}
                                    @else
                                        <span class="text-muted">Sistema</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No hay registros de auditoría.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $activities->appends(request()->query())->links('pagination::bootstrap-4') }}</div>
    </div>
</div>
@endsection
