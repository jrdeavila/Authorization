@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    <style>
        .login-page {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 40%, #1d4ed8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            width: 420px;
            max-width: 92vw;
        }

        /* Logo blanco sobre fondo oscuro */
        .login-logo {
            margin-bottom: 20px;
        }
        .login-logo a {
            color: #fff !important;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            text-decoration: none !important;
        }
        .login-logo img {
            filter: brightness(0) invert(1);
            margin-bottom: 8px;
        }

        /* Card principal */
        .card {
            border-radius: 22px !important;
            border: none !important;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35) !important;
            overflow: hidden;
            background: #fff !important;
        }
        .card.card-outline {
            border-top: none !important;
        }

        /* Header del card con gradiente */
        .card-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%) !important;
            border-bottom: none !important;
            padding: 28px 30px 22px !important;
            text-align: center;
        }
        .card-header .card-title {
            font-size: 1.2rem !important;
            font-weight: 800 !important;
            color: #ffffff !important;
            letter-spacing: 0.3px;
        }

        /* Body */
        .login-card-body {
            padding: 30px 30px 22px !important;
            background: #fff !important;
        }

        /* Footer */
        .card-footer {
            background: #f8fafc !important;
            border-top: 1px solid #f1f5f9 !important;
            padding: 14px 30px !important;
            text-align: center;
        }
        .card-footer p {
            font-size: 0.75rem;
            color: #94a3b8;
            margin: 0;
        }

        /* Inputs modernos */
        .input-group {
            margin-bottom: 20px !important;
        }
        .input-group .form-control {
            border-radius: 12px 0 0 12px !important;
            border: 2px solid #e2e8f0 !important;
            border-right: none !important;
            padding: 13px 16px !important;
            font-size: 0.95rem;
            height: auto !important;
            background: #f8fafc;
            color: #1e293b;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .input-group .form-control::placeholder {
            color: #94a3b8;
        }
        .input-group .form-control:focus {
            border-color: #3b82f6 !important;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(59,130,246,0.1) !important;
            color: #1e293b;
        }
        .input-group-append .input-group-text {
            border-radius: 0 12px 12px 0 !important;
            border: 2px solid #e2e8f0 !important;
            border-left: none !important;
            background: #f8fafc;
            color: #94a3b8;
            padding: 0 16px;
            transition: border-color 0.2s, background 0.2s;
        }
        .input-group:focus-within .input-group-text {
            border-color: #3b82f6 !important;
            background: #fff;
            color: #3b82f6;
        }

        /* Remember me */
        .icheck-primary label {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Botón de login */
        .btn-flat.btn-primary {
            border-radius: 12px !important;
            padding: 12px 20px !important;
            font-weight: 700 !important;
            font-size: 0.95rem !important;
            letter-spacing: 0.3px;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6) !important;
            border: none !important;
            box-shadow: 0 4px 16px rgba(29,78,216,0.35);
            transition: transform 0.15s, box-shadow 0.2s;
        }
        .btn-flat.btn-primary:hover {
            box-shadow: 0 6px 20px rgba(29,78,216,0.45);
            transform: translateY(-1px);
        }
        .btn-flat.btn-primary:active {
            transform: scale(0.97);
        }

        /* Validación */
        .invalid-feedback strong {
            font-size: 0.8rem;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-box { width: 100%; max-width: 96vw; padding: 0 8px; }
            .login-logo a { font-size: 1.2rem; }
            .login-card-body { padding: 24px 20px 18px !important; }
            .card-header { padding: 22px 20px 18px !important; }
            .card-footer { padding: 12px 20px !important; }
            .row.align-items-center {
                flex-direction: column;
                gap: 12px;
            }
            .row.align-items-center .col-7,
            .row.align-items-center .col-5 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
@stop

@section('auth_header', 'Iniciar Sesión')

@section('auth_body')
    @php
        $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
        if (config('adminlte.use_route_url', false)) {
            $loginUrl = $loginUrl ? route($loginUrl) : '';
        } else {
            $loginUrl = $loginUrl ? url($loginUrl) : '';
        }
    @endphp

    <form action="{{ $loginUrl }}" method="post">
        @csrf

        {{-- Usuario --}}
        <div class="input-group">
            <input type="number" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="Número de documento" autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div class="input-group">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Contraseña">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Acciones --}}
        <div class="row align-items-center">
            <div class="col-7">
                <div class="icheck-primary">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Recordarme</label>
                </div>
            </div>
            <div class="col-5">
                <button type="submit" class="btn btn-block btn-flat btn-primary">
                    <span class="fas fa-sign-in-alt mr-1"></span> Ingresar
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
@stop
