@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
    <div class="container-fluid py-4">

        {{-- ALERTAS --}}
        <div class="row justify-content-center">
            <div class="col-md-11">
                @if (session('success'))
                    <div class="custom-alert success animated pulse">
                        <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="alert-text"><strong>¡Completado!</strong> {{ session('success') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="custom-alert danger animated shake">
                        <div class="alert-icon"><i class="fas fa-exclamation-circle"></i></div>
                        <div class="alert-text">
                            <strong>Verifica los datos:</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row justify-content-center">
            {{-- LISTADO DE USUARIOS --}}
            <div class="{{ isset($currentUser) ? 'col-md-7' : 'col-md-11' }} transition-all">
                <div class="modern-card">
                    <div class="card-header-gradient">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0 font-weight-bold text-white"><i class="fas fa-users-cog mr-2"></i> Usuarios
                                </h4>
                                <p class="text-white-50 small mb-0">Gestiona accesos y perfiles del personal</p>
                            </div>
                            <form action="{{ route('users.index') }}" method="GET" class="search-bar">
                                <input type="hidden" name="user_id" value="{{ $currentUser?->id }}">
                                <input type="text" name="search" placeholder="Buscar..."
                                    value="{{ request('search') }}">
                                <button type="submit"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 custom-table">
                                <thead>
                                    <tr>
                                        <th class="pl-4">ID</th>
                                        <th>Colaborador</th>
                                        <th>Contacto</th>
                                        <th class="text-right pr-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr
                                            class="{{ isset($currentUser) && $currentUser->id == $user->id ? 'active-row' : '' }}">
                                            <td class="pl-4 align-middle">
                                                <span class="badge badge-light shadow-sm">#{{ $user->employee->id }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-wrapper mr-3">
                                                        <img src="{{ $user->employee->curriculum->photo }}"
                                                            class="img-fluid rounded-circle border-{{ $user->employee->status ? 'success' : 'danger' }}">
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold mb-0 text-dark">
                                                            {{ $user->employee->full_name }}</div>
                                                        <div class="text-muted x-small text-uppercase">
                                                            {{ $user->employee->job->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-muted small">
                                                <div><i class="far fa-envelope mr-1"></i> {{ $user->employee->email }}
                                                </div>
                                            </td>
                                            <td class="text-right pr-4 align-middle">
                                                <a href="{{ route('users.index', ['user_id' => $user->id, 'page' => request('page')]) }}"
                                                    class="btn-action-view {{ isset($currentUser) && $currentUser->id == $user->id ? 'active' : '' }}">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- PAGINACIÓN CORREGIDA --}}
                    <div class="card-footer bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="small text-muted">
                                Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} de {{ $users->total() }}
                                registros
                            </div>
                            <div>
                                {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PANEL DE DETALLES --}}
            @if ($currentUser)

                <div class="col-md-5 animated fadeInRight">
                    <div class="modern-card sticky-top" style="top: 20px;">
                        <div
                            class="card-header border-0 bg-white d-flex justify-content-between align-items-center pt-4 px-4">
                            <h5 class="font-weight-bold mb-0">Perfil de Usuario</h5>
                            <a href="{{ route('users.index', ['page' => request('page')]) }}"
                                class="close-btn text-muted"><i class="fas fa-times"></i></a>
                        </div>

                        <div class="card-body px-4">
                            <div class="user-detail-box mb-4 text-center p-3 bg-light rounded-lg">
                                <img src="{{ $currentUser->employee->curriculum->photo }}"
                                    class="img-thumbnail rounded-circle mb-3 shadow-sm"
                                    style="width: 100px; height: 100px; object-fit: cover;">
                                <h5 class="font-weight-bold mb-1">{{ $currentUser->employee->full_name }}</h5>
                                <p class="badge badge-primary px-3 rounded-pill text-uppercase small">
                                    {{ $currentUser->employee->job->name }}
                                </p>
                            </div>
                            <div class="mt-4">

    <div class="mb-3">
        <small class="text-muted font-weight-bold">Correo</small>
        <div>{{ $currentUser->employee->email ?? 'No registrado' }}</div>
    </div>

    <div class="mb-3">
        <small class="text-muted font-weight-bold">Teléfono</small>
        <div>{{ $currentUser->employee->phone ?? 'No registrado' }}</div>
    </div>

    <div class="mb-3">
        <small class="text-muted font-weight-bold">Documento</small>
        <div>{{ $currentUser->employee->document ?? 'No registrado' }}</div>
    </div>

    <div class="mb-3">
        <small class="text-muted font-weight-bold">Estado</small>
        <div>
            @if($currentUser->employee->status)
                <span class="badge badge-success">Activo</span>
            @else
                <span class="badge badge-danger">Inactivo</span>
            @endif
        </div>
    </div>

