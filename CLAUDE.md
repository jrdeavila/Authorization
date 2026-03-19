# CLAUDE.md — PermissionsManager

Este archivo es leído automáticamente por Claude Code en cada sesión.
Contiene el contexto permanente del proyecto y el plan de trabajo activo.

---

## Proyecto

Sistema de gestión de roles y permisos para funcionarios de una entidad pública.
Refactorización de una implementación existente sobre Laravel 12 + Spatie Laravel Permission.

---

## Stack

| Capa | Tecnología |
|------|-----------|
| Framework | Laravel 12 |
| Permisos | Spatie Laravel Permission (ya instalado) |
| Frontend | Blade + Alpine.js (vía CDN, sin compilación) |
| UI | AdminLTE 3 (ya integrado) |
| BD principal | MySQL (conexión `mysql` por defecto) |
| BD usuarios | Conexión `timeit` (externa, solo lectura) |

---

## Reglas permanentes del proyecto

Estas reglas aplican en TODA interacción. Nunca ignorarlas:

1. **No alterar tablas de Spatie** — `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` no se tocan. Otras plataformas las consumen directamente.
2. **No modificar la conexión `timeit`** — `User` tiene `protected $connection = 'timeit'`. No mover datos de usuarios a la BD principal. No hacer joins entre las dos BDs.
3. **No romper rutas existentes** — si ya hay rutas funcionando en el proyecto, mantenerlas o redirigir hacia las nuevas.
4. **Alpine.js siempre vía CDN** — no agregar Vite, npm builds ni compilación de assets.
5. **`@js()` para pasar datos de Blade a Alpine.js** — nunca `@json()`.
6. **Rutas y nombres de clases en inglés.**
7. **Guard siempre `web`** en operaciones de Spatie.
8. **Siempre `->paginate()`** en listados, nunca `->get()` en vistas públicas.
9. **Siempre `route('...')`** para referencias a rutas, nunca URLs hardcodeadas.

---

## Arquitectura de archivos objetivo

```
app/
├── Http/Controllers/Permissions/
│   ├── RoleController.php
│   ├── PermissionController.php
│   ├── UserPermissionController.php
│   └── AuditController.php
├── Services/Permissions/
│   ├── RoleService.php
│   ├── PermissionService.php
│   └── UserPermissionService.php
├── Exports/PermissionsExport.php
├── Imports/PermissionsImport.php
└── Notifications/PermissionChangedNotification.php

resources/views/permissions/
├── layouts/base.blade.php
├── roles/
│   ├── index.blade.php
│   └── form.blade.php
├── permissions/
│   ├── index.blade.php
│   └── form.blade.php
├── users/
│   ├── index.blade.php
│   └── assign.blade.php
├── audit/index.blade.php
└── reports/pdf.blade.php

routes/permissions.php          ← incluido desde web.php
database/seeders/PermissionsSeeder.php
```

---

## Plan de trabajo — 7 fases

Ejecutar en orden. Verificar cada fase antes de continuar.

### Fase 1 — Configuración base y rutas
**Verificación:** `php artisan route:list --path=permissions`

**1.1 — Verificar Spatie**
- Si no existe `config/permission.php`, publicar: `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`
- Confirmar guard `web` y modelo `App\Models\User` en `config/permission.php`
- NO ejecutar migraciones de Spatie — las tablas ya existen

**1.2 — Instalar paquetes faltantes**
```bash
composer require spatie/laravel-activitylog
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```
- Publicar y ejecutar SOLO la migración de activitylog
- Publicar config de dompdf

**1.3 — Crear `routes/permissions.php`**
```php
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Permissions\RoleController;
use App\Http\Controllers\Permissions\PermissionController;
use App\Http\Controllers\Permissions\UserPermissionController;
use App\Http\Controllers\Permissions\AuditController;

Route::prefix('permissions')->name('permissions.')->middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::get('users', [UserPermissionController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserPermissionController::class, 'show'])->name('users.show');
    Route::post('users/{user}/assign', [UserPermissionController::class, 'assign'])->name('users.assign');
    Route::delete('users/{user}/revoke', [UserPermissionController::class, 'revoke'])->name('users.revoke');
    Route::get('audit', [AuditController::class, 'index'])->name('audit.index');
    Route::get('reports/pdf', [UserPermissionController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('reports/excel', [UserPermissionController::class, 'exportExcel'])->name('reports.excel');
    Route::post('reports/import', [UserPermissionController::class, 'import'])->name('reports.import');
});
```
Agregar al final de `routes/web.php`: `require __DIR__.'/permissions.php';`

