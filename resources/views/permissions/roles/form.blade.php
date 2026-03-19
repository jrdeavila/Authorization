@extends('permissions.layouts.app')

@section('title', $role ? 'Editar Rol' : 'Nuevo Rol')

@push('css')
<style>
/* Permission group cards animation */
.card.mb-2 {
    transition: box-shadow 0.3s ease;
}
</style>
@endpush

@section('content_header')
<h1 class="m-0 text-dark">{{ $role ? 'Editar Rol' : 'Nuevo Rol' }}</h1>
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{ route('permissions.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">{{ $role ? 'Editar' : 'Crear' }}</li>
</ol>
@endsection

@section('module_content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-{{ $role ? 'edit' : 'plus' }} mr-2"></i>
                        {{ $role ? 'Editar Rol: ' . $role->name : 'Nuevo Rol' }}
                    </h3>
                </div>

                <form action="{{ $role ? route('permissions.roles.update', $role) : route('permissions.roles.store') }}"
                      method="POST"
                      x-data="{
                          search: '',
                          selected: @js($role ? $role->permissions->pluck('id')->toArray() : []),
                          groups: @js($groupedPermissions),
                          toggleGroup(perms) {
                              const ids = perms.map(p => p.id);
                              const allSelected = ids.every(id => this.selected.includes(id));
                              if (allSelected) { this.selected = this.selected.filter(id => !ids.includes(id)); }
                              else { ids.forEach(id => { if (!this.selected.includes(id)) this.selected.push(id) }); }
                          },
                          isGroupSelected(perms) { return perms.every(p => this.selected.includes(p.id)); },
                          get filteredGroups() {
                              if (!this.search) return this.groups;
                              const q = this.search.toLowerCase();
                              return Object.fromEntries(
                                  Object.entries(this.groups)
                                      .map(([k, perms]) => [k, perms.filter(p => p.name.toLowerCase().includes(q))])
                                      .filter(([k, perms]) => perms.length > 0)
                              );
                          }
                      }">
                    @csrf
                    @if($role) @method('PUT') @endif

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Nombre del Rol</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $role->name ?? '') }}" placeholder="Ej: supervisor" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mt-4">
                            <label class="font-weight-bold">Permisos</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
                                <input type="text" class="form-control" placeholder="Buscar permisos..." x-model="search" aria-label="Buscar permisos">
                            </div>

                            <template x-for="(perms, group) in filteredGroups" :key="group">
                                <div class="card mb-2 border">
                                    <div class="card-header py-2 px-3 bg-light d-flex justify-content-between align-items-center">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" :id="'group-' + group"
                                                   :checked="isGroupSelected(perms)" @change="toggleGroup(perms)">
                                            <label class="custom-control-label font-weight-bold text-uppercase small" :for="'group-' + group" x-text="group"></label>
                                        </div>
                                        <span class="badge badge-primary badge-pill" x-text="perms.filter(p => selected.includes(p.id)).length + '/' + perms.length"></span>
                                    </div>
                                    <div class="card-body py-2 px-3">
                                        <div class="row">
                                            <template x-for="perm in perms" :key="perm.id">
                                                <div class="col-md-6 col-lg-4 mb-1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" name="permissions[]"
                                                               :value="perm.id" :id="'perm-' + perm.id" :checked="selected.includes(perm.id)"
                                                               @change="selected.includes(perm.id) ? selected = selected.filter(id => id !== perm.id) : selected.push(perm.id)">
                                                        <label class="custom-control-label small" :for="'perm-' + perm.id" x-text="perm.name"></label>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('permissions.roles.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Volver</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ $role ? 'Actualizar' : 'Crear' }} Rol</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
