<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Autorización') }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow: hidden;
        }

        .welcome-page {
            min-height: 100vh;
            min-height: 100dvh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 40%, #1d4ed8 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        /* Decoraciones de fondo */
        .welcome-page::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -15%;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }
        .welcome-page::after {
            content: '';
            position: absolute;
            bottom: -25%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: rgba(255,255,255,0.02);
            border-radius: 50%;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 480px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo de fondo */
        .welcome-logo-bg {
            position: absolute;
            right: -5%;
            top: 50%;
            transform: translateY(-50%);
            width: 450px;
            height: 450px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: 0.04;
            pointer-events: none;
            z-index: 0;
            animation: fadeIn 1.2s ease-out 0.3s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 0.04; }
        }

        /* Textos */
        .welcome-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            line-height: 1.3;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .welcome-subtitle {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.65);
            line-height: 1.5;
            margin-bottom: 36px;
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }

        /* Card */
        .welcome-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            padding: 28px 24px;
            animation: fadeInUp 0.6s ease-out 0.4s both;
        }

        /* Botones */
        .welcome-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 15px 24px;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            cursor: pointer;
            border: none;
            text-decoration: none !important;
            transition: transform 0.15s, box-shadow 0.2s;
        }

        .welcome-btn:active {
            transform: scale(0.97);
        }

        .welcome-btn-primary {
            background: linear-gradient(135deg, #fff 0%, #f1f5f9 100%);
            color: #1e3a8a;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }

        .welcome-btn-primary:hover {
            box-shadow: 0 12px 32px rgba(0,0,0,0.3);
            transform: translateY(-2px);
            color: #1e3a8a;
        }

        .welcome-btn-outline {
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255,255,255,0.3);
            margin-top: 12px;
        }

        .welcome-btn-outline:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.5);
            color: #fff;
        }

        .welcome-btn i {
            font-size: 1.1rem;
        }

        /* Separador */
        .welcome-divider {
            display: flex;
            align-items: center;
            margin: 18px 0;
            color: rgba(255,255,255,0.3);
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .welcome-divider::before,
        .welcome-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.15);
        }
        .welcome-divider::before { margin-right: 12px; }
        .welcome-divider::after { margin-left: 12px; }

        /* Footer */
        .welcome-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 16px;
            text-align: center;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out 0.5s both;
        }
        .welcome-footer p {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.35);
            margin: 0;
        }

        /* Shield badge */
        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 0.68rem;
            font-weight: 700;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            animation: fadeInUp 0.6s ease-out 0.15s both;
        }
        .welcome-badge i {
            font-size: 0.7rem;
            color: #60a5fa;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .welcome-title { font-size: 1.3rem; }
            .welcome-subtitle { font-size: 0.85rem; margin-bottom: 28px; }
            .welcome-card { padding: 22px 18px; }
            .welcome-btn { padding: 14px 20px; font-size: 0.92rem; }
            .welcome-logo-bg { width: 280px; height: 280px; right: -15%; }
        }
    </style>
</head>
<body>
    <div class="welcome-page">
        <img src="{{ asset('img/logo.png') }}" alt="" class="welcome-logo-bg">

        <div class="welcome-content">
            <div class="welcome-badge">
                <i class="fas fa-shield-alt"></i>
                Sistema de Autorización
            </div>

            <h1 class="welcome-title">Cámara de Comercio de Valledupar</h1>
            <p class="welcome-subtitle">
                Plataforma de gestión de roles, permisos y control de acceso para funcionarios.
            </p>

            <div class="welcome-card">
                @auth
                    <a href="{{ route('home') }}" class="welcome-btn welcome-btn-primary">
                        <i class="fas fa-home"></i>
                        Ir a la Plataforma
                    </a>
                    <div class="welcome-divider">o</div>
                    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" class="welcome-btn welcome-btn-outline">
                            <i class="fas fa-sign-out-alt"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="welcome-btn welcome-btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        Iniciar Sesión
                    </a>
                @endauth
            </div>
        </div>

        <div class="welcome-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }} — Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>
