<?php
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TimeslotController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\TermController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\ClassAssignmentController;

use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\DashboardController;


//ruta inicial, manda al login o al inicio de sesión
Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

//ruta donde ira la parte principal del proyecto
Route::get('/dashboard', UserDashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

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
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/change-term', [DashboardController::class, 'changeTerm'])->name('change-term');

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

    // gestión de términos académicos
    Route::resource('terms', TermController::class);

    // gestión de ofertas de cursos
    Route::resource('course-offerings', CourseOfferingController::class);

    // gestión de asignaciones de clases
    Route::resource('class-assignments', ClassAssignmentController::class);
    
    // Esta ruta mostrará el horario de un docente específico
    Route::get('class-assignments/schedule/{user}', [ClassAssignmentController::class, 'showSchedule'])
         ->name('class-assignments.schedule');
});

require __DIR__ . '/auth.php';