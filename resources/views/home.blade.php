@extends('layouts.app')

@section('content_body')
    {{-- Dependencias --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <div class="container-fluid min-vh-100 py-4 dashboard-clean position-relative">
        <div class="bg-gradient-soft"></div>

        {{-- Header --}}
        <header class="row align-items-center mb-5 fade-in-up">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge-corporate">SISTEMA DE CONTROL</span>
                    <div class="header-line"></div>
                </div>
                <h1 class="main-title">Panel <span class="blue-accent">administrativo</span></h1>
                <p class="subtitle text-uppercase small fw-bold text-muted">Administración de seguridad y usuarios de la plataforma.</p>
            </div>
        </header>

        <div class="row g-4">
            {{-- Hero Card --}}
            <section class="col-12 fade-in-up" style="animation-delay: 0.1s">
                <div class="hero-card-white shadow-sm border-0">
                    <div class="row align-items-center g-0">
                        <div class="col-lg-8 p-4 p-md-5">
                            <h2 class="welcome-text mb-3">
                                Bienvenido, <span class="text-blue-main fw-bold d-inline-block text-nowrap">
                                    {{ Auth::user()->employee->full_name ?? Auth::user()->name }}
                                </span>
                            </h2>
                            <p class="hero-description text-muted mb-0">
                                Desde aquí puede configurar los niveles de acceso y supervisar las cuentas activas en el sistema. Seleccione un módulo para comenzar.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Módulos de Gestión --}}
            @php
                $modules = [
                    [
                        'title' => 'PERMISOS',
                        'icon' => 'bi-person-lock',
                        'status' => 'SEGURIDAD', // Badge útil
                        'desc' => 'Configure qué acciones específicas puede realizar cada perfil en el sistema.',
                        'link' => route('permissions.index'),
                        'color' => '#007bbd',
                        'action' => 'CONFIGURAR ACCESOS'
                    ],
                    [
                        'title' => 'ROLES',
                        'icon' => 'bi-node-plus-fill',
                        'status' => 'JERARQUÍA', // Badge útil
                        'desc' => 'Cree y edite grupos de funciones para asignar a los empleados.',
                        'link' => route('roles.index'),
                        'color' => '#003a70',
                        'action' => 'DEFINIR ROLES'
                    ],
                    [
                        'title' => 'USUARIOS',
                        'icon' => 'bi-person-gear',
                        'status' => 'DIRECTORIO', // Badge útil
                        'desc' => 'Gestión completa de cuentas, contraseñas y vinculación con empleados.',
                        'link' => route('users.index'),
                        'color' => '#0ea5e9',
                        'action' => 'ADMINISTRAR CUENTAS'
                    ],
                ];
            @endphp

            @foreach ($modules as $index => $module)
                <div class="col-xl-4 col-md-6 mb-4 fade-in-up" style="animation-delay: {{ 0.2 + $index * 0.1 }}s">
                    <a href="{{ $module['link'] }}" class="text-decoration-none h-100 d-block card-anchor">
                        <article class="corporate-card h-100 shadow-sm">
                            <div class="card-accent-line" style="background: {{ $module['color'] }}"></div>
                            <div class="card-body-content text-center p-4">

                                <div class="d-flex justify-content-end mb-2">
                                    {{-- Ahora el badge indica el tipo de módulo --}}
                                    <span class="badge-status">{{ $module['status'] }}</span>
                                </div>

                                <div class="icon-circle-v2 mb-3"
                                    style="color: {{ $module['color'] }}; background-color: {{ $module['color'] }}15;">
                                    <i class="bi {{ $module['icon'] }}"></i>
                                </div>

                                <h3 class="card-title-bold h5 mb-2">{{ $module['title'] }}</h3>
                                <p class="card-description small text-muted mb-4">{{ $module['desc'] }}</p>

                                {{-- Botón funcional con texto en español --}}
                                <div class="btn-access py-2"
                                    style="border: 1px solid {{ $module['color'] }}; color: {{ $module['color'] }};">
                                    {{ $module['action'] }}
                                </div>
                            </div>
                        </article>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        :root {
            --corp-blue-dark: #003a70;
            --corp-blue-main: #007bbd;
            --text-muted: #64748b;
            --bg-body: #f8fafc;
            --transition-standard: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--corp-blue-dark);
        }

        .bg-gradient-soft {
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 0% 0%, #edf2f7 0%, #ffffff 100%);
            z-index: -1;
        }

        .main-title { font-weight: 800; font-size: 2.8rem; letter-spacing: -1px; }
        .blue-accent { color: var(--corp-blue-main); }
        .badge-corporate { background: var(--corp-blue-dark); color: #fff; padding: 4px 12px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; }
        .header-line { height: 2px; width: 50px; background: var(--corp-blue-main); opacity: 0.5; }

        .hero-card-white { background: #fff; border-radius: 24px; }
        .welcome-text { font-weight: 700; color: #1e293b; }
        .text-blue-main { color: var(--corp-blue-main); }

        .corporate-card {
            background: #ffffff;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: var(--transition-standard);
        }

        .card-accent-line { height: 5px; width: 100%; position: absolute; top: 0; left: 0; }

        .icon-circle-v2 {
            width: 70px; height: 70px; border-radius: 18px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2rem; transition: var(--transition-standard);
        }

        .badge-status {
            font-size: 0.65rem; font-weight: 800; letter-spacing: 1px;
            color: #64748b; border: 1px solid #e2e8f0;
            padding: 3px 10px; border-radius: 6px;
        }

        .btn-access {
            border-radius: 10px; font-size: 0.75rem; font-weight: 800;
            transition: var(--transition-standard);
        }

        /* Hover: Toda la tarjeta reacciona */
        .card-anchor:hover .corporate-card {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 58, 112, 0.1) !important;
            border-color: var(--corp-blue-main);
        }

        .card-anchor:hover .icon-circle-v2 {
            background-color: var(--corp-blue-main) !important;
            color: #fff !important;
        }

        .card-anchor:hover .btn-access {
            background: var(--corp-blue-main);
            color: #fff !important;
            border-color: var(--corp-blue-main) !important;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in-up { animation: fadeInUp 0.6s ease-out forwards; opacity: 0; }
    </style>
@endsection
