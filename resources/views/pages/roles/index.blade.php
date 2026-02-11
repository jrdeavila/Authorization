@extends('layouts.app')

@section('title', 'Gestión de Roles')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">

            {{-- TABLA DE ROLES PRINCIPAL --}}
            <div class="{{ isset($currentRole) ? 'col-md-7' : 'col-md-11' }} transition-all">
                <div class="card-premium shadow-lg border-0">

                    <div class="card-premium-header-modern">
                        <div class="header-left">
                            <div class="icon-box-white">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="header-text">
                                <h2 class="main-title-white">Gestión de Roles</h2>
                                <p class="sub-title-white">Niveles de seguridad y control de acceso</p>
                            </div>
                        </div>

                        <div class="header-right-actions">
                            <div class="search-pill-container">
                                <i class="fas fa-search search-icon-inner"></i>
                                <input type="text" placeholder="Buscar rol..." class="search-input-inner" id="customSearchInput">

                                @can('roles-create')
                                    <button type="button" class="btn-pill-white btn-create-role">
                                        <i class="fas fa-plus"></i>
                                        <span>NUEVO ROL</span>
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="card-premium-body">
                        @php
                            $heads = [
                                ['label' => 'ID', 'width' => 10, 'class' => 'text-center'],
                                ['label' => 'Nombre', 'width' => 40],
                                ['label' => 'Guard', 'width' => 25, 'class' => 'text-center'],
                                ['label' => 'Acciones', 'width' => 25, 'class' => 'text-right'],
                            ];

                            $config = [
                                'language' => ['url' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'],
                                'autoWidth' => false,
                                'responsive' => true,
                                'paging' => true,
                                'searching' => true,
                                'info' => true,
                                'dom' => 't', // 't' oculta el buscador y controles originales de DataTable para usar los tuyos
                            ];
                        @endphp

                        <x-adminlte-datatable id="rolesTable" :heads="$heads" :config="$config" hoverable compressed theme="none">
                            @foreach ($roles as $role)
                                <tr class="modern-tr {{ isset($currentRole) && $currentRole->id == $role->id ? 'tr-active' : '' }}">
                                    <td class="text-center">
                                        <span class="modern-id">#{{ $role->id }}</span>
                                    </td>
                                    <td>
                                        <div class="role-info d-flex flex-column">
                                            <span class="role-name font-weight-bold">{{ $role->name }}</span>
                                            <span class="role-desc text-xs text-muted">Protección activa</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="guard-pill mx-auto">
                                            <i class="fas fa-fingerprint"></i>
                                            <span>{{ strtoupper($role->guard_name) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('roles.index', ['role_id' => $role->id]) }}" class="btn-action-view">
                                            <span>Ver Permisos</span>
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </x-adminlte-datatable>
                    </div>

                    <div class="card-footer-modern border-top">
                        <div class="d-flex justify-content-between align-items-center w-100 px-4 py-3">
                            <span class="text-muted small">
                                Mostrando <strong>{{ $roles->firstItem() }}</strong> a
                                <strong>{{ $roles->lastItem() }}</strong> de <strong>{{ $roles->total() }}</strong> registros
                            </span>

                            <div class="custom-pagination-wrapper">
                                {{ $roles->links('custom.pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- VISTA LATERAL DE PERMISOS --}}
            @if (isset($currentRole))
                <div class="col-md-5 animated-fade-in-right">
                    <div class="card-premium h-100 shadow-lg border-0">

                        <div class="card-premium-header-modern" style="margin: 15px; padding: 10px 20px;">
                            <div class="header-left">
                                <div class="icon-box-white" style="width: 40px; height: 40px;">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="header-text">
                                    <h2 class="main-title-white" style="font-size: 1.1rem;">{{ $currentRole->name }}</h2>
                                    <p class="sub-title-white">
                                        <span class="badge badge-light text-primary" style="border-radius: 50px; font-size: 0.7rem;">
                                            {{ $currentRole->permissions->count() }}
                                        </span> Permisos Asignados
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card-premium-body mt-2">
                            <div class="d-flex justify-content-end mb-4" style="gap: 8px;">
                                @can('roles-edit')
                                    <button class="btn-modern-edit btn-edit-role"
                                        data-id="{{ $currentRole->id }}"
                                        data-name="{{ $currentRole->name }}"
                                        data-guard="{{ $currentRole->guard_name }}"
                                        data-permissions="{{ $currentRole->permissions->pluck('id') }}">
                                        <i class="fas fa-edit"></i> <span>EDITAR</span>
                                    </button>
                                @endcan

                                @can('roles-delete')
                                    <button type="button" class="btn-modern-delete" onclick="confirmDelete()">
                                        <i class="fas fa-trash-alt"></i> <span>ELIMINAR</span>
                                    </button>

                                    {{-- FORMULARIO OCULTO PARA ELIMINAR --}}
                                    <form id="form-delete-role" action="{{ route('roles.destroy', $currentRole->id) }}" method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endcan
                            </div>

                            <hr class="my-3" style="opacity: 0.1;">

                            <div class="permissions-grid mt-3">
                                @forelse($currentRole->permissions as $perm)
                                    <div class="permission-pill-modern shadow-sm">
                                        <div class="pill-dot"></div>
                                        <span class="pill-text">{{ $perm->name }}</span>
                                    </div>
                                @empty
                                    <div class="empty-state text-center py-5 w-100">
                                        <div class="empty-icon mb-3" style="font-size: 3rem; color: #e2e8f0;">
                                            <i class="fas fa-lock-open"></i>
                                        </div>
                                        <h5 class="text-muted">Sin Permisos</h5>
                                        <p class="small text-muted">Este rol no tiene privilegios asignados todavía.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@push('js')
<script>
    $(document).ready(function() {
        // Conexión del Buscador personalizado con la Tabla
        const table = $('#rolesTable').DataTable();
        $('#customSearchInput').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

    // Función para confirmación de eliminación con SweetAlert2
    function confirmDelete() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminará el rol de forma permanente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-delete-role').submit();
            }
        });
    }
</script>
@endpush
@push('css')
    <style>
        :root {
            --brand-blue: #007BFF;
            --brand-gradient: linear-gradient(90deg, #007BFF, #00c6ff);
            --text-main: #1e293b;
            --text-sub: #64748b;
        }

        /* Corrección de alineación de tabla */
        #rolesTable thead th {
            padding: 15px !important;
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            border-bottom: 2px solid #f1f5f9 !important;
            vertical-align: middle;
        }

        .modern-tr td {
            padding: 15px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        /* Estilos de elementos internos */
        .card-premium {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid #ebeef2;
        }

        .card-premium-accent {
            height: 5px;
            background: var(--brand-gradient);
        }

        .card-premium-accent-secondary {
            height: 5px;
            background: linear-gradient(90deg, #00c6ff, #007BFF);
        }

        .card-premium-header {
            position: relative;
            z-index: 20 !important;
            /* Aumentamos para asegurar que esté arriba */
        }

        .icon-wrapper-modern {
            width: 45px;
            height: 45px;
            background: rgba(0, 123, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand-blue);
            font-size: 1.2rem;
            margin-right: 15px;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .main-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
        }

        .sub-title {
            font-size: 0.8rem;
            color: var(--text-sub);
            margin: 0;
        }

        .btn-modern-primary {
            background: var(--brand-blue);
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .guard-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #475569;
        }

        .btn-action-view {
            color: var(--brand-blue);
            font-weight: 700;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none !important;
        }

        .btn-modern-edit {
            background: #fff;
            color: var(--brand-blue);
            border: 2px solid var(--brand-blue);
            padding: 5px 15px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .btn-modern-delete {
            background: #fff;
            color: #dc3545;
            border: 2px solid #dc3545;
            padding: 5px 15px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .permission-pill-modern {
            background: #ffffff;
            border: 1px solid #edf2f7;
            padding: 8px 14px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
        }

        .pill-dot {
            width: 6px;
            height: 6px;
            background: var(--brand-blue);
            border-radius: 50%;
        }

        .pill-text {
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
        }

        .tr-active {
            background-color: #f0f7ff !important;
            border-left: 4px solid var(--brand-blue) !important;
        }


        /* El contenedor principal azul (Cápsula) */
        .card-premium-header-modern {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            padding: 12px 25px;
            border-radius: 50px;
            /* Forma de píldora */
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            margin: 20px;
            /* Margen para que respire dentro de la card */
            box-shadow: 0 8px 20px rgba(30, 64, 175, 0.2);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .icon-box-white {
            background: rgba(255, 255, 255, 0.2);
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .main-title-white {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: white;
        }

        .sub-title-white {
            margin: 0;
            opacity: 0.85;
            font-size: 0.8rem;
            color: white;
        }

        /* Buscador y Botón Integrados */
        .search-pill-container {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 5px 5px 5px 15px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            min-width: 380px;
        }

        .search-input-inner {
            background: transparent;
            border: none;
            color: white;
            outline: none;
            width: 100%;
            font-size: 0.85rem;
        }

        .search-input-inner::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-icon-inner {
            margin-right: 10px;
            opacity: 0.9;
        }

        /* Botón Blanco NUEVO */
        .btn-pill-white {
            background: white;
            color: #1e40af;
            border: none;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            white-space: nowrap;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-pill-white:hover {
            background: #f8fafc;
            transform: scale(1.03);
        }

        /* Ocultar buscador original de DataTables */
    </style>
@endpush


@push('css')
    <style>
        :root {
            --brand-blue: #007BFF;
            --brand-dark-blue: #0056b3;
            --brand-gradient: linear-gradient(90deg, #007BFF, #00c6ff);
            --text-main: #1e293b;
            --text-sub: #64748b;
        }

        /* --- DATATABLES CUSTOM --- */
        .dataTables_length label,
        .dataTables_filter label {
            font-weight: 700 !important;
            color: var(--brand-blue) !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
        }

        .dataTables_length select,
        .dataTables_filter input {
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 5px 12px !important;
            outline: none !important;
        }

        /* --- CARDS --- */
        .card-premium {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.06);
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-premium-accent {
            height: 6px;
            background: var(--brand-gradient);
            width: 100%;
        }

        .card-premium-accent-secondary {
            height: 6px;
            background: linear-gradient(90deg, #00c6ff, #007BFF);
            width: 100%;
        }

        .card-premium-header {
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .icon-wrapper-modern {
            width: 50px;
            height: 50px;
            background: rgba(0, 123, 255, 0.1);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand-blue);
            font-size: 1.4rem;
        }

        .main-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
        }

        .sub-title {
            font-size: 0.85rem;
            color: var(--text-sub);
            margin: 0;
        }

        /* --- BOTONES --- */
        .btn-modern-primary {
            background: var(--brand-blue);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 8px 15px rgba(0, 123, 255, 0.2);
            transition: 0.3s;
        }

        .btn-modern-edit {
            background: white;
            color: var(--brand-blue);
            border: 2px solid var(--brand-blue);
            padding: 6px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* --- TABLA --- */
        .card-premium-body {
            padding: 0 25px 25px 25px;
        }

        .modern-tr td {
            vertical-align: middle !important;
            border: none !important;
            padding: 16px !important;
        }

        .tr-active {
            background: rgba(0, 123, 255, 0.04) !important;
            border-left: 4px solid var(--brand-blue) !important;
        }

        .modern-id {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
        }

        .role-name {
            font-weight: 700;
            color: var(--text-main);
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .guard-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.7rem;
            color: #64748b;
            font-weight: bold;
        }

        .btn-action-view {
            color: var(--brand-blue);
            font-weight: 700;
            font-size: 0.8rem;
            text-decoration: none !important;
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: flex-end;
        }

        /* --- PERMISOS MODAL --- */
        .permissions-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .permission-pill-modern {
            background: white;
            border: 1px solid #eef2f6;
            padding: 6px 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pill-dot {
            width: 6px;
            height: 6px;
            background: var(--brand-blue);
            border-radius: 50%;
        }

        .pill-text {
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
        }

        .permissions-container-scroll {
            max-height: 350px;
            overflow-y: auto;
            padding: 15px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .custom-checkbox-item {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 10px;
            border-radius: 10px;
            transition: 0.2s;
            background: white;
            border: 1px solid #f1f5f9;
            height: 100%;
        }

        .custom-checkbox-item:hover {
            background: rgba(0, 123, 255, 0.05);
            border-color: var(--brand-blue);
        }

        .custom-checkbox-item input {
            display: none;
        }

        .checkbox-box {
            width: 20px;
            height: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            background: white;
            flex-shrink: 0;
        }

        .custom-checkbox-item input:checked+.checkbox-box {
            background: #28a745;
            border-color: #28a745;
        }

        .checkbox-box i {
            color: white;
            font-size: 10px;
            display: none;
        }

        .custom-checkbox-item input:checked+.checkbox-box i {
            display: block;
        }

        .animated-fade-in-right {
            animation: fadeInRight 0.5s ease-out;
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-control-pro {
            border-radius: 12px;
            height: 45px;
            padding-left: 40px !important;
        }

        .label-pro {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
            display: block;
        }

        .btn-confirm-pro {
            background: var(--brand-blue) !important;
            border-radius: 12px !important;
            padding: 10px 25px !important;
            font-weight: 700 !important;
        }

        .dataTables_wrapper {
            position: relative;
            z-index: 1;
        }

        .dataTables_wrapper * {
            pointer-events: auto;
        }

        .card-premium-header {
            position: relative;
            z-index: 10;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Evitar doble carga
            if (window.rolesJSLoaded) return;
            window.rolesJSLoaded = true;

            const azulLogo = '#007BFF';
            const permissions = @json($permissions);

            /* ==========================================================
               NOTIFICACIONES AUTOMÁTICAS (Success / Error)
            ============================================================= */
            // ... dentro de tu script en el Blade ...

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });

            // 1. Mensaje para CREAR / EDITAR (Verde)
            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: '{{ session('success') }}',
                    background: '#dcfce7',
                    color: '#166534',
                    iconColor: '#22c55e'
                });
            @endif

            // 2. Mensaje para ELIMINAR (Rojo) <--- ESTO ES LO QUE TE FALTA
            @if (session('deleted')) Toast.fire({
        icon: 'error',
        title: '{{ session('deleted') }}',
        background: '#fee2e2', // Rojo suave
        color: '#991b1b',      // Texto rojo oscuro
        iconColor: '#ef4444'
    }); @endif

            // 3. Mensaje para ERRORES (Rojo)
            @if (session('error')) Toast.fire({
        icon: 'error',
        title: '{{ session('error') }}',
        background: '#fee2e2',
        color: '#991b1b',
        iconColor: '#ef4444'
    }); @endif

            /* =======================
               GENERADOR DE PERMISOS
            ======================== */
            const getPermissionsHtml = (selectedIds = []) => {
                const selectedStrings = selectedIds.map(id => String(id));
                let html = `
                <div class="form-group mb-0 mt-4 text-left">
                    <label class="label-pro mb-2" style="font-weight:700; color:#64748b; text-transform:uppercase; font-size:0.75rem;">Asignar Privilegios</label>
                    <div style="max-height:250px; overflow-y:auto; padding:15px; border:1px solid #e2e8f0; border-radius:12px; background:#f8fafc;">
                        <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:12px;">`;

                permissions.forEach(perm => {
                    const isChecked = selectedStrings.includes(String(perm.id)) ? 'checked' : '';
                    html += `
                        <label style="display:flex; align-items:center; background:#fff; border:1px solid #cbd5e1; padding:10px; border-radius:10px; cursor:pointer; margin-bottom:0; transition: 0.2s shadow;">
                            <input type="checkbox" name="permissions[]" value="${perm.id}" ${isChecked}
                                style="min-width:20px; height:20px; accent-color:${azulLogo}; margin-right:12px;">
                            <span style="font-size:0.85rem; color:#334155; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                ${perm.name}
                            </span>
                        </label>`;
                });

                html += `</div></div></div>`;
                return html;
            };

            /* =======================
               MODAL BASE SWEETALERT
            ======================== */
            const openProModal = (title, formHtml, confirmText, icon) => {
                Swal.fire({
                    title: `
                        <div style="background:rgba(0,123,255,.1); width:60px; height:60px; border-radius:18px;
                                    display:flex; align-items:center; justify-content:center; margin:0 auto 15px;">
                            <i class="${icon}" style="color:${azulLogo}; font-size:1.8rem;"></i>
                        </div>
                        <span style="font-weight:800; color:#1e293b; font-size:1.4rem;">${title}</span>
                    `,
                    html: formHtml,
                    width: 700,
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Cancelar',
                    buttonsStyling: false,
                    allowOutsideClick: false,
                    customClass: {
                        confirmButton: 'btn btn-primary rounded-pill px-5 py-2 mx-2 shadow-sm',
                        cancelButton: 'btn btn-light rounded-pill px-5 py-2 mx-2 border',
                        popup: 'border-0 rounded-4 shadow-lg'
                    },
                    preConfirm: () => {
                        const form = Swal.getPopup().querySelector('form');
                        if (!form.checkValidity()) {
                            form.reportValidity();
                            return false;
                        }
                        return true;
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        const form = Swal.getPopup().querySelector('form');
                        if (form) form.submit();
                    }
                });
            };

            /* =======================
               DELEGACIÓN DE EVENTOS
            ======================== */
            document.addEventListener('click', (e) => {

                // 1. CREAR ROL
                const btnCreate = e.target.closest('.btn-create-role');
                if (btnCreate) {
                    const formHtml = `
                        <form action="{{ route('roles.store') }}" method="POST" class="px-2">
                            @csrf
                            <div class="row text-left">
                                <div class="col-md-6 mb-3">
                                    <label class="label-pro" style="display:block; margin-bottom:5px; font-weight:700; color:#64748b; font-size:0.75rem;">NOMBRE DEL ROL</label>
                                    <input type="text" name="name" class="form-control" placeholder="Ej: Supervisor" required style="border-radius:10px; height:45px;">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="label-pro" style="display:block; margin-bottom:5px; font-weight:700; color:#64748b; font-size:0.75rem;">GUARD (PLATAFORMA)</label>
                                    <select name="guard_name" class="form-control" required style="border-radius:10px; height:45px;">
                                        <option value="web" selected>web</option>
                                        <option value="api">api</option>
                                    </select>
                                </div>
                            </div>
                            ${getPermissionsHtml([])}
                        </form>`;
                    openProModal('NUEVO SISTEMA DE ROL', formHtml, 'Guardar Rol', 'fas fa-shield-alt');
                    return;
                }

                // 2. EDITAR ROL
                const btnEdit = e.target.closest('.btn-edit-role');
                if (btnEdit) {
                    const id = btnEdit.dataset.id;
                    const name = btnEdit.dataset.name;
                    const guard = btnEdit.dataset.guard;
                    let perms = [];
                    try {
                        perms = JSON.parse(btnEdit.dataset.permissions);
                    } catch (err) {
                        perms = [];
                    }

                    const formHtml = `
                        <form action="/roles/${id}" method="POST" class="px-2">
                            @csrf
                            @method('PUT')
                            <div class="row text-left">
                                <div class="col-md-6 mb-3">
                                    <label class="label-pro" style="display:block; margin-bottom:5px; font-weight:700; color:#64748b; font-size:0.75rem;">NOMBRE DEL ROL</label>
                                    <input type="text" name="name" class="form-control" value="${name}" required style="border-radius:10px; height:45px;">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="label-pro" style="display:block; margin-bottom:5px; font-weight:700; color:#64748b; font-size:0.75rem;">GUARD (PLATAFORMA)</label>
                                    <select name="guard_name" class="form-control" required style="border-radius:10px; height:45px;">
                                        <option value="web" ${guard === 'web' ? 'selected' : ''}>web</option>
                                        <option value="api" ${guard === 'api' ? 'selected' : ''}>api</option>
                                    </select>
                                </div>
                            </div>
                            ${getPermissionsHtml(perms)}
                        </form>`;
                    openProModal('ACTUALIZAR CREDENCIALES', formHtml, 'Guardar Cambios',
                        'fas fa-user-shield');
                    return;
                }

                // 3. ELIMINAR ROL
                const btnDelete = e.target.closest('#btnDeleteRole');
                if (btnDelete) {
                    Swal.fire({
                        title: '<span style="color:#e11d48; font-weight:800;">¿CONFIRMAR ELIMINACIÓN?</span>',
                        text: 'Esta acción es irreversible y podría afectar a los usuarios vinculados.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Eliminar Permanentemente',
                        cancelButtonText: 'Cancelar',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-danger rounded-pill px-4 py-2 mx-2 shadow-sm',
                            cancelButton: 'btn btn-light rounded-pill px-4 py-2 mx-2 border',
                            popup: 'border-0 rounded-4 shadow-lg'
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('formDeleteRole');
                            if (form) form.submit();
                        }
                    });
                }
            });
        });
        $(document).ready(function() {
            // Inicializamos la tabla (el ID en tu código es rolesTable)
            var table = $('#rolesTable').DataTable();

            // Conectamos tu input azul con la búsqueda
            $('#customSearchInput').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
        // Vincular el buscador personalizado con la tabla DataTables
        $('#customSearchInput').on('keyup', function() {
            $('#rolesTable').DataTable().search($(this).val()).draw();
        });
    </script>
@endpush
