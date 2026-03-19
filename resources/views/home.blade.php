@extends('permissions.layouts.app')

@section('title', 'Dashboard')

@push('css')
<style>
/* ==============================
   HERO WELCOME
   ============================== */
.hero-welcome {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
    border-radius: 18px;
    padding: 28px 30px;
    color: #fff;
    margin-bottom: 24px;
    box-shadow: 0 10px 30px rgba(30,58,138,0.25);
    position: relative;
    overflow: hidden;
}
.hero-welcome::after {
    content: '';
    position: absolute;
    top: -40%;
    right: -10%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.hero-welcome h2 {
    font-size: 1.4rem;
    font-weight: 800;
    margin: 0 0 4px;
    color: #fff;
}
.hero-welcome p {
    font-size: 0.88rem;
    opacity: 0.8;
    margin: 0;
}

/* ==============================
   STAT CARDS
   ============================== */
.stat-card {
    border-radius: 16px !important;
    overflow: hidden;
    height: 100%;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 35px rgba(0,0,0,0.12) !important;
}
.stat-card .card-body {
    height: 100%;
    min-height: 90px;
    padding: 20px;
}
.stat-icon {
    width: 52px;
    height: 52px;
    min-width: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.stat-value {
    font-size: 1.7rem;
    font-weight: 800;
    line-height: 1;
    color: #1e293b;
    min-width: 36px;
}
.stat-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 4px;
    line-height: 1.2;
}

/* Dark mode stat values */
body.dark-mode .stat-value { color: #f1f5f9; }
body.dark-mode .stat-label { color: #94a3b8; }
body.dark-mode .hero-welcome { background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); }

/* ==============================
   ACTIVITY TIMELINE
   ============================== */
.activity-item {
    display: flex;
    gap: 12px;
    padding: 12px 8px;
    border-bottom: 1px solid #f1f5f9;
    border-radius: 8px;
    transition: background 0.15s ease;
}
.activity-item:last-child { border-bottom: none; }
.activity-item:hover { background: #f8fafc; }
body.dark-mode .activity-item { border-bottom-color: #334155; }
body.dark-mode .activity-item:hover { background: rgba(255,255,255,0.03); }

.activity-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-top: 5px;
    flex-shrink: 0;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
}
.activity-text {
    flex: 1;
    font-size: 0.85rem;
    color: #334155;
    line-height: 1.4;
}
body.dark-mode .activity-text { color: #cbd5e1; }

.activity-time {
    font-size: 0.72rem;
    color: #94a3b8;
    white-space: nowrap;
    font-weight: 500;
}

/* ==============================
   NEW USER ROWS
   ============================== */
.new-user-row {
    transition: transform 0.15s ease, background 0.15s ease;
}
.new-user-row:hover {
    transform: translateX(3px);
}

/* ==============================
   EMPTY STATE
   ============================== */
.empty-state {
    text-align: center;
    padding: 40px 20px;
}
.empty-state-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 14px;
    font-size: 1.5rem;
    color: #94a3b8;
}
body.dark-mode .empty-state-icon { background: #334155; }

/* ==============================
   RESPONSIVE
   ============================== */
@media (max-width: 991.98px) {
    .hero-welcome { padding: 22px 20px; }
    .hero-welcome h2 { font-size: 1.15rem; }
    .stat-value { font-size: 1.35rem; }
    .stat-icon { width: 44px; height: 44px; min-width: 44px; font-size: 17px; }
    .stat-card .card-body { padding: 14px; min-height: 74px; }
    .stat-label { font-size: 0.6rem; }
}
</style>
@endpush

@section('content_header')
@endsection

@section('module_content')
<div class="container-fluid py-3">

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    {{-- Hero Welcome --}}
    @auth
    <div class="hero-welcome">
        <h2><i class="fas fa-shield-alt mr-2"></i>Gestión de Permisos</h2>
        <p>Bienvenido, {{ auth()->user()->employee->full_name ?? 'Administrador' }}</p>
    </div>
    @endauth

    {{-- Stat Cards --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-6 mb-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon mr-3" style="background:#dbeafe; color:#1d4ed8;">
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
                    <div class="stat-icon mr-3" style="background:#d1fae5; color:#059669;">
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
                    <div class="stat-icon mr-3" style="background:#e0e7ff; color:#4338ca;">
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
                    <div class="stat-icon mr-3" style="background:#fef3c7; color:#d97706;">
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
                                    <br><small class="text-muted">{{ $activity->causer->employee->full_name ?? 'Usuario #' . $activity->causer_id }}</small>
                                @endif
                            </div>
                            <div class="activity-time">
                                {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <p class="font-weight-bold mb-1">Sin actividad</p>
                            <small class="text-muted">Las acciones sobre roles y permisos aparecerán aquí.</small>
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
                                        <td class="py-3 pl-3">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->employee->curriculum->photo ?? '' }}"
                                                     alt="{{ $user->employee->full_name ?? 'usuario' }}"
                                                     class="img-circle mr-3"
                                                     style="width:40px; height:40px; object-fit:cover; border:2px solid #e2e8f0;"
                                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->employee->full_name ?? 'U') }}&size=40&background=28a745&color=fff'">
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
                                        <td class="text-right align-middle pr-3" style="width:40px">
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
