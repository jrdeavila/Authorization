@extends('layouts.app')

@section('title', 'Información del usuario')

@section('content_header')
    <h1>Información del usuario</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-adminlte-card theme="light" title="Información del Usuario" icon="fas fa-user">
                <x-slot name="toolsSlot">
                    <div class="row">
                        @can('users-update')
                            @if ($user->hasRole('superadmin'))
                                @role('superadmin')
                                    <x-adminlte-button class="btn-flat" icon="fas fa-edit" label="Editar" data-toggle="modal"
                                        data-target="#editUserModal" />
                                    <x-adminlte-modal id="editUserModal" title="Editar Usuario" size="lg" theme="warning"
                                        icon="fas fa-edit" v-centered>
                                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <x-adminlte-select name="roles[]" label="Rol" multiple>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ $user->roles->contains($role->id) ? 'selected' : '' }}>
                                                        {{ $role->name }}</option>
                                                @endforeach
                                            </x-adminlte-select>
                                            <x-adminlte-select name="permissions[]" label="Permisos" multiple>
                                                @foreach ($permissions as $permission)
                                                    <option value="{{ $permission->id }}"
                                                        {{ $user->permissions->contains($permission->id) ? 'selected' : '' }}>
                                                        {{ $permission->name }} </option>
                                                @endforeach
                                            </x-adminlte-select>
                                            <x-adminlte-button type="submit" class="btn btn-warning" label="Actualizar" />
                                        </form>
                                        <x-slot name="footerSlot">
                                            <x-adminlte-button class="btn-flat" data-dismiss="modal" icon="fas fa-times"
                                                label="Cancelar" />
                                        </x-slot>
                                    </x-adminlte-modal>
                                @endrole
                            @else
                                <x-adminlte-button class="btn-flat" icon="fas fa-edit" label="Editar" data-toggle="modal"
                                    data-target="#editUserModal" />
                                <x-adminlte-modal id="editUserModal" title="Editar Usuario" size="lg" theme="warning"
                                    icon="fas fa-edit" v-centered>
                                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <x-adminlte-select name="roles[]" label="Rol" multiple>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ $user->roles->contains($role->id) ? 'selected' : '' }}>
                                                    {{ $role->name }}</option>
                                            @endforeach
                                        </x-adminlte-select>
                                        <x-adminlte-select name="permissions[]" label="Permisos" multiple>
                                            @foreach ($permissions as $permission)
                                                <option value="{{ $permission->id }}"
                                                    {{ $user->permissions->contains($permission->id) ? 'selected' : '' }}>
                                                    {{ $permission->name }} </option>
                                            @endforeach
                                        </x-adminlte-select>
                                        <x-adminlte-button type="submit" class="btn btn-warning" label="Actualizar" />
                                    </form>
                                    <x-slot name="footerSlot">
                                        <x-adminlte-button class="btn-flat" data-dismiss="modal" icon="fas fa-times"
                                            label="Cancelar" />
                                    </x-slot>
                                </x-adminlte-modal>
                            @endif
                        @endcan
                    </div>
                </x-slot>
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <img src="{{ $user->adminlte_image() }}" class="img-circle elevation-2" alt="User avatar"
                        style="height: 200px; object-fit: contain">
                </div>
                <x-adminlte-input name="name" label="Nombre" value="{{ $user->employee->full_name }}" readonly />
                <x-adminlte-input name="email" label="Email" value="{{ $user->employee->email }}" readonly />
                <x-adminlte-input name="document_number" label="Documento" value="{{ $user->employee->document_number }}"
                    readonly />

                <form action="{{ route('users.resume', $user) }}" method="GET">
                    <x-adminlte-button type="submit" class="btn btn-flat" label="Ver hoja de vida"
                        icon="fas fa-file-pdf" />
                </form>


            </x-adminlte-card>
        </div>
    </div>
@stop
