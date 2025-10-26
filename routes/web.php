<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// User Routes
Route::middleware(['auth', 'rol:Administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');
    
    // Users Management
    Route::get('/users', [UserController::class, 'usersIndex'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'createUser'])->name('users.create');
    Route::post('/users/createMassive', [UserController::class, 'createUserMassive'])->name('users.createMassive');
    Route::post('/users', [UserController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'deleteUser'])->name('users.destroy');
    
    // Roles Management
    Route::get('/roles', [RoleController::class, 'Index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'delete'])->name('roles.destroy');
    
    // Permissions Management
    Route::get('/permissions', [PermissionController::class, 'Index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'delete'])->name('permissions.destroy');
});

require __DIR__.'/auth.php';