</div>


                            <div class="info-row">
                                <label><i class="fas fa-shield-alt mr-1"></i> Roles Asignados</label>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    {{-- CAMBIO CRÍTICO AQUÍ: Usamos getRoleNames() para forzar la lectura de Spatie --}}
                                    @forelse ($currentUser->getRoleNames() as $roleName)
                                        <span class="custom-badge role-badge mb-2 mr-2">{{ $roleName }}</span>
                                    @empty
                                        <span class="text-muted x-small italic">Sin roles asignados</span>
                                    @endforelse
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-grid gap-2">
                                @can('users-update')
                                    <button type="button"
                                        class="btn btn-warning btn-block rounded-pill font-weight-bold shadow-sm"
                                        onclick="openEditModal(
    {{ $currentUser->id }},
    '{{ $currentUser->employee->full_name }}',
    @json($currentUser->roles->pluck('id')),
    @json($currentUser->permissions->pluck('id'))
)"
>
                                        <i class="fas fa-user-shield mr-2"></i> Editar Roles y Permisos
                                    </button>
                                @endcan

                                <a href="{{ route('users.resume', $currentUser) }}"
                                    class="btn btn-outline-danger btn-block rounded-pill font-weight-bold mt-2">
                                    <i class="fas fa-file-pdf mr-2"></i> Ver Hoja de Vida
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FORMULARIO OCULTO PARA SWEETALERT --}}
                <form id="updateForm" action="{{ route('users.update', $currentUser->id) }}" method="POST"
                    style="display:none;">
                    @csrf @method('PUT')
                    <select name="roles[]" id="hiddenRoles" multiple></select>
                    <select name="permissions[]" id="hiddenPermissions" multiple></select>
                </form>
            @endif
        </div>
    </div>
@endsection


@push('css')
<style>

/* ======================================================
   VARIABLES
====================================================== */
:root {
    --main-gradient: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
}

/* ======================================================
   BASE
====================================================== */
body {
    background-color: #f4f7f6;
}

/* ======================================================
   CARDS
====================================================== */
.modern-card {
    background: #fff;
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 2rem;
}

.card-header-gradient {
    background: var(--main-gradient);
    padding: 25px;
    color: #fff;
}

/* ======================================================
   TABLE
====================================================== */
.custom-table thead th {
    background: #f8fafc;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 1px;
    color: #64748b;
    border: none;
}

.active-row {
    background-color: rgba(59, 130, 246, 0.05) !important;
    border-left: 5px solid #3b82f6 !important;
}

/* ======================================================
   AVATAR
====================================================== */
.avatar-wrapper img {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border: 2px solid #fff;
    border-radius: 50%;
}

/* ======================================================
   BOTONES
====================================================== */
.btn-action-view {
    width: 35px;
    height: 35px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    color: #64748b;
}

.btn-action-view.active {
    background: #3b82f6;
    color: #fff;
}

