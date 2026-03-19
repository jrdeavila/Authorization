<?php

use App\Http\Controllers\Permissions\AuditController;
use App\Http\Controllers\Permissions\PermissionController;
use App\Http\Controllers\Permissions\RoleController;
use App\Http\Controllers\Permissions\UserPermissionController;
use Illuminate\Support\Facades\Route;

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
