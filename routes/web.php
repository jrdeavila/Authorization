<?php

use App\Http\Controllers\UI\PermissionController;
use App\Http\Controllers\UI\RoleController;
use App\Http\Controllers\UI\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    Route::resource('roles', RoleController::class)
        ->only('index', 'store', 'update')
        ->names('roles');
    Route::resource('permissions', PermissionController::class)
        ->only('index', 'store')
        ->names('permissions');
});
