@extends('layouts.app')

@section('content')
    <div class="row justify-content-center py-3">
        <div class="col-md-12">
            @session('success')
                <blockquote class="quote quote-success">
                    <h5>Genial! 👍<h5>

                            <p>{{ session('success') }}</p>
                </blockquote>
            @endsession
        </div>
        <div class="col-md-8">
            <x-adminlte-card theme="light" title="Permisos" icon="fas fa-lock">
                <x-slot name="toolsSlot">
                    @can('permissions-create')
                        <x-adminlte-button class="btn-flat" icon="fas fa-plus" label="Crear Permiso" data-toggle="modal"
                            data-target="#createPermissionModal" />
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

                <x-adminlte-modal id="createPermissionModal" title="Crear Permiso" theme="primary" icon="fas fa-plus"
                    size="lg" scrollable>
                    <form action="{{ route('permissions.store') }}" method="POST">
                        @csrf
                        <x-adminlte-input name="name" label="Nombre del Permiso" required
                            placeholder="Ingrese el nombre del permiso" />

                        <x-adminlte-input name="guard_name" label="Guard Name" value="web" required
                            placeholder="Ingrese el guard name" />
                        <x-slot name="footerSlot">
                            <x-adminlte-button class="btn-flat" type="submit" form="createPermissionForm"
                                icon="fas fa-save" label="Guardar" />
                            <x-adminlte-button class="btn-flat" data-dismiss="modal" icon="fas fa-times" label="Cancelar" />
                            <x-adminlte-button class="btn-flat" type="reset" form="createPermissionForm"
                                icon="fas fa-undo" label="Limpiar" />
                    </form>
                    </x-slot>
                </x-adminlte-modal>


            </x-adminlte-card>

        </div>
    </div>
@endsection

@push('js')
    <script>
        // Todos los botones que sean tipo submit
        $(document).load(function() {
            $('button[type="submit"]').on('click', function() {
                // Hacer submit al form padre
                $(this).closest('form').submit();
            });

        });
    </script>
@endpush
