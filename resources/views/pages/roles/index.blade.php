@extends('layouts.app')

@section('title', 'Roles')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <x-adminlte-card theme="light" title="Roles" icon="fas fa-lock">
                    <x-slot name="toolsSlot">
                        @can('roles-create')
                            <x-adminlte-button class="btn-flat" icon="fas fa-plus" label="Crear Rol" data-toggle="modal"
                                data-target="#createRoleModal" />
                            <x-adminlte-modal id="createRoleModal" title="Crear Rol" size="lg" theme="primary"
                                icon="fas fa-plus" v-centered>
                                <form action="{{ route('roles.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Nombre del Rol</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="guard_name">Guard Name</label>
                                        <input type="text" class="form-control" id="guard_name" name="guard_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="permissions">Permisos</label>
                                        <select class="form-control select2" id="permissions" name="permissions[]"
                                            multiple="multiple" data-placeholder="Seleccione permisos">
                                            @foreach ($permissions as $permission)
                                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <x-adminlte-button type="submit" class="btn btn-success" label="Crear Rol" />
                                </form>
                            </x-adminlte-modal>
                        @endcan

                    </x-slot>

                    @php
                        $heads = ['ID', 'Nombre', 'Guard Name', ''];
                        $config = [
                            'data' => $roles,
                            'order' => [[0, 'asc']],
                            'columns' => [['data' => 'id'], ['data' => 'name'], ['data' => 'guard_name']],
                        ];
                    @endphp

                    <x-adminlte-datatable id="rolesTable" :heads="$heads" :config="$config">
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->guard_name }}</td>
                                <td>
                                    <a href="{{ route('roles.index', ['role_id' => $role->id]) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
            @if (isset($currentRole))
                <div class="col-md-4">
                    <x-adminlte-card theme="light" title="Permisos del Rol: {{ $currentRole->name }}" icon="fas fa-lock">
                        <x-slot name="toolsSlot">
                            @can('roles-edit')
                                <x-adminlte-button class="btn-flat" icon="fas fa-edit" label="Editar Rol" data-toggle="modal"
                                    data-target="#editRoleModal" />
                                <x-adminlte-modal id="editRoleModal" title="Editar Rol: {{ $currentRole->name }}" size="lg"
                                    theme="warning" icon="fas fa-edit" v-centered>
                                    <form action="{{ route('roles.update', $currentRole->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label for="name">Nombre del Rol</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ $currentRole->name }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="guard_name">Guard Name</label>
                                            <input type="text" class="form-control" id="guard_name" name="guard_name"
                                                value="{{ $currentRole->guard_name }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="permissions">Permisos</label>
                                            <select class="form-control select2" id="permissions" name="permissions[]"
                                                multiple="multiple" data-placeholder="Seleccione permisos">
                                                @foreach ($permissions as $permission)
                                                    <option value="{{ $permission->id }}"
                                                        {{ $currentRole->permissions->contains($permission->id) ? 'selected' : '' }}>
                                                        {{ $permission->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <x-adminlte-button type="submit" class="btn btn-warning" label="Actualizar Rol" />
                                    </form>
                                </x-adminlte-modal>
                            @endcan
                        </x-slot>
                        <ul class="list-group">
                            @php
                                $rolePermissions = $currentRole->permissions()->paginate(5);
                            @endphp
                            @foreach ($rolePermissions as $permission)
                                <li class="list-group-item">
                                    {{ $permission->name }}

                                </li>
                            @endforeach
                            <hr>
                            {{ $rolePermissions->appends(request()->query())->links('custom.pagination') }}
                        </ul>
                    </x-adminlte-card>


                </div>
            @endif
        </div>
    </div>
@endsection
