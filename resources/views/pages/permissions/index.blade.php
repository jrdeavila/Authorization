@extends('layouts.app')

@php

@endphp

@section('content')
    <div class="row justify-content-center py-4">
        <div class="col-md-11">

            {{-- 1. ENCABEZADO PREMIUM --}}
            <div class="header-premium mb-4">
                <div class="header-premium-content">
                    <div class="header-info">
                        <div class="header-icon-container">
                            <i class="fas fa-shield-alt text-white"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="header-title">Gestión de Permisos</h1>
                            <p class="header-subtitle">Niveles de seguridad y control de acceso granular</p>
                        </div>
                    </div>

                    <div class="header-actions d-flex align-items-center">
                        {{-- Buscador Premium --}}
                        <div class="search-container-premium mr-3">
                            <input type="text" id="customSearch" placeholder="Buscar permiso...">
                            <i class="fas fa-search"></i>
                        </div>

                        {{-- Botón Nuevo Integrado --}}
                        @can('permissions-create')
                            <button class="btn btn-nuevo-premium" onclick="window.appData.onCreate()">
                                <i class="fas fa-plus"></i> NUEVO PERMISO
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- 2. TABLA DE DATOS --}}
            <div x-data="app()" id="app-container">
                <div class="table-responsive px-2">
                    @php
                        $heads = [
                            ['label' => 'ID', 'width' => 5],
                            'Permiso / Guard',
                            ['label' => 'Guard Name', 'width' => 15],
                            ['label' => 'Acción', 'width' => 15],
                        ];
                        $config = [
                            'order' => [[0, 'desc']],
                            'language' => ['url' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'],
                            'dom' => 'rt', // Solo mostramos la tabla (t), el buscador es el nuestro
                        ];
                    @endphp

                    <x-adminlte-datatable id="permissionsTable" :heads="$heads" :config="$config" borderless animated>
                        @foreach ($permissions as $permission)
                            <tr class="table-row-card">
                                <td class="align-middle">
                                    <span class="badge badge-light px-2 py-1 text-muted"
                                        style="border-radius: 8px;">#{{ $permission->id }}</span>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold text-dark"
                                            style="font-size: 1rem; letter-spacing: 0.5px;">{{ strtoupper($permission->name) }}</span>
                                        <span class="text-muted small">Acceso nivel: {{ $permission->guard_name }}</span>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="badge-guard-pill d-inline-flex align-items-center">
                                        <i class="fas fa-fingerprint mr-2 text-primary"
                                            style="font-size: 0.9rem; opacity: 0.7;"></i>
                                        <span class="font-weight-bold text-muted"
                                            style="font-size: 0.75rem;">{{ strtoupper($permission->guard_name) }}</span>
                                    </div>
                                </td>
                                <td class="align-middle text-right">
                                    @can('permissions-delete')
                                        <button type="button" class="btn-eliminar-permiso"
                                            x-on:click="onDelete('{{ route('permissions.destroy', $permission->id) }}', '{{ $permission->name }}')">
                                            <i class="fas fa-trash-alt mr-1"></i> ELIMINAR
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </div>

                {{-- Paginación --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $permissions->links('custom.pagination') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        /* --- ENCABEZADO PREMIUM --- */
        .header-premium {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 25px;
            padding: 25px 35px;
            box-shadow: 0 10px 30px rgba(30, 64, 175, 0.2);
            color: white;
        }

        .header-premium-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-icon-container {
            background: rgba(255, 255, 255, 0.2);
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            backdrop-filter: blur(5px);
        }

        .header-title {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .header-subtitle {
            font-size: 0.9rem;
            margin: 5px 0 0 0;
            opacity: 0.8;
        }

        /* --- BUSCADOR PREMIUM --- */
        .search-container-premium {
            position: relative;
            width: 280px;
        }

        .search-container-premium input {
            width: 100%;
            background: rgba(255, 255, 255, 0.15) !important; /* Fondo semi-transparente */
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 15px;
            padding: 10px 15px 10px 40px;
            color: white !important; /* Texto escrito en blanco */
            outline: none;
            transition: all 0.3s ease;
        }

        /* Efecto al hacer clic en el buscador */
        .search-container-premium input:focus {
            background: rgba(255, 255, 255, 0.25) !important;
            border-color: rgba(255, 255, 255, 0.6) !important;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.1);
        }

        /* Placeholder blanco */
        .search-container-premium input::placeholder { color: rgba(255, 255, 255, 0.8) !important; }
        .search-container-premium input::-webkit-input-placeholder { color: rgba(255, 255, 255, 0.8) !important; }

        .search-container-premium i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: white !important;
            opacity: 0.9;
            z-index: 5;
        }

        /* --- BOTÓN NUEVO --- */
        .btn-nuevo-premium {
            background: white;
            color: #1e40af;
            border: none;
            padding: 10px 25px;
            border-radius: 15px;
            font-weight: 800;
            font-size: 0.85rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .btn-nuevo-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            color: #2563eb;
        }

        /* --- TABLA CARDS --- */
        #permissionsTable {
            border-collapse: separate !important;
            border-spacing: 0 12px !important;
            width: 100% !important;
        }

        .table-row-card {
            background-color: #f8fbff !important;
            transition: 0.3s;
        }

        .table-row-card td {
            border: none !important;
            padding: 15px 20px !important;
        }

        .table-row-card td:first-child { border-radius: 18px 0 0 18px !important; }
        .table-row-card td:last-child { border-radius: 0 18px 18px 0 !important; }

        .table-row-card:hover {
            transform: scale(1.005);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05) !important;
            background-color: #ffffff !important;
        }

        .btn-eliminar-permiso {
            color: #ef4444;
            font-weight: 800;
            font-size: 0.8rem;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-eliminar-permiso:hover {
            color: #b91c1c;
            transform: scale(1.1);
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Definimos la lógica de Alpine en un objeto global para acceder desde el botón del header
        function app() {
            const logic = {
                onCreate() {
                    Swal.fire({
                        title: '<span style="font-weight:900; color: #1e293b; font-size: 1.3rem;">NUEVO PERMISO</span>',
                        html: `
                            <form id="permissionForm" action="{{ route('permissions.store') }}" method="POST" class="px-3 text-left">
                                @csrf
                                <div class="mb-4 text-left">
                                    <label class="small font-weight-bold text-muted mb-2 d-block">NOMBRE DEL PERMISO</label>
                                    <input type="text" name="name" class="form-control border-radius-10" style="border-radius:12px; padding:12px;" placeholder="ej: reportes.view" required>
                                </div>
                                <div class="mb-2 text-left">
                                    <label class="small font-weight-bold text-muted mb-2 d-block">GUARD NAME</label>
                                    <select name="guard_name" class="form-control" style="border-radius:12px; height:auto; padding:10px;" required>
                                        <option value="web" selected>WEB</option>
                                        <option value="api">API</option>
                                    </select>
                                </div>
                            </form>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'GUARDAR',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-primary rounded-pill px-5 py-2 mx-2 shadow font-weight-bold',
                            cancelButton: 'btn btn-light rounded-pill px-4 py-2 mx-2 border text-muted'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.showLoading();
                            document.getElementById('permissionForm').submit();
                        }
                    });
                },
                onDelete(url, name) {
                    Swal.fire({
                        title: '<span style="font-weight:900; color: #ef4444;">¿ELIMINAR PERMISO?</span>',
                        text: `Esta acción no se puede deshacer para "${name.toUpperCase()}"`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'SÍ, ELIMINAR',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-danger rounded-pill px-5 py-2 mx-2 shadow font-weight-bold',
                            cancelButton: 'btn btn-light rounded-pill px-4 py-2 mx-2 border'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.action = url;
                            form.method = 'POST';
                            form.innerHTML =
                                `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }
            };
            window.appData = logic; // Exponer a nivel global
            return logic;
        }

        // --- SUSTITUYE SOLO ESTA PARTE EN TU JS ---

        $(document).ready(function() {
            var table = $('#permissionsTable').DataTable();

            $('#customSearch').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Notificaciones inteligentes
            // --- NOTIFICACIONES LIMPIAS Y EFECTIVAS ---

            // 1. Mensajes de Éxito (Crear / Actualizar) -> VERDE
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 4000,
                    background: '#dcfce7',
                    color: '#166534'
                });
            @endif

            // 2. Mensajes de Borrado -> ROJO
            @if (session('deleted'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: "{{ session('deleted') }}",
                    showConfirmButton: false,
                    timer: 4000,
                    background: '#fee2e2',
                    color: '#991b1b'
                });
            @endif

            // 3. Mensajes de Error de Sistema -> ROJO
            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 5000,
                    background: '#fff4e5',
                    color: '#663c00'
                });
            @endif
        }); // Fin de document.ready
    </script>
@endpush
