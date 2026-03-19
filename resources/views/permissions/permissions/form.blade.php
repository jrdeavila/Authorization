@extends('adminlte::page')

@section('title', $permission ? 'Editar Permiso' : 'Nuevo Permiso')

@section('content_header')
<h1 class="m-0 text-dark">{{ $permission ? 'Editar Permiso' : 'Nuevo Permiso' }}</h1>
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{ route('permissions.permissions.index') }}">Permisos</a></li>
    <li class="breadcrumb-item active">{{ $permission ? 'Editar' : 'Crear' }}</li>
</ol>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h3 class="card-title mb-0"><i class="fas fa-{{ $permission ? 'edit' : 'plus' }} mr-2"></i>{{ $permission ? 'Editar: ' . $permission->name : 'Nuevo Permiso' }}</h3>
                </div>
                <form action="{{ $permission ? route('permissions.permissions.update', $permission) : route('permissions.permissions.store') }}" method="POST">
                    @csrf
                    @if($permission) @method('PUT') @endif
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Nombre del Permiso</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $permission->name ?? '') }}" placeholder="Ej: modulo.accion" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">Convención: <code>modulo.accion</code> (ej: roles.manage)</small>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('permissions.permissions.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Volver</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ $permission ? 'Actualizar' : 'Crear' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
