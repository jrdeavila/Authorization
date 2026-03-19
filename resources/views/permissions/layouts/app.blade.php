{{--
    Layout base para el módulo de permisos.
    Vistas hijas: @extends('permissions.layouts.app')
    Usar @section('module_content') en lugar de @section('content')
--}}
@extends('adminlte::page')

@push('css')
<style>
[x-cloak] { display: none !important; }

/* Page fade-in */
.content-wrapper .container-fluid {
    animation: fadeInUp 0.4s ease-out;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Card hover */
.card {
    transition: box-shadow 0.3s ease, transform 0.2s ease;
}
.card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

/* Table row hover */
.table-hover tbody tr {
    transition: background-color 0.2s ease, transform 0.15s ease;
}
.table-hover tbody tr:hover {
    transform: translateX(3px);
}

/* Button hover */
.btn {
    transition: all 0.2s ease;
}
.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Badge hover */
.badge {
    transition: transform 0.2s ease;
}
.badge:hover {
    transform: scale(1.1);
}

/* Modal animation */
.modal.fade.show {
    animation: fadeIn 0.2s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Alert slide-in */
.alert {
    animation: slideInRight 0.3s ease-out;
}
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}

/* ==============================
   RESPONSIVE — Mobile
   ============================== */
@media (max-width: 767.98px) {
    .main-sidebar {
        display: none !important;
    }
    .content-wrapper,
    .main-footer {
        margin-left: 0 !important;
    }
    .navbar .nav-item .nav-link[data-widget="pushmenu"],
    .navbar .nav-item .nav-link[data-widget="navbar-search"],
    .navbar .nav-item .nav-link[data-widget="fullscreen"],
    .navbar .nav-item.dropdown.user-menu {
        display: none !important;
    }
    .content-wrapper {
        padding-bottom: 70px !important;
    }
    .card-header.d-flex,
    .card-header .d-flex {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .card-footer.d-flex {
        flex-direction: column;
        gap: 0.5rem;
    }
    .card-footer .btn {
        width: 100%;
    }
    /* Paginación responsive */
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
    .card-footer .d-flex {
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }
    .card-footer .small.text-muted {
        text-align: center;
    }
}
</style>
@endpush

@section('content')
    @yield('module_content')

    @auth
        @include('permissions.partials.bottom-appbar')
    @endauth
@stop
