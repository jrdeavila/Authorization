@extends('permissions.layouts.app')

@section('title', 'Dashboard')

@push('css')
<style>
/* Stat Cards */
.stat-card {
    border: none;
    border-radius: 14px;
    overflow: hidden;
    height: 100%;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12) !important;
}
.stat-card .card-body {
    height: 100%;
    min-height: 90px;
}
.stat-icon {
    width: 56px;
    height: 56px;
    min-width: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}
.stat-value {
    font-size: 1.8rem;
    font-weight: 800;
    line-height: 1;
    color: #1e293b;
    min-width: 40px;
}
.stat-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 4px;
    line-height: 1.2;
}

/* Activity timeline */
.activity-item {
    display: flex;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}
.activity-item:last-child {
    border-bottom: none;
}
.activity-item:hover {
    background: #f8fafc;
    border-radius: 8px;
    padding-left: 8px;
    margin-left: -8px;
}
.activity-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-top: 6px;
    flex-shrink: 0;
}
.activity-text {
    flex: 1;
    font-size: 0.85rem;
    color: #334155;
}
.activity-time {
    font-size: 0.75rem;
    color: #94a3b8;
    white-space: nowrap;
}

/* New user row */
.new-user-row {
    transition: transform 0.15s ease, background 0.15s ease;
}
.new-user-row:hover {
    transform: translateX(3px);
}

/* Responsive */
@media (max-width: 767.98px) {
    .stat-value { font-size: 1.4rem; }
    .stat-icon { width: 44px; height: 44px; min-width: 44px; font-size: 18px; }
    .stat-card .card-body { padding: 1rem; min-height: 76px; }
    .stat-label { font-size: 0.65rem; }
}
</style>
@endpush

@section('content_header')
<h1 class="m-0 text-dark">Dashboard</h1>
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Inicio</li>
</ol>
@endsection

@section('module_content')
<div class="container-fluid py-3">

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-6 mb-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-primary text-white mr-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ number_format($activeEmployees) }}</div>
                        <div class="stat-label">Empleados Activos</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6 mb-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success text-white mr-3">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $usersWithRoles }}</div>
                        <div class="stat-label">Con Roles</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6 mb-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-info text-white mr-3">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $totalRoles }}</div>
                        <div class="stat-label">Roles</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6 mb-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-warning text-white mr-3">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $totalPermissions }}</div>
                        <div class="stat-label">Permisos</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Actividad Reciente --}}
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-history mr-2"></i>Actividad Reciente</h5>
                    <a href="{{ route('permissions.audit.index') }}" class="btn btn-sm btn-light">Ver todo</a>
                </div>
                <div class="card-body">
                    @forelse($recentActivities as $activity)
                        <div class="activity-item">
                            <div class="activity-dot bg-{{ $activity->subject_type === 'App\\Models\\Role' ? 'info' : ($activity->subject_type === 'App\\Models\\Permission' ? 'warning' : 'primary') }}"></div>
                            <div class="activity-text">
                                {{ $activity->description }}
                                @if($activity->causer)
                                    <span class="text-muted">— {{ $activity->causer->employee->full_name ?? 'Usuario #' . $activity->causer_id }}</span>
                                @endif
                            </div>
                            <div class="activity-time">
                                {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-clipboard-list fa-2x mb-3 d-block" style="opacity:0.3"></i>
                            <p class="mb-0">No hay actividad registrada aún.</p>
                            <small>Las acciones sobre roles y permisos aparecerán aquí.</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Usuarios Recientes --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-user-plus mr-2"></i>Usuarios Recientes</h5>
                    <a href="{{ route('permissions.users.index') }}" class="btn btn-sm btn-light">Ver todos</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @forelse($newUsers as $user)
                                    <tr class="new-user-row">
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->employee->curriculum->photo ?? '' }}"
                                                     alt="{{ $user->employee->full_name ?? 'usuario' }}"
                                                     class="img-circle mr-3"
                                                     style="width:36px; height:36px; object-fit:cover; border:2px solid #e2e8f0;"
                                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->employee->full_name ?? 'U') }}&size=36&background=28a745&color=fff'">
                                                <div style="min-width:0">
                                                    <div class="font-weight-bold text-truncate">{{ $user->employee->full_name ?? 'N/A' }}</div>
                                                    <small class="text-muted text-truncate d-block">{{ $user->employee->job->name ?? 'Sin cargo' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right align-middle d-none d-sm-table-cell">
                                            @forelse($user->roles as $role)
                                                <span class="badge badge-primary">{{ $role->name }}</span>
                                            @empty
                                                <span class="badge badge-light">Sin rol</span>
                                            @endforelse
                                        </td>
                                        <td class="text-right align-middle" style="width:40px">
                                            <a href="{{ route('permissions.users.show', $user) }}" class="text-primary" title="Gestionar">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No hay usuarios registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