**1.4 — Alpine.js en layout AdminLTE**
En el `<head>` del layout base:
```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

---

### Fase 2 — Servicios de negocio
**Objetivo:** toda la lógica de Spatie vive en servicios. Los controladores solo delegan.

**RoleService** (`app/Services/Permissions/RoleService.php`):
- `all()` → `Role::with('permissions')->get()`
- `find(int $id)` → `Role::with('permissions')->findOrFail($id)`
- `create(array $data)` → crea rol y sincroniza permisos si vienen en `$data['permissions']`
- `update(Role $role, array $data)` → actualiza nombre y sincroniza permisos
- `delete(Role $role)` → verifica que el rol no tenga usuarios; lanza excepción con mensaje claro si los tiene
- `syncPermissions(Role $role, array $permissionIds)` → `$role->syncPermissions($permissionIds)`
- Cada escritura registra: `activity()->causedBy(auth()->user())->performedOn($role)->log('...')`

**PermissionService** (`app/Services/Permissions/PermissionService.php`):
- `all()` → `Permission::orderBy('name')->get()`
- `allGrouped()` → agrupa por prefijo antes del primer `.` (sin punto → grupo `general`)
- `find(int $id)`, `create(array $data)`, `update(...)`, `delete(...)` (verifica que no tenga roles)
- Registrar actividad en escrituras

**UserPermissionService** (`app/Services/Permissions/UserPermissionService.php`):
- `getUsers(string $search = '')` → busca en `User` (timeit) por nombre o email, pagina de 20
- `getUserWithPermissions(User $user)` → retorna `['user', 'roles', 'directPermissions', 'allPermissions']`
- `assignRoles(User $user, array $roleIds)` → `syncRoles()` + notificación + actividad
- `assignDirectPermissions(User $user, array $permissionIds)` → `syncPermissions()` + actividad
- `revokeRole(User $user, int $roleId)`
- `revokePermission(User $user, int $permissionId)`

---

### Fase 3 — Controladores
**Objetivo:** controladores delgados que inyectan servicios y retornan vistas.

**RoleController**: inyecta `RoleService` + `PermissionService`. CRUD completo.
- Validación de nombre: `'required|string|max:100|unique:roles,name'` (ignorar id propio en update)
- `destroy()`: captura excepción de RoleService → redirect back con error

**PermissionController**: similar a RoleController para permisos.
- Convención de nombres: `modulo.accion`
- `index()` muestra count de roles que usan cada permiso

**UserPermissionController**: inyecta `UserPermissionService`.
- `index()`: listado paginado con `?search=`
- `show()`: perfil con roles, permisos directos y efectivos
- `assign()`: recibe `roles[]` y `permissions[]` como IDs
- `revoke()`: recibe `role_id` o `permission_id` + `type`
- `exportPdf()`, `exportExcel()`, `import()`

**AuditController**:
- `index()`: consulta `activity_log` filtrando por `?user_id`, `?subject_type`, `?date_from`, `?date_to`, pagina de 25

---

### Fase 4 — Vistas Blade + Alpine.js
**Objetivo:** interfaces sobre AdminLTE 3, Alpine.js para interactividad local.

**Convenciones:**
- Extienden `@extends('adminlte::page')`
- Alpine: filtros en vivo, confirmaciones, toggle masivo de checkboxes, colapso de grupos
- Éxito/error con `session('success')` y `session('error')`
- Datos a Alpine siempre con `@js()`, nunca `@json()`

**roles/index.blade.php**: tabla Nombre | Permisos (count) | Usuarios (count) | Acciones
Confirmación de eliminación con Alpine puro (sin SweetAlert):
```html
<div x-data="{ confirm: false, roleId: null, roleName: '' }">
  <button @click="confirm = true; roleId = {{ $role->id }}; roleName = '{{ $role->name }}'">
    Eliminar
  </button>
  <div x-show="confirm" class="modal fade show" style="display:block!important">
    <p>¿Eliminar rol <strong x-text="roleName"></strong>?</p>
    <form :action="`/permissions/roles/${roleId}`" method="POST">
      @csrf @method('DELETE')
      <button type="submit">Confirmar</button>
      <button type="button" @click="confirm = false">Cancelar</button>
    </form>
  </div>