/* ======================================================
   SEARCH BAR TABLA
====================================================== */
.search-bar {
    display: flex;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 5px 15px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.search-bar input,
.search-bar button {
    background: transparent;
    border: none;
    color: #fff;
    outline: none;
}

/* ======================================================
   BADGES
====================================================== */
.custom-badge {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
}

.role-badge {
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #dbeafe;
}

/* ======================================================
   ALERTAS
====================================================== */
.custom-alert {
    display: flex;
    align-items: center;
    padding: 15px;
    border-radius: 15px;
    margin-bottom: 20px;
}

.custom-alert.success {
    background: #dcfce7;
    color: #15803d;
    border-left: 6px solid #22c55e;
}

.custom-alert.danger {
    background: #fee2e2;
    color: #b91c1c;
    border-left: 6px solid #ef4444;
}

/* ======================================================
   PAGINACIÓN
====================================================== */
.page-item.active .page-link {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

/* ======================================================
   SWEETALERT - POPUP GENERAL
====================================================== */
.swal-modern-popup {
    width: 900px !important;
    max-height: 85vh !important; /* Ajustado para mejor visualización */
    border-radius: 20px !important;
    padding: 0 !important;
    display: flex !important;
    flex-direction: column !important;
}

.swal2-html-container {
    margin: 0 !important;
    padding: 0 !important;
    overflow-y: auto !important; /* MEJORA 2: Permite el scroll en la ventana */
}

/* Estilo de la barra de scroll para que se vea moderna */
.swal2-html-container::-webkit-scrollbar {
    width: 8px;
}
.swal2-html-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

/* ======================================================
   SWEETALERT - HEADER CON BUSCADOR INTEGRADO
====================================================== */

.swal-header-fancy {
    background: var(--main-gradient) !important;
    padding: 20px 25px !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    border-radius: 20px 20px 0 0 !important;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.swal-header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}
.swal-text-group {
    display: flex;
    flex-direction: column;
    text-align: left;
}

.swal-icon-circle {
    width: 30px;
    height: 30px;
    background: rgba(255,255,255,0.2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.swal-icon-circle i {
    font-size: 13px;
    color: white;
}

.swal-tag {
    font-size: 10px !important;
    font-weight: 800;
    letter-spacing: 1px;
    color: rgba(255,255,255,0.8) !important;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.swal-title-main {
    font-size: 1.1rem !important;
    font-weight: 700 !important;
    color: white !important;
    margin: 0 !important;
}

.swal-header-right {
    display: flex;
    align-items: center;
}

.swal-search-wrapper-header {
    position: relative;
}

.swal-search-wrapper-header i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: white !important; /* MEJORA 1: Icono blanco */
    font-size: 13px;
    z-index: 10;
}

.swal-search-wrapper-header input {
    background: rgba(255, 255, 255, 0.15) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    padding: 7px 12px 7px 35px !important;
    border-radius: 10px !important;
    color: white !important;
    width: 200px !important;
    outline: none !important;
}

/* MEJORA 1: Frase "Buscar permisos..." en blanco */
.swal-search-wrapper-header input::placeholder {
    color: rgba(255, 255, 255, 0.75) !important;
    opacity: 1;
}

/* ======================================================
   SWEETALERT - BODY
====================================================== */
.swal-body-custom {
    padding: 20px !important;
    background: #f8fafc;
}
.swal-box {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 20px;
}

.swal-box-header {
    background: #f1f5f9;
    padding: 10px 15px;
    font-size: 11px;
    font-weight: 800;
    color: #475569;
    border-bottom: 1px solid #e2e8f0;
    text-transform: uppercase;
}

/* ======================================================
   GRID (MEJORA 3: Organización de permisos)
====================================================== */
/* ======================================================
   GRID (MEJORA 3: Organización de permisos)
====================================================== */
/* ======================================================
   PERMISOS INDIVIDUALES EN FILAS (CORREGIDO)
====================================================== */
.swal-scroll-area {
    display: flex !important;
    flex-wrap: wrap !important; /* Permite que bajen a la siguiente línea */
    gap: 12px !important;
    padding: 15px;
}

/* Cada permiso ocupa espacio horizontal */
.swal-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Tamaño fijo elegante */
.swal-label-card {
    display: flex !important;
    align-items: center !important;
    gap: 8px;
}

/* ======================================================
   CHECKBOX CARDS
====================================================== */
.swal-item input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    width: 18px;
    height: 18px;
    border: 2px solid #94a3b8;
    border-radius: 5px;
    cursor: pointer;
    position: relative;
    background: transparent;
    transition: all 0.2s ease;
    margin: 0;
}
.swal-item input[type="checkbox"]:hover {
    border-color: #3b82f6;
}

.swal-label-card {
    display: flex !important;
    align-items: center !important;
    padding: 12px 15px !important; /* Ajuste leve para mejor organización */
    background: #fff !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 12px !important;
    font-size: 13px !important; /* Un poco más pequeño para que quepan mejor */
    font-weight: 600 !important;
    color: #334155 !important;
    transition: all 0.2s;
    cursor: pointer;
    height: 100%;
    margin-bottom: 0 !important;
}

.swal-label-card i {
    font-size: 16px !important;
    margin-right: 10px;
    color: #94a3b8;
}

.swal-item input[type="checkbox"]:checked {
    border-color: #3b82f6;
    background: transparent;
}
/* ======================================================
   BOTONES
====================================================== */
.swal-confirm-btn,
.swal-cancel-btn {
    padding: 8px 25px !important;
    font-size: 13px !important;
    border-radius: 8px !important;
}

/* Limpieza de contenedor SweetAlert */
.swal-modern-popup .swal2-html-container {
    margin: 0 !important;
    padding: 0 !important;
}
.swal-item input[type="checkbox"]:checked::after {
    content: "✔";
    font-size: 13px;
    font-weight: bold;
    color: #3b82f6;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -58%);
}

</style>
@endpush


@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
  function openEditModal(userId, userName, userRoles = [], userPerms = []) {

    Swal.fire({
        title: '',
        html: `
        <div class="swal-header-fancy">
            <div class="swal-header-left">
                <div class="swal-icon-circle">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="swal-text-group">
                    <div class="swal-tag">SEGURIDAD</div>
                    <h4 class="swal-title-main">${userName}</h4>
                </div>
            </div>
            <div class="swal-header-right">
                <div class="swal-search-wrapper-header">
                    <i class="fas fa-search"></i>
                    <input type="text" id="permSearch" placeholder="Buscar permisos...">
                </div>
            </div>
        </div>

        <div class="swal-body-custom text-left">
            <div class="swal-box">
                <div class="swal-box-header">ROLES ASIGNADOS</div>
                <div class="row no-gutters p-2">
                    @foreach ($roles as $role)
                        <div class="col-6 p-1">
                            <div class="swal-item">
                                <input type="checkbox"
                                    name="swal-roles"
                                    id="role-{{ $role->id }}"
                                    value="{{ $role->id }}"
                                    ${userRoles.map(String).includes("{{ $role->id }}") ? 'checked' : ''}
>
                                <label for="role-{{ $role->id }}" class="swal-label-card">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span>{{ $role->name }}</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="swal-box">
                <div class="swal-box-header">PERMISOS INDIVIDUALES</div>
                <div class="swal-scroll-area">
                    @foreach ($permissions as $permission)
                        <div class="swal-item perm-item-wrapper">
                            <input type="checkbox"
                                name="swal-perms"
                                id="perm-{{ $permission->id }}"
                                value="{{ $permission->id }}"
                                ${userPerms.map(String).includes("{{ $permission->id }}") ? 'checked' : ''}>
                            <label for="perm-{{ $permission->id }}" class="swal-label-card">
                                <i class="fas fa-key mr-2"></i>
                                <span>{{ $permission->name }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Guardar Cambios',
        cancelButtonText: 'Cerrar',
        confirmButtonColor: '#3b82f6',
        customClass: {
            popup: 'swal-modern-popup',
            confirmButton: 'swal-confirm-btn',
            cancelButton: 'swal-cancel-btn'
        },
        didOpen: () => {
            const searchInput = document.getElementById('permSearch');
            searchInput.addEventListener('input', function(e) {
                const search = e.target.value.toLowerCase();
                document.querySelectorAll('.perm-item-wrapper').forEach(item => {
                    item.style.display =
                        item.innerText.toLowerCase().includes(search) ? 'flex' : 'none';
                });
            });
        },
        preConfirm: () => {

            const selectedRoles = Array.from(
                document.querySelectorAll('input[name="swal-roles"]:checked')
            ).map(cb => cb.value);

            const selectedPerms = Array.from(
                document.querySelectorAll('input[name="swal-perms"]:checked')
            ).map(cb => cb.value);

            const hiddenRoles = document.getElementById('hiddenRoles');
            const hiddenPerms = document.getElementById('hiddenPermissions');

            hiddenRoles.innerHTML = '';
            hiddenPerms.innerHTML = '';

            selectedRoles.forEach(v =>
                hiddenRoles.innerHTML += `<option value="${v}" selected></option>`
            );

            selectedPerms.forEach(v =>
                hiddenPerms.innerHTML += `<option value="${v}" selected></option>`
            );

            document.getElementById('updateForm').submit();
        }
    });
}

    </script>
@endpush
