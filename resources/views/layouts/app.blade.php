@extends('adminlte::page')

@push('css')
    {{-- Dependencias Críticas --}}
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.css') }}">

    <style>
        :root {
            --brand-blue: #007BFF;
            --brand-gradient: linear-gradient(135deg, #007BFF 0%, #00c6ff 100%);
            --bg-main: #ffffff;
        }

        /* RESET DE FONDO ADMINLTE */
        .content-wrapper,
        .content,
        .container-fluid {
            background-color: var(--bg-main) !important;
            border: none !important;
        }

        /* CONTENEDOR PRINCIPAL */
        .screen-wrapper {
            background: var(--bg-main);
            min-height: calc(100vh - 60px);
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* MARCA DE AGUA (Logo de fondo) */
        .screen-logo {
            position: fixed;
            top: 55%;
            left: 58%;
            transform: translate(-50%, -50%);
            width: 450px;
            opacity: 0.05;
            z-index: 0;
            pointer-events: none;
            filter: grayscale(100%);
            transition: all 0.5s ease;
        }

        /* CAPA DE CONTENIDO */
        .screen-content {
            position: relative;
            z-index: 1;
            animation: fadeIn 0.8s ease-out;
        }

        /* TARJETAS ESTILIZADAS */
        .card, .adminlte-card {
            border-radius: 20px !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05) !important;
            border: 1px solid rgba(0, 123, 255, 0.08) !important;
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(5px);
            transition: transform 0.3s ease;
        }

        /* --- ESTILO PARA LA IMAGEN DE BIENVENIDA (Para impresionar) --- */
        .welcome-image-container {
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 123, 255, 0.15);
            border: 1px solid rgba(0, 123, 255, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: white;
            display: inline-block;
            max-width: 100%;
        }

        .welcome-image-container:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 45px rgba(0, 123, 255, 0.25);
        }

        .welcome-image-container img {
            display: block;
            width: 100%;
            height: auto;
        }

        /* BOTÓN FLOTANTE */
        .btn-change-voice {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--brand-gradient);
            color: white;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0, 123, 255, 0.3);
            opacity: 0.8;
            z-index: 999;
            transition: all 0.4s ease;
            border: none;
        }

        .btn-change-voice:hover {
            opacity: 1;
            transform: scale(1.1) rotate(10deg);
        }

        /* TOASTS */
        .colored-toast {
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
            border-radius: 12px !important;
        }
        .colored-toast.swal2-icon-success { background-color: #28a745 !important; }
        .colored-toast.swal2-icon-error { background-color: #dc3545 !important; }
        .colored-toast.swal2-icon-info { background-color: var(--brand-blue) !important; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Datatables */
        .dataTables_wrapper .btn-secondary {
            background: #f8fafc !important;
            color: var(--brand-blue) !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
        }
    </style>
@endpush

@section('content')
    <div class="screen-wrapper">
        {{-- Logo de fondo sutil --}}
        <img src="{{ asset('img/logo.png') }}" alt="Watermark" class="screen-logo">

        <div class="screen-content">
            {{-- Aquí se inyectará el contenido de tus vistas (Roles, Usuarios, o Bienvenida) --}}
            @yield('content_body')
        </div>

        {{-- Botón de soporte flotante --}}
        <button class="btn-change-voice shadow-lg" title="Soporte Técnico">
            <i class="fas fa-headset fa-lg"></i>
        </button>
    </div>
@endsection

@push('js')
    <script src="{{ asset('vendor/jquery/jquery.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('build/app2.js') }}"></script>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: { popup: 'colored-toast' }
        });
    </script>
@endpush
