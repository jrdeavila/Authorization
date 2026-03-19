{{-- Bottom App Bar — Solo visible en móvil --}}
<div x-data="{ menuOpen: false, userOpen: false }"
     class="bottom-appbar d-block d-md-none"
     @keydown.escape.window="menuOpen = false; userOpen = false">

    {{-- Overlay --}}
    <div x-show="menuOpen || userOpen" x-cloak
         class="bottom-appbar-overlay"
         @click="menuOpen = false; userOpen = false"
         x-transition:enter="transition-fast"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-fast"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    {{-- Popup Menú --}}
    <div x-show="menuOpen" x-cloak
         class="bottom-appbar-popup"
         x-transition:enter="transition-slide-up"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition-slide-up"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         @click.outside="menuOpen = false">
        <div class="popup-header">
            <span class="popup-title">Gestión de Permisos</span>
            <button @click="menuOpen = false" class="popup-close" aria-label="Cerrar">&times;</button>
        </div>
        <nav class="popup-nav">
            <a href="{{ route('permissions.roles.index') }}" class="popup-item {{ request()->routeIs('permissions.roles.*') ? 'active' : '' }}">
                <i class="fas fa-user-tag"></i><span>Roles</span>
            </a>
            <a href="{{ route('permissions.permissions.index') }}" class="popup-item {{ request()->routeIs('permissions.permissions.*') ? 'active' : '' }}">
                <i class="fas fa-key"></i><span>Permisos</span>
            </a>
            <a href="{{ route('permissions.users.index') }}" class="popup-item {{ request()->routeIs('permissions.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i><span>Funcionarios</span>
            </a>
            <a href="{{ route('permissions.audit.index') }}" class="popup-item {{ request()->routeIs('permissions.audit.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i><span>Auditoría</span>
            </a>
        </nav>
    </div>

    {{-- Popup Usuario --}}
    <div x-show="userOpen" x-cloak
         class="bottom-appbar-popup"
         x-transition:enter="transition-slide-up"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition-slide-up"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         @click.outside="userOpen = false">
        <div class="popup-header">
            <span class="popup-title">Mi Cuenta</span>
            <button @click="userOpen = false" class="popup-close" aria-label="Cerrar">&times;</button>
        </div>
        <div class="popup-user-info">
            @auth
                <div class="user-card">
                    <img src="{{ auth()->user()->employee->curriculum->photo ?? '' }}"
                         class="user-avatar"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->employee->full_name ?? 'U') }}&size=48&background=4e73df&color=fff'"
                         alt="Avatar">
                    <div class="user-details">
                        <strong>{{ auth()->user()->employee->full_name ?? 'Usuario' }}</strong>
                        <small>{{ auth()->user()->email }}</small>
                        <small class="text-muted">{{ auth()->user()->employee->job->name ?? '' }}</small>
                    </div>
                </div>
            @endauth
        </div>
        <nav class="popup-nav">
            <a href="{{ route('home') }}" class="popup-item">
                <i class="fas fa-home"></i><span>Inicio</span>
            </a>
            <a href="{{ route('logout') }}" class="popup-item popup-item-danger"
               onclick="event.preventDefault(); document.getElementById('bottom-logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span>
            </a>
            <form id="bottom-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </nav>
    </div>

    {{-- Barra inferior --}}
    <nav class="bottom-appbar-bar">
        <a href="{{ route('permissions.roles.index') }}"
           class="bar-item {{ request()->routeIs('permissions.roles.*') ? 'active' : '' }}">
            <i class="fas fa-user-tag"></i>
            <span>Roles</span>
        </a>

        <a href="{{ route('permissions.users.index') }}"
           class="bar-item {{ request()->routeIs('permissions.users.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Funcionarios</span>
        </a>

        <button @click="menuOpen = !menuOpen; userOpen = false"
                class="bar-item" :class="{ 'active': menuOpen }">
            <i class="fas fa-th"></i>
            <span>Menú</span>
        </button>

        <button @click="userOpen = !userOpen; menuOpen = false"
                class="bar-item bar-item-avatar" :class="{ 'active': userOpen }">
            @auth
                <img src="{{ auth()->user()->employee->curriculum->photo ?? '' }}"
                     class="bar-avatar"
                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->employee->full_name ?? 'U') }}&size=28&background=4e73df&color=fff'"
                     alt="Mi cuenta">
            @else
                <i class="fas fa-user-circle"></i>
            @endauth
            <span>Cuenta</span>
        </button>
    </nav>
</div>

<style>
/* ==============================
   BOTTOM APP BAR — Mobile Only
   ============================== */
.bottom-appbar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1050;
}

.bottom-appbar-bar {
    display: flex;
    justify-content: space-around;
    align-items: center;
    background: #fff;
    border-top: 1px solid #e2e8f0;
    padding: 6px 0 env(safe-area-inset-bottom, 6px);
    box-shadow: 0 -2px 12px rgba(0,0,0,0.08);
}

.bar-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
    padding: 6px 4px;
    color: #94a3b8;
    text-decoration: none !important;
    font-size: 10px;
    font-weight: 600;
    border: none;
    background: none;
    cursor: pointer;
    transition: color 0.2s ease, transform 0.15s ease;
    -webkit-tap-highlight-color: transparent;
}

.bar-item i {
    font-size: 18px;
    margin-bottom: 2px;
    transition: transform 0.2s ease;
}

.bar-item.active {
    color: #4e73df;
}

.bar-item.active i {
    transform: scale(1.15);
}

.bar-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e2e8f0;
    transition: border-color 0.2s ease, transform 0.2s ease;
}

.bar-item.active .bar-avatar {
    border-color: #4e73df;
    transform: scale(1.15);
}

.bar-item:active {
    transform: scale(0.92);
}

/* Overlay */
.bottom-appbar-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 1049;
    backdrop-filter: blur(2px);
}

/* Popup */
.bottom-appbar-popup {
    position: fixed;
    bottom: 60px;
    left: 8px;
    right: 8px;
    z-index: 1051;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 -4px 30px rgba(0,0,0,0.15);
    overflow: hidden;
    max-height: 70vh;
    overflow-y: auto;
}

.popup-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
    background: #f8fafc;
}

.popup-title {
    font-weight: 700;
    font-size: 14px;
    color: #1e293b;
}

.popup-close {
    background: none;
    border: none;
    font-size: 22px;
    color: #94a3b8;
    cursor: pointer;
    padding: 0 4px;
    line-height: 1;
}

.popup-nav {
    padding: 8px;
}

.popup-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 14px;
    color: #334155;
    text-decoration: none !important;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    transition: background 0.15s ease;
}

.popup-item i {
    width: 22px;
    text-align: center;
    font-size: 16px;
    color: #64748b;
}

.popup-item:hover,
.popup-item:active {
    background: #f1f5f9;
    color: #1e293b;
}

.popup-item.active {
    background: #eef2ff;
    color: #4e73df;
}

.popup-item.active i {
    color: #4e73df;
}

.popup-item-danger {
    color: #dc3545 !important;
}

.popup-item-danger i {
    color: #dc3545 !important;
}

/* User Card */
.popup-user-info {
    padding: 16px 18px;
    border-bottom: 1px solid #f1f5f9;
}

.user-card {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e2e8f0;
}

.user-details {
    display: flex;
    flex-direction: column;
    line-height: 1.3;
}

.user-details strong {
    font-size: 14px;
    color: #1e293b;
}

.user-details small {
    font-size: 12px;
    color: #64748b;
}

/* Transitions */
.transition-fast {
    transition: opacity 0.2s ease;
}

.opacity-0 { opacity: 0; }
.opacity-100 { opacity: 1; }

.transition-slide-up {
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.translate-y-full { transform: translateY(100%); }
.translate-y-0 { transform: translateY(0); }

/* Padding inferior para que el contenido no quede debajo del bar */
@media (max-width: 767.98px) {
    .content-wrapper {
        padding-bottom: 70px !important;
    }
    /* Ocultar sidebar en móvil cuando hay bottom appbar */
    .main-sidebar {
        display: none !important;
    }
    .content-wrapper,
    .main-footer {
        margin-left: 0 !important;
    }
    /* Ocultar toggle del sidebar en navbar */
    .navbar .nav-item .nav-link[data-widget="pushmenu"] {
        display: none !important;
    }
}
</style>
