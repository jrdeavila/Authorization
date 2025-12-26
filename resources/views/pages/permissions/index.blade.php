@extends('layouts.app')

@php

@endphp

@section('content')
    <div class="row justify-content-center py-3">
        <div class="col-md-12">
            @foreach (['error', 'success'] as $msg)
                @if (session()->has($msg))
                    <div class="alert alert-{{ $msg == 'error' ? 'danger' : $msg }} alert-dismissible fade show"
                        role="alert">
                        {{ session($msg) }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            @endforeach
        </div>
        <div x-data="app()" class="col-md-8">

            <x-adminlte-card theme="light" title="Permisos" icon="fas fa-lock">
                <x-slot name="toolsSlot">
                    @can('permissions-create')
                        <x-adminlte-button theme="primary" icon="fas fa-plus" label="Crear Permiso" x-on:click="onCreate" />
                    @endcan
                </x-slot>

                @php
                    $heads = ['ID', 'Nombre', 'Guard Name'];

                    $config = [
                        'order' => [[0, 'asc']],
                        'buttons' => [
                            ['extend' => 'colvis', 'text' => '<i class="fas fa-eye"></i> Columnas'],
                            ['extend' => 'copy', 'text' => '<i class="fas fa-copy"></i> Copiar'],
                            ['extend' => 'excel', 'text' => '<i class="fas fa-file-excel"></i> Excel'],
                            ['extend' => 'pdf', 'text' => '<i class="fas fa-file-pdf"></i> PDF'],
                            ['extend' => 'print', 'text' => '<i class="fas fa-print"></i> Imprimir'],
                        ],
                    ];
                @endphp

                <x-adminlte-datatable id="permissionsTable" :heads="$heads" :config="$config" striped hoverable
                    with-buttons>
                    @foreach ($permissions as $permission)
                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>{{ $permission->guard_name }}</td>
                        </tr>
                    @endforeach
                    <tr class="text-center">
                        <td colspan="5">
                            {{ $permissions->links('custom.pagination') }}
                        </td>

                    </tr>
                </x-adminlte-datatable>

            </x-adminlte-card>

        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}" defer></script>

    <script>
        function app() {
            return {
                onCreate() {
                    let sweetalert = Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger'
                        },
                        buttonsStyling: false
                    })
                    // Se necesita el nombre y guard name
                    sweetalert.fire({
                        title: 'Crear Permiso',
                        html: `
                            <form id="permissionForm" class="form-horizontal" method="POST" action="{{ route('permissions.store') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Nombre del Permiso</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="guard_name">Guard Name</label>
                                    <input type="text" class="form-control" id="guard_name" name="guard_name" required>
                                </div>
                            </form>
                        `,
                        showCloseButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Crear',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('permissionForm').submit();
                        }
                    })
                }
            }
        }
    </script>
@endpush
