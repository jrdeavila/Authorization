{{--
    Layout base para el módulo de permisos.
    Vistas hijas: @extends('permissions.layouts.app')
    Usar @section('module_content') en lugar de @section('content')
--}}
@extends('adminlte::page')

@push('css')
<style>
/* ==============================
   RESET & BASE
   ============================== */
* { -webkit-tap-highlight-color: transparent; }
[x-cloak] { display: none !important; }

/* Fondo limpio */
.content-wrapper,
.content,
.container-fluid {
    background-color: #ffffff !important;
}

/* ==============================
   ANIMATIONS
   ============================== */
.content-wrapper .container-fluid {
    animation: fadeInUp 0.4s ease-out;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Cards */
.card {
    border-radius: 18px !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
    border: none !important;
    transition: box-shadow 0.3s ease, transform 0.2s ease;
}
.card:hover {
    box-shadow: 0 12px 35px rgba(0,0,0,0.12) !important;
}

/* Table rows */
.table-hover tbody tr {
    transition: background-color 0.2s ease, transform 0.15s ease;
}
.table-hover tbody tr:hover {
    transform: translateX(3px);
}

/* Buttons */
.btn {
    transition: all 0.2s ease;
}
.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Badges */
.badge {
    transition: transform 0.2s ease;
}
.badge:hover {
    transform: scale(1.1);
}

/* Modals */
.modal.fade.show {
    animation: fadeIn 0.2s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Alerts */
.alert {
    animation: slideInRight 0.3s ease-out;
}
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}

/* ==============================
   RESPONSIVE — Mobile & Tablet
   ============================== */
@media (max-width: 991.98px) {
    /* Ocultar sidebar */
    .main-sidebar,
    .sidebar-mini .main-sidebar {
        display: none !important;
    }
    .content-wrapper,
    .main-footer {
        margin-left: 0 !important;
    }
    body.sidebar-open .content-wrapper {
        transform: none !important;
    }
    /* Ocultar controles de navbar desktop */
    .navbar .nav-item .nav-link[data-widget="pushmenu"],
    .navbar .nav-item .nav-link[data-widget="navbar-search"],
    .navbar .nav-item .nav-link[data-widget="fullscreen"],
    .navbar .nav-item.dropdown.user-menu,
    .main-header .navbar-nav.ml-auto {
        display: none !important;
    }
    /* Espacio para bottom bar */
    body {
        overflow-x: hidden;
        padding-bottom: 66px !important;
    }
    .content-wrapper {
        padding-bottom: 70px !important;
    }
    /* Card headers wrap */
    .card-header.d-flex,
    .card-header .d-flex {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    /* Form footers stack */
    .card-footer.d-flex {
        flex-direction: column;
        gap: 0.5rem;
    }
    .card-footer .btn {
        width: 100%;
    }
    /* Paginación */
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
        gap: 2px;
    }
    .pagination .page-link {
        padding: 6px 10px;
        font-size: 0.8rem;
        min-width: 34px;
        text-align: center;
    }
    .card-footer {
        overflow-x: auto;
    }
    .card-footer > .d-flex {
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }
    .card-footer .small.text-muted {
        text-align: center;
    }
}

/* ==============================
   DARK MODE
   ============================== */
body.dark-mode .content-wrapper,
body.dark-mode .content,
body.dark-mode .container-fluid {
    background-color: #0f172a !important;
}

body.dark-mode .card {
    background-color: #1e293b !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important;
}

body.dark-mode .card-header {
    border-bottom-color: #334155 !important;
}

body.dark-mode .card-footer {
    border-top-color: #334155 !important;
    background-color: #1e293b !important;
}

body.dark-mode .table {
    color: #e2e8f0;
}

body.dark-mode .table-hover tbody tr:hover {
    background-color: rgba(255,255,255,0.05) !important;
}

body.dark-mode .thead-light th {
    background-color: #334155 !important;
    color: #cbd5e1 !important;
    border-color: #475569 !important;
}

body.dark-mode .table td,
body.dark-mode .table th {
    border-color: #334155 !important;
}

body.dark-mode .form-control {
    background-color: #334155;
    border-color: #475569;
    color: #e2e8f0;
}

body.dark-mode .form-control:focus {
    background-color: #1e293b;
    border-color: #3b82f6;
    color: #f1f5f9;
}

body.dark-mode .input-group-text {
    background-color: #334155;
    border-color: #475569;
    color: #94a3b8;
}

body.dark-mode .text-dark,
body.dark-mode h1, body.dark-mode h2, body.dark-mode h3,
body.dark-mode h4, body.dark-mode h5 {
    color: #f1f5f9 !important;
}

body.dark-mode .text-muted {
    color: #94a3b8 !important;
}

body.dark-mode .breadcrumb {
    background-color: transparent;
}

body.dark-mode .breadcrumb-item a {
    color: #60a5fa;
}

body.dark-mode .breadcrumb-item.active {
    color: #94a3b8;
}

body.dark-mode .alert-success {
    background-color: #064e3b;
    border-color: #065f46;
    color: #6ee7b7;
}

body.dark-mode .alert-danger {
    background-color: #7f1d1d;
    border-color: #991b1b;
    color: #fca5a5;
}

body.dark-mode .badge-light {
    background-color: #334155;
    color: #e2e8f0;
}

body.dark-mode .badge-info {
    background-color: #1e40af;
}

body.dark-mode .modal-content {
    background-color: #1e293b;
    color: #e2e8f0;
}

body.dark-mode .pagination .page-link {
    background-color: #1e293b;
    border-color: #334155;
    color: #e2e8f0;
}

body.dark-mode .pagination .page-item.active .page-link {
    background-color: #1d4ed8;
    border-color: #1d4ed8;
}

body.dark-mode code {
    color: #f472b6;
    background-color: #334155;
}

body.dark-mode .custom-control-label {
    color: #e2e8f0;
}

body.dark-mode .content-header {
    background-color: #0f172a !important;
}

/* Toggle button */
.dark-mode-toggle {
    position: fixed;
    bottom: 80px;
    right: 16px;
    z-index: 1039;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: none;
    background: #1e293b;
    color: #fbbf24;
    font-size: 1.1rem;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s, transform 0.2s, color 0.3s;
}

.dark-mode-toggle:hover {
    transform: scale(1.1);
}

body.dark-mode .dark-mode-toggle {
    background: #fbbf24;
    color: #1e293b;
}

@media (min-width: 992px) {
    .dark-mode-toggle {
        bottom: 20px;
    }
}

/* Navbar superior dark */
body.dark-mode .main-header,
body.dark-mode .main-header.navbar {
    background: #1e293b !important;
    border-bottom-color: #334155 !important;
    color: #e2e8f0 !important;
}
body.dark-mode .main-header .nav-link,
body.dark-mode .main-header .navbar-nav .nav-link,
body.dark-mode .navbar-light .navbar-nav .nav-link,
body.dark-mode .main-header .navbar-nav .nav-item .nav-link {
    color: #e2e8f0 !important;
}
body.dark-mode .main-header .nav-link:hover,
body.dark-mode .main-header .navbar-nav .nav-link:hover {
    color: #fff !important;
}
body.dark-mode .main-header .navbar-brand,
body.dark-mode .navbar-light .navbar-brand {
    color: #e2e8f0 !important;
}
/* Buscador del navbar dark */
body.dark-mode .navbar-search-block,
body.dark-mode .navbar-search-block .form-control {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #e2e8f0 !important;
}
body.dark-mode .navbar-search-block .btn {
    color: #94a3b8 !important;
}
/* User menu dropdown dark */
body.dark-mode .dropdown-menu {
    background: #1e293b;
    border-color: #334155;
}
body.dark-mode .dropdown-menu .dropdown-item {
    color: #e2e8f0;
}
body.dark-mode .dropdown-menu .dropdown-item:hover {
    background: #334155;
    color: #fff;
}
body.dark-mode .dropdown-menu .dropdown-divider {
    border-top-color: #334155;
}

/* Sidebar dark mode */
body.dark-mode .main-sidebar {
    background-color: #0f172a !important;
}
body.dark-mode .main-sidebar .brand-link {
    background-color: #0f172a !important;
    border-bottom-color: #1e293b !important;
    color: #e2e8f0 !important;
}
body.dark-mode .main-sidebar .nav-sidebar .nav-link {
    color: #94a3b8 !important;
}
body.dark-mode .main-sidebar .nav-sidebar .nav-link:hover,
body.dark-mode .main-sidebar .nav-sidebar .nav-link.active {
    background-color: #1e293b !important;
    color: #e2e8f0 !important;
}
body.dark-mode .main-sidebar .nav-header {
    color: #64748b !important;
}
body.dark-mode .sidebar {
    background: transparent !important;
}
/* Buscador sidebar dark */
body.dark-mode .sidebar .form-control-sidebar,
body.dark-mode .sidebar-search-results,
body.dark-mode .sidebar .input-group .form-control {
    background-color: #1e293b !important;
    border-color: #334155 !important;
    color: #e2e8f0 !important;
}
body.dark-mode .sidebar .input-group .form-control::placeholder {
    color: #64748b !important;
}
body.dark-mode .sidebar .input-group-append .btn,
body.dark-mode .sidebar .input-group .input-group-text {
    background-color: #334155 !important;
    border-color: #334155 !important;
    color: #94a3b8 !important;
}
body.dark-mode .sidebar .sidebar-search-results .list-group-item {
    background-color: #1e293b !important;
    border-color: #334155 !important;
    color: #e2e8f0 !important;
}

/* Sidebar light mode (forzar claro) */
body:not(.dark-mode) .main-sidebar {
    background-color: #fff !important;
}
body:not(.dark-mode) .main-sidebar .brand-link {
    background-color: #fff !important;
    border-bottom: 1px solid #f1f5f9 !important;
    color: #1e293b !important;
}
body:not(.dark-mode) .main-sidebar .nav-sidebar .nav-link {
    color: #475569 !important;
}
body:not(.dark-mode) .main-sidebar .nav-sidebar .nav-link:hover {
    background-color: #f1f5f9 !important;
    color: #1d4ed8 !important;
}
body:not(.dark-mode) .main-sidebar .nav-sidebar .nav-link.active {
    background-color: #eef2ff !important;
    color: #1d4ed8 !important;
}
body:not(.dark-mode) .main-sidebar .nav-sidebar .nav-link.active .nav-icon {
    color: #1d4ed8 !important;
}
body:not(.dark-mode) .main-sidebar .nav-header {
    color: #94a3b8 !important;
}
body:not(.dark-mode) .main-sidebar .nav-sidebar .nav-icon {
    color: #94a3b8 !important;
}
body:not(.dark-mode) .sidebar {
    background: transparent !important;
}
/* Buscador sidebar light */
body:not(.dark-mode) .sidebar .form-control-sidebar,
body:not(.dark-mode) .sidebar .input-group .form-control {
    background-color: #f8fafc !important;
    border-color: #e2e8f0 !important;
    color: #1e293b !important;
}
body:not(.dark-mode) .sidebar .input-group .form-control::placeholder {
    color: #94a3b8 !important;
}
body:not(.dark-mode) .sidebar .input-group-append .btn,
body:not(.dark-mode) .sidebar .input-group .input-group-text {
    background-color: #f1f5f9 !important;
    border-color: #e2e8f0 !important;
    color: #64748b !important;
}

/* Footer */
body.dark-mode .main-footer {
    background-color: #1e293b !important;
    border-top-color: #334155 !important;
    color: #94a3b8 !important;
}
</style>
@endpush

@section('content')
    @yield('module_content')

    {{-- Dark mode toggle --}}
    <button class="dark-mode-toggle" onclick="toggleDarkMode()" title="Cambiar modo oscuro/claro">
        <i class="fas fa-moon" id="dark-mode-icon"></i>
    </button>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('dark-mode', isDark ? '1' : '0');
            document.getElementById('dark-mode-icon').className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        }
        // Restaurar preferencia al cargar
        (function() {
            if (localStorage.getItem('dark-mode') === '1') {
                document.body.classList.add('dark-mode');
                var icon = document.getElementById('dark-mode-icon');
                if (icon) icon.className = 'fas fa-sun';
            }
        })();
    </script>

    @auth
        @include('permissions.partials.bottom-appbar')
    @endauth
@stop
