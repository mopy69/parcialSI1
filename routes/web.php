<?php
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TimeslotController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;


//ruta inicial, manda al login o al inicio de sesión
Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

//ruta donde ira la parte principal del proyecto
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//ruta del perfil personal del usuario
Route::middleware(['auth','log'])->group(function () {
    Route::resource('/profile', ProfileController::class);
});

/*
|--------------------------------------------------------------------------
| Rutas del administrador
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'rol:Administrador','log'])->prefix('admin')->name('admin.')->group(function () {

    // panel de control exclusivo del administrador
    Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');

    // gestion de usuarios
    // ruta para poder crear usuarios de forma masiva
    Route::post('/users/createMassive', [UserController::class, 'createMassive'])->name('users.createMassive');
    // ruta para poder ver, crear, editar y eliminar usuarios
    Route::resource('users', UserController::class);
    // gestion de aulas
    // ruta para poder ver, crear, editar y eliminar aulas
    Route::resource('classrooms', ClassroomController::class);

    // gestion de grupos
    // ruta para poder ver, crear, editar y eliminar grupos
    Route::resource('groups', GroupController::class);

    // gestion de materias
    // ruta para poder ver, crear, editar y eliminar materias
    Route::resource('subjects', SubjectController::class);

    // visualizacion de los logs(bitacora) de la aplicación
    Route::resource('logs', LogController::class);

    // gestion de horarios
    // ruta para poder ver, crear, editar y eliminar horarios
    Route::resource('timeslots', TimeslotController::class);
});

require __DIR__ . '/auth.php';