@extends('permissions.layouts.app')

@section('title', 'Asignar Permisos - ' . ($user->employee->full_name ?? ''))

@push('css')
<style>
/* User header card responsive */
@media (max-width: 767.98px) {
    .user-header-card {
        flex-direction: column;
        text-align: center;
    }
    .user-header-card img {
        margin-right: 0 !important;
        margin-bottom: 1rem;
    }
    .user-header-card .btn {
        margin-left: 0 !important;
        margin-top: 1rem;
        width: 100%;
    }
}
</style>
@endpush

@section('content_header')
<h1 class="m-0 text-dark">Asignar Permisos</h1>
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{ route('permissions.users.index') }}">Funcionarios</a></li>
    <li class="breadcrumb-item active">{{ $user->employee->full_name ?? 'Asignar' }}</li>
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

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex align-items-center flex-wrap user-header-card">
            <img src="{{ $user->employee->curriculum->photo ?? '' }}"
                 alt="Foto de {{ $user->employee->full_name ?? 'usuario' }}"
                 class="img-circle elevation-2 mr-4" style="width:80px; height:80px; object-fit:cover;"
                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->employee->full_name ?? 'U') }}&size=80'">
            <div>
                <h4 class="mb-1 font-weight-bold">{{ $user->employee->full_name ?? 'N/A' }}</h4>
                <p class="text-muted mb-0">{{ $user->employee->job->name ?? 'Sin cargo' }} &middot; {{ $user->email }}</p>
            </div>
            <a href="{{ route('permissions.users.index') }}" class="btn btn-secondary ml-auto"><i class="fas fa-arrow-left mr-1"></i> Volver</a>
        </div>
    </div>

    <form action="{{ route('permissions.users.assign', $user) }}" method="POST"
          x-data="{
              selectedRoles: @js($roles->pluck('id')->toArray()),
              selectedPerms: @js($directPermissions->pluck('id')->toArray()),
              search: '',
              groups: @js($groupedPermissions),
              get filteredGroups() {
                  if (!this.search) return this.groups;
                  const q = this.search.toLowerCase();
                  return Object.fromEntries(
                      Object.entries(this.groups).map(([k, perms]) => [k, perms.filter(p => p.name.toLowerCase().includes(q))]).filter(([k, perms]) => perms.length > 0)
                  );
              }
          }">
        @csrf

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-gradient-info text-white"><h5 class="card-title mb-0"><i class="fas fa-user-tag mr-2"></i>Roles</h5></div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($allRoles as $role)
                                <div class="col-md-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="roles[]" value="{{ $role->id }}" id="role-{{ $role->id }}"
                                               :checked="selectedRoles.includes({{ $role->id }})"
                                               @change="selectedRoles.includes({{ $role->id }}) ? selectedRoles = selectedRoles.filter(id => id !== {{ $role->id }}) : selectedRoles.push({{ $role->id }})">
                                        <label class="custom-control-label" for="role-{{ $role->id }}">{{ $role->name }} <small class="text-muted">({{ $role->permissions->count() }})</small></label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-gradient-warning text-white"><h5 class="card-title mb-0"><i class="fas fa-key mr-2"></i>Permisos Directos</h5></div>
                    <div class="card-body">
                        <div class="alert alert-warning py-2 small"><i class="fas fa-info-circle mr-1"></i>Los permisos directos son excepcionales. Prefiera asignar roles.</div>
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
                            <input type="text" class="form-control" placeholder="Buscar permisos..." x-model="search" aria-label="Buscar permisos">
                        </div>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <template x-for="(perms, group) in filteredGroups" :key="group">
                                <div class="mb-2">
                                    <strong class="text-uppercase small text-muted" x-text="group"></strong>
                                    <template x-for="perm in perms" :key="perm.id">
                                        <div class="custom-control custom-checkbox ml-3">
                                            <input type="checkbox" class="custom-control-input" name="permissions[]" :value="perm.id" :id="'dperm-' + perm.id"
                                                   :checked="selectedPerms.includes(perm.id)"
                                                   @change="selectedPerms.includes(perm.id) ? selectedPerms = selectedPerms.filter(id => id !== perm.id) : selectedPerms.push(perm.id)">
                                            <label class="custom-control-label small" :for="'dperm-' + perm.id" x-text="perm.name"></label>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right mb-4">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save mr-2"></i> Guardar Cambios</button>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-secondary text-white"><h5 class="card-title mb-0"><i class="fas fa-shield-alt mr-2"></i>Permisos Efectivos (Solo Lectura)</h5></div>
        <div class="card-body">
            <div class="row">
                @forelse($allPermissions as $perm)
                    <div class="col-md-4 col-lg-3 mb-2">
                        <span class="badge badge-light border p-2 d-block text-left">
                            <i class="fas fa-check text-success mr-1"></i>{{ $perm->name }}
                            @if($directPermissions->contains('id', $perm->id))
                                <span class="badge badge-warning badge-pill ml-1" title="Directo">D</span>
                            @else
                                <span class="badge badge-info badge-pill ml-1" title="Heredado">R</span>
                            @endif
                        </span>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-3">Sin permisos asignados.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
