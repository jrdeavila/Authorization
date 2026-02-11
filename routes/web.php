<?php

use App\Http\Controllers\UI\PermissionController;
use App\Http\Controllers\UI\RoleController;
use App\Http\Controllers\UI\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use function Symfony\Component\String\u;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/users/{user}/resume', [UserController::class, 'resume'])->name('users.resume');
    Route::resource('users', UserController::class)
        ->only('index', 'show', 'update')
        ->names('users');
    Route::middleware([
        'can:roles-read',
        'can:roles-create',
        'can:roles-update',
        'can:roles-delete',
    ])->resource('roles', RoleController::class)
        ->only('index', 'store', 'update', 'destroy')
        ->names('roles');
Route::middleware([
    'can:permissions-read',
    'can:permissions-create',
    'can:permissions-update',
    'can:permissions-delete',
])->resource('permissions', PermissionController::class)
    ->only('index', 'store','update', 'destroy')
    ->names('permissions');
});
