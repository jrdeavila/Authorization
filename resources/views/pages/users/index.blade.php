@extends('layouts.app')

@section('content_header')
    <h1>Listado de Usuarios</h1>
@endsection

@section('content')
    <div class="py-4">

        <div class="row justify-content-center">
            <div class="col-md-12">
                @session('success')
                    <blockquote class="quote quote-success">
                        <h5>Genial! 👍</h5>
                        <p>{{ session('success') }}</p>
                    </blockquote>
                @endsession
                @session('error')
                    <blockquote class="quote quote-danger">
                        <h5>Ups! Algo salió mal. 🙁</h5>
                        <p>{{ session('error') }}</p>
                    </blockquote>
                @endsession
                @if ($errors->any())
                    <blockquote class="quote quote-danger">
                        <h5>Ups! Algo salió mal. 🙁</h5>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </blockquote>
                @endif
            </div>

            <div class="col-md-8 row-md-2 position-relative">

                <div>
                    <x-adminlte-card theme="light" title="Usuarios" icon="fas fa-users">
                        <x-slot name="toolsSlot">
                            <form class="form-inline" action="{{ route('users.index') }}" method="GET">
                                <input type="hidden" name="user_id" value="{{ $currentUser?->id }}">
                                <x-adminlte-input name="search" placeholder="Buscar..." class="mr-2" />
                                <x-adminlte-button class="btn-flat" type="submit" icon="fas fa-search" />
                            </form>
                        </x-slot>
                        @php
                            $heads = ['ID', 'Nombre', 'Email', 'Documento', ''];
                            $config = [
                                'data' => $users,
                                'order' => [[0, 'asc']],
                                'columns' => [['data' => 'id'], ['data' => 'name'], ['data' => 'email']],
                            ];
                        @endphp
                        <x-adminlte-datatable id="usersTable" :heads="$heads" :config="$config">
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->employee->id }}</td>
                                    <td>
                                        <div class="row">
                                            <img src="{{ $user->employee->curriculum->photo }}" alt="avatar"
                                                class="img-circle mr-2 mt-1 border border-3 border-{{ $user->employee->status ? 'success' : 'danger' }} "
                                                style="width: 40px; height: 40px;">
                                            <div class="col">
                                                <div>
                                                    {{ $user->employee->full_name }}
                                                </div>
                                                <div class="text-muted text-sm">
                                                    {{ $user->employee->job->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->employee->email }}</td>
                                    <td>{{ $user->employee->document_number }}</td>
                                    <td>
                                        <a href="{{ route('users.index', ['user_id' => $user->id]) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="5">
                                    {{ $users->appends(request()->query())->links('custom.pagination') }}
                                </td>
                            </tr>
                        </x-adminlte-datatable>
                    </x-adminlte-card>
                </div>
            </div>
            <div class="col-md-4 row">
                @if (isset($currentUser))
                    <div class="col-md-12">
                        <x-adminlte-card theme="light" title="Información del Usuario" icon="fas fa-user">
                            <x-slot name="toolsSlot">
                                <div class="row">
                                    <form action="{{ route('users.index') }}" method="GET">
                                        @csrf
                                        <x-adminlte-button type="submit" class="btn-flat" icon="fas fa-times"
                                            label="Cerrar" />
                                    </form>
                                    @can('users-update')
                                        @if ($currentUser->hasRole('superadmin'))
                                            @role('superadmin')
                                                <x-adminlte-button class="btn-flat" icon="fas fa-edit" label="Editar"
                                                    data-toggle="modal" data-target="#editUserModal" />
                                                <x-adminlte-modal id="editUserModal" title="Editar Usuario" size="lg"
                                                    theme="warning" icon="fas fa-edit" v-centered>
                                                    <form action="{{ route('users.update', $currentUser->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <x-adminlte-select name="roles[]" label="Rol" multiple>
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->id }}"
                                                                    {{ $currentUser->roles->contains($role->id) ? 'selected' : '' }}>
                                                                    {{ $role->name }}</option>
                                                            @endforeach
                                                        </x-adminlte-select>
                                                        <x-adminlte-select name="permissions[]" label="Permisos" multiple>
                                                            @foreach ($permissions as $permission)
                                                                <option value="{{ $permission->id }}"
                                                                    {{ $currentUser->permissions->contains($permission->id) ? 'selected' : '' }}>
                                                                    {{ $permission->name }} </option>
                                                            @endforeach
                                                        </x-adminlte-select>
                                                        <x-adminlte-button type="submit" class="btn btn-warning"
                                                            label="Actualizar" />
                                                    </form>
                                                    <x-slot name="footerSlot">
                                                        <x-adminlte-button class="btn-flat" data-dismiss="modal" icon="fas fa-times"
                                                            label="Cancelar" />
                                                    </x-slot>
                                                </x-adminlte-modal>
                                            @endrole
                                        @else
                                            <x-adminlte-button class="btn-flat" icon="fas fa-edit" label="Editar"
                                                data-toggle="modal" data-target="#editUserModal" />
                                            <x-adminlte-modal id="editUserModal" title="Editar Usuario" size="lg"
                                                theme="warning" icon="fas fa-edit" v-centered>
                                                <form action="{{ route('users.update', $currentUser->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <x-adminlte-select name="roles[]" label="Rol" multiple>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}"
                                                                {{ $currentUser->roles->contains($role->id) ? 'selected' : '' }}>
                                                                {{ $role->name }}</option>
                                                        @endforeach
                                                    </x-adminlte-select>
                                                    <x-adminlte-select name="permissions[]" label="Permisos" multiple>
                                                        @foreach ($permissions as $permission)
                                                            <option value="{{ $permission->id }}"
                                                                {{ $currentUser->permissions->contains($permission->id) ? 'selected' : '' }}>
                                                                {{ $permission->name }} </option>
                                                        @endforeach
                                                    </x-adminlte-select>
                                                    <x-adminlte-button type="submit" class="btn btn-warning"
                                                        label="Actualizar" />
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
                            <x-adminlte-input name="name" label="Nombre"
                                value="{{ $currentUser->employee->full_name }}" readonly />
                            <x-adminlte-input name="email" label="Email" value="{{ $currentUser->employee->email }}"
                                readonly />
                            <x-adminlte-input name="document_number" label="Documento"
                                value="{{ $currentUser->employee->document_number }}" readonly />

                            <form action="{{ route('users.resume', $currentUser) }}" method="GET">
                                <x-adminlte-button type="submit" class="btn btn-flat" label="Ver hoja de vida"
                                    icon="fas fa-file-pdf" />
                            </form>


                        </x-adminlte-card>
                    </div>
                @endif
                @if (isset($currentUser) && $currentUser->roles->count() > 0)
                    <div class="col-md-12">
                        <x-adminlte-card theme="light" title="Roles" icon="fas fa-lock">
                            @php
                                $heads = ['ID', 'Nombre', 'Guard Name', ''];
                                $config = [
                                    'data' => $currentUser->roles,
                                    'order' => [[0, 'asc']],
                                    'columns' => [['data' => 'id'], ['data' => 'name'], ['data' => 'guard_name']],
                                ];
                            @endphp
                            <x-adminlte-datatable id="rolesTable" :heads="$heads" :config="$config">
                                @foreach ($currentUser->roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->guard_name }}</td>
                                    </tr>
                                @endforeach
                            </x-adminlte-datatable>
                        </x-adminlte-card>
                    </div>
                @endif
                @if (isset($currentUser) && $currentUser->permissions->count() > 0)
                    <div class="col-md-12">
                        <x-adminlte-card theme="light" title="Permisos" icon="fas fa-lock">
                            @php
                                $heads = ['ID', 'Nombre', 'Guard Name', ''];
                                $config = [
                                    'data' => $currentUser->permissions,
                                    'order' => [[0, 'asc']],
                                    'columns' => [['data' => 'id'], ['data' => 'name'], ['data' => 'guard_name']],
                                ];
                            @endphp
                            <x-adminlte-datatable id="rolesTable" :heads="$heads" :config="$config">
                                @foreach ($currentUser->permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->id }}</td>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->guard_name }}</td>
                                    </tr>
                                @endforeach
                            </x-adminlte-datatable>
                        </x-adminlte-card>
                    </div>
                @endif
            </div>

        </div>

    </div>
@endsection
