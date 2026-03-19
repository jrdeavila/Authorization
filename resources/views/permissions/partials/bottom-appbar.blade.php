{{-- Bottom App Bar — Mobile & Tablet --}}
<div x-data="{ moreOpen: false }" class="d-block d-lg-none">

    {{-- Backdrop --}}
    <div class="mob-sheet-backdrop" x-show="moreOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="moreOpen = false">
    </div>

    {{-- More Sheet --}}
    <div class="mob-more-sheet" x-show="moreOpen" x-cloak
         x-transition:enter="sheet-enter"
         x-transition:enter-start="sheet-enter-from"
         x-transition:enter-end="sheet-enter-to"
         x-transition:leave="sheet-leave"
         x-transition:leave-start="sheet-leave-from"
         x-transition:leave-end="sheet-leave-to"
         @click.outside="moreOpen = false">

        {{-- Handle --}}
        <div class="mob-sheet-handle"></div>

        {{-- Header: user info --}}
        @auth
        @php
            $authEmployee = optional(auth()->user()->employee);
            $authName     = $authEmployee->full_name ?: auth()->user()->email;
            $authEmail    = auth()->user()->email;
            $authPhoto    = optional($authEmployee->curriculum)->photo;
            $authInitial  = strtoupper(substr($authName, 0, 1));
            $authJob      = optional($authEmployee->job)->name ?? '';
        @endphp
        <div class="mob-sheet-header">
            <div class="mob-sheet-avatar">
                @if($authPhoto)
                    <img src="{{ $authPhoto }}" alt="{{ $authName }}"
                         style="width:100%;height:100%;object-fit:cover;border-radius:50%;"
                         onerror="this.textContent='{{ $authInitial }}';this.style.display='none';this.parentElement.textContent='{{ $authInitial }}'">
                @else
                    {{ $authInitial }}
                @endif
            </div>
            <div style="min-width:0;">
                <div class="mob-sheet-user-name">{{ $authName }}</div>
                <div class="mob-sheet-user-email">{{ $authEmail }}</div>
                @if($authJob)
                    <div class="mob-sheet-user-email">{{ $authJob }}</div>
                @endif
            </div>
            <button class="mob-sheet-close" @click="moreOpen = false" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endauth

        {{-- Body --}}
        <div class="mob-sheet-body">

            {{-- Navegación — Grid 3 columnas --}}
            <p class="mob-sheet-section-title">Gestión de Permisos</p>
            <div class="mob-sheet-grid">
                <a href="{{ route('permissions.roles.index') }}" class="mob-sheet-card {{ request()->routeIs('permissions.roles.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tag"></i><span>Roles</span>
                </a>
                <a href="{{ route('permissions.permissions.index') }}" class="mob-sheet-card {{ request()->routeIs('permissions.permissions.*') ? 'active' : '' }}">
                    <i class="fas fa-key"></i><span>Permisos</span>
                </a>
                <a href="{{ route('permissions.users.index') }}" class="mob-sheet-card {{ request()->routeIs('permissions.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i><span>Funcionarios</span>
                </a>
            </div>

            {{-- Opciones — List rows --}}
            <p class="mob-sheet-section-title">Opciones</p>
            <a href="{{ route('permissions.audit.index') }}" class="mob-sheet-row">
                <span class="row-icon"><i class="fas fa-history"></i></span>
                Auditoría
                <i class="fas fa-chevron-right row-chevron"></i>
            </a>
            <a href="{{ route('permissions.reports.pdf') }}" class="mob-sheet-row">
                <span class="row-icon"><i class="fas fa-file-pdf"></i></span>
                Exportar PDF
                <i class="fas fa-chevron-right row-chevron"></i>
            </a>
            <a href="{{ route('permissions.reports.excel') }}" class="mob-sheet-row">
                <span class="row-icon"><i class="fas fa-file-excel"></i></span>
                Exportar Excel
                <i class="fas fa-chevron-right row-chevron"></i>
            </a>
            <a href="{{ route('home') }}" class="mob-sheet-row">
                <span class="row-icon"><i class="fas fa-home"></i></span>
                Inicio
                <i class="fas fa-chevron-right row-chevron"></i>
            </a>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="mb-0">
                @csrf
                <button type="submit" class="mob-sheet-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </button>
            </form>

        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="mob-bottom-nav">
        <a href="{{ route('home') }}"
           class="mob-bnav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>

        <a href="{{ route('permissions.roles.index') }}"
           class="mob-bnav-item {{ request()->routeIs('permissions.roles.*') ? 'active' : '' }}">
            <i class="fas fa-user-tag"></i>
            <span>Roles</span>
        </a>

        <button type="button" class="mob-bnav-item" :class="{ 'active': moreOpen }"
                @click="moreOpen = !moreOpen" style="flex:0.8;">
            <i class="fas fa-th"></i>
            <span>Menú</span>
        </button>

        <button type="button" class="mob-bnav-item" :class="{ 'active': moreOpen }"
                @click="moreOpen = !moreOpen">
            @auth
                @if($authPhoto)
                    <img src="{{ $authPhoto }}" class="mob-bnav-avatar"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
                         alt="Mi cuenta">
                    <i class="fas fa-user-circle" style="display:none"></i>
                @else
                    <i class="fas fa-user-circle"></i>
                @endif
            @else
                <i class="fas fa-user-circle"></i>
            @endauth
            <span>Cuenta</span>
        </button>
    </div>

</div>

<style>
/* ==============================
   BOTTOM NAV BAR
   ============================== */
.mob-bottom-nav {
    display: none;
    position: fixed; bottom: 0; left: 0; right: 0; z-index: 1040;
    background: #fff; border-top: 1px solid #e5e7eb;
    box-shadow: 0 -4px 16px rgba(0,0,0,.1);
    min-height: 62px;
    padding-bottom: env(safe-area-inset-bottom, 0);
}
@media (max-width: 991.98px) {
    .mob-bottom-nav { display: flex !important; }
}

.mob-bnav-item {
    flex: 1; display: flex; flex-direction: column; align-items: center;
    justify-content: center; gap: 3px; padding: 6px 4px;
    text-decoration: none !important; color: #9ca3af; border: none; background: none;
    font-size: .57rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px;
    cursor: pointer; position: relative; transition: color .15s;
}
.mob-bnav-item.active,
.mob-bnav-item:hover { color: #1d4ed8; }
.mob-bnav-item.active::after {
    content: ''; position: absolute; top: 0; left: 25%; right: 25%;
    height: 2.5px; background: #1d4ed8; border-radius: 0 0 3px 3px;
}
.mob-bnav-item i { font-size: 1.2rem; }

.mob-bnav-avatar {
    width: 24px; height: 24px; border-radius: 50%; object-fit: cover;
    border: 2px solid #e2e8f0; transition: border-color .2s;
}
.mob-bnav-item.active .mob-bnav-avatar { border-color: #1d4ed8; }

/* ==============================
   SHEET BACKDROP
   ============================== */
.mob-sheet-backdrop {
    position: fixed; inset: 0; z-index: 1041;
    background: rgba(15,23,42,.5);
}

/* ==============================
   SHEET PANEL
   ============================== */
.mob-more-sheet {
    position: fixed; bottom: 0; left: 0; right: 0; z-index: 1042;
    background: #fff; border-radius: 22px 22px 0 0;
    max-height: 86vh; display: flex; flex-direction: column;
    overflow: hidden;
    box-shadow: 0 -8px 40px rgba(0,0,0,.18);
}

/* Sheet transitions */
.sheet-enter { transition: transform .32s cubic-bezier(.32,1,.23,1), opacity .22s ease; }
.sheet-enter-from { transform: translateY(100%); opacity: 0; }
.sheet-enter-to   { transform: translateY(0);    opacity: 1; }
.sheet-leave      { transition: transform .22s ease-in, opacity .18s ease; }
.sheet-leave-from { transform: translateY(0);    opacity: 1; }
.sheet-leave-to   { transform: translateY(100%); opacity: 0; }

.mob-sheet-handle {
    width: 38px; height: 4px; background: #d1d5db;
    border-radius: 2px; margin: 10px auto 0; flex-shrink: 0;
}

/* ==============================
   SHEET HEADER (user info)
   ============================== */
.mob-sheet-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
    padding: 14px 18px 18px;
    display: flex; align-items: center; gap: 12px; flex-shrink: 0;
}
.mob-sheet-avatar {
    width: 46px; height: 46px; border-radius: 50%;
    background: rgba(255,255,255,.2); border: 2px solid rgba(255,255,255,.4);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; font-weight: 800; color: #fff; flex-shrink: 0;
    overflow: hidden;
}
.mob-sheet-user-name { font-size: .95rem; font-weight: 700; color: #fff; line-height: 1.25; }
.mob-sheet-user-email { font-size: .72rem; color: rgba(255,255,255,.65); margin-top: 2px; }
.mob-sheet-close {
    margin-left: auto; width: 30px; height: 30px; border-radius: 50%;
    background: rgba(255,255,255,.15); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,.85); font-size: .8rem; flex-shrink: 0;
}

/* ==============================
   SHEET BODY
   ============================== */
.mob-sheet-body {
    overflow-y: auto; flex: 1;
    padding: 12px 14px calc(16px + env(safe-area-inset-bottom, 0));
}
.mob-sheet-section-title {
    font-size: .62rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: .7px; color: #9ca3af; padding: 10px 2px 6px; margin: 0;
}

/* ==============================
   GRID CARDS (3 columnas)
   ============================== */
.mob-sheet-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 2px;
}
.mob-sheet-card {
    display: flex; flex-direction: column; align-items: center; gap: 6px;
    padding: 14px 4px 11px; background: #f5f8ff;
    border: 1px solid #e0e7ff; border-radius: 14px;
    text-decoration: none !important; color: #1e293b; cursor: pointer;
    transition: background .12s, transform .1s;
}
.mob-sheet-card:active { transform: scale(.95); background: #e0e7ff; }
.mob-sheet-card.active { background: #dbeafe; border-color: #93c5fd; }
.mob-sheet-card i { font-size: 1.25rem; color: #1d4ed8; }
.mob-sheet-card span { font-size: .62rem; font-weight: 700; text-align: center; line-height: 1.25; color: #374151; }

/* ==============================
   LIST ROWS
   ============================== */
.mob-sheet-row {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 8px; border-radius: 10px;
    text-decoration: none !important; color: #1e293b;
    font-size: .85rem; font-weight: 600;
    transition: background .12s;
}
.mob-sheet-row:active { background: #f1f5f9; }
.mob-sheet-row .row-icon {
    width: 34px; height: 34px; border-radius: 9px;
    background: #eef2ff; display: flex; align-items: center;
    justify-content: center; flex-shrink: 0;
}
.mob-sheet-row .row-icon i { font-size: .9rem; color: #1d4ed8; }
.mob-sheet-row .row-chevron { margin-left: auto; color: #d1d5db; font-size: .7rem; }

/* ==============================
   LOGOUT
   ============================== */
.mob-sheet-logout {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; padding: 13px; margin-top: 10px;
    background: #fff0f0; border: 1.5px solid #fecaca;
    border-radius: 12px; color: #dc2626;
    font-size: .88rem; font-weight: 700; cursor: pointer;
    transition: background .12s;
}
.mob-sheet-logout:active { background: #fee2e2; }

/* ==============================
   DARK MODE
   ============================== */
/* Bottom nav bar */
body.dark-mode .mob-bottom-nav {
    background: #1e293b;
    border-top-color: #334155;
    box-shadow: 0 -4px 16px rgba(0,0,0,.3);
}
body.dark-mode .mob-bnav-item { color: #64748b; }
body.dark-mode .mob-bnav-item.active,
body.dark-mode .mob-bnav-item:hover { color: #60a5fa; }
body.dark-mode .mob-bnav-item.active::after { background: #60a5fa; }
body.dark-mode .mob-bnav-avatar { border-color: #475569; }
body.dark-mode .mob-bnav-item.active .mob-bnav-avatar { border-color: #60a5fa; }

/* Sheet panel */
body.dark-mode .mob-more-sheet {
    background: #1e293b;
    box-shadow: 0 -8px 40px rgba(0,0,0,.4);
}
body.dark-mode .mob-sheet-handle { background: #475569; }

/* Sheet header */
body.dark-mode .mob-sheet-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
}

/* Sheet body */
body.dark-mode .mob-sheet-section-title { color: #64748b; }

/* Grid cards */
body.dark-mode .mob-sheet-card {
    background: #334155;
    border-color: #475569;
    color: #e2e8f0;
}
body.dark-mode .mob-sheet-card:active { background: #475569; }
body.dark-mode .mob-sheet-card.active { background: #1e3a8a; border-color: #3b82f6; }
body.dark-mode .mob-sheet-card i { color: #60a5fa; }
body.dark-mode .mob-sheet-card span { color: #cbd5e1; }

/* List rows */
body.dark-mode .mob-sheet-row { color: #e2e8f0; }
body.dark-mode .mob-sheet-row:active { background: #334155; }
body.dark-mode .mob-sheet-row .row-icon { background: #334155; }
body.dark-mode .mob-sheet-row .row-icon i { color: #60a5fa; }
body.dark-mode .mob-sheet-row .row-chevron { color: #475569; }

/* Logout */
body.dark-mode .mob-sheet-logout {
    background: #3b1c1c;
    border-color: #7f1d1d;
    color: #fca5a5;
}
body.dark-mode .mob-sheet-logout:active { background: #4c1d1d; }

/* Backdrop */
body.dark-mode .mob-sheet-backdrop { background: rgba(0,0,0,.65); }

/* Navbar superior */
body.dark-mode .main-header.navbar {
    background: #1e293b !important;
    border-bottom-color: #334155 !important;
}
</style>