</div>
```

**roles/form.blade.php**: campo nombre + permisos agrupados con búsqueda y toggle por grupo:
```html
<div x-data="{
  groups: @js($groupedPermissions),
  selected: @js($role->permissions->pluck('id') ?? []),
  search: '',
  toggleGroup(group) {
    const ids = group.map(p => p.id)
    const allSelected = ids.every(id => this.selected.includes(id))
    if (allSelected) {
      this.selected = this.selected.filter(id => !ids.includes(id))
    } else {
      ids.forEach(id => { if (!this.selected.includes(id)) this.selected.push(id) })
    }
  },
  get filteredGroups() {
    if (!this.search) return this.groups
    const q = this.search.toLowerCase()
    return Object.fromEntries(
      Object.entries(this.groups).map(([k, perms]) => [
        k, perms.filter(p => p.name.toLowerCase().includes(q))
      ]).filter(([k, perms]) => perms.length > 0)
    )
  }
}">
```

**users/assign.blade.php**: 3 secciones:
1. Roles disponibles como checkboxes (pre-seleccionados los actuales) → POST a `permissions.users.assign`
2. Permisos directos agrupados (con advertencia visual de uso excepcional)
3. Permisos efectivos en solo lectura, con origen indicado (Blade puro, no Alpine)

**audit/index.blade.php**: filtros via GET (quedan en URL), tabla paginada, sin DataTables

**reports/pdf.blade.php**: layout HTML puro (sin AdminLTE), CSS inline, fuentes Arial para DomPDF

---

### Fase 5 — Export, Import y Notificaciones

**PermissionsExport** (`app/Exports/PermissionsExport.php`):
- Implementa `FromCollection`, `WithHeadings`, `WithTitle`
- Columnas: ID Usuario | Nombre | Email | Roles | Permisos Directos | Permisos Efectivos
- Roles/permisos como strings separados por coma
- Sheet title: `Permisos por Funcionario`

**PermissionsImport** (`app/Imports/PermissionsImport.php`):
- Implementa `ToModel`, `WithHeadingRow`, `SkipsOnError`, `WithValidation`
- Columnas esperadas: `user_id`, `roles` (nombres separados por coma), `permissions`
- Por fila: busca User en timeit → resuelve nombres → llama servicios
- Retorna `['imported' => N, 'skipped' => N, 'errors' => [...]]`

**PermissionChangedNotification** (`app/Notifications/PermissionChangedNotification.php`):
- Canales: `database` + `mail`
- Payload: `type`, `subject`, `performed_by`, `timestamp`
- Mensaje: `"Tu acceso ha sido actualizado: se [asignó/revocó] el rol [nombre]."`

---

### Fase 6 — Políticas de acceso
**Verificación:** `php artisan db:seed --class=PermissionsSeeder`

**Gates en AuthServiceProvider:**
```php
Gate::define('manage-roles', fn(User $user) => $user->hasPermissionTo('roles.manage'));
Gate::define('manage-permissions', fn(User $user) => $user->hasPermissionTo('permissions.manage'));
Gate::define('assign-permissions', fn(User $user) => $user->hasPermissionTo('users.assign'));
Gate::define('view-audit', fn(User $user) => $user->hasAnyPermission(['audit.view', 'roles.manage']));
```

**PermissionsSeeder** (`database/seeders/PermissionsSeeder.php`):
Permisos base: `roles.manage`, `permissions.manage`, `users.assign`, `audit.view`, `reports.export`, `reports.import`
Rol `super-admin` con todos los permisos via `firstOrCreate`.

---

### Fase 7 — Menú AdminLTE
Agregar en `config/adminlte.php` → array `menu`:
```php
[
    'text'    => 'Gestión de Permisos',
    'icon'    => 'fas fa-shield-alt',
    'submenu' => [
        ['text' => 'Roles',        'url' => 'permissions/roles',       'icon' => 'fas fa-user-tag', 'can' => 'manage-roles'],
        ['text' => 'Permisos',     'url' => 'permissions/permissions',  'icon' => 'fas fa-key',      'can' => 'manage-permissions'],
        ['text' => 'Funcionarios', 'url' => 'permissions/users',        'icon' => 'fas fa-users',    'can' => 'assign-permissions'],
        ['text' => 'Auditoría',    'url' => 'permissions/audit',        'icon' => 'fas fa-history',  'can' => 'view-audit'],
    ],
],
```

---

## Estado actual del plan

| Fase | Estado |
|------|--------|
| Fase 1 — Configuración base y rutas | ✅ Completada |
| Fase 2 — Servicios de negocio | ✅ Completada |
| Fase 3 — Controladores | ✅ Completada |
| Fase 4 — Vistas Blade + Alpine.js | ✅ Completada |
| Fase 5 — Export, Import, Notificaciones | ✅ Completada |
| Fase 6 — Políticas de acceso | ✅ Completada |
| Fase 7 — Menú AdminLTE | ✅ Completada |

**Actualizar este estado a ✅ al completar cada fase.**
