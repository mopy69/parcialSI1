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
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Attendance\QrAttendanceController;

use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\DashboardController;


//ruta inicial, manda al login o al inicio de sesión
Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

//ruta donde ira la parte principal del proyecto
Route::get('/dashboard', UserDashboardController::class)
    ->middleware(['auth', 'log'])
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

    // gestión de ofertas de cursos - Rutas personalizadas ANTES del resource
    Route::post('course-offerings/copy-from-term', [CourseOfferingController::class, 'copyFromTerm'])
         ->name('course-offerings.copy-from-term');
    
    Route::resource('course-offerings', CourseOfferingController::class);

    // gestión de asignaciones de clases - Rutas personalizadas ANTES del resource
    Route::post('class-assignments/copy-from-term', [ClassAssignmentController::class, 'copyFromTerm'])
         ->name('class-assignments.copy-from-term');
    
    // Esta ruta mostrará el horario de un docente específico
    Route::get('class-assignments/schedule/{user}', [ClassAssignmentController::class, 'showSchedule'])
         ->name('class-assignments.schedule');
    
    // Ruta para eliminar grupo de clases
    Route::delete('class-assignments/destroy-group', [ClassAssignmentController::class, 'destroyGroup'])
         ->name('class-assignments.destroy-group');
    
    // Ruta para mover bloque de horario
    Route::post('class-assignments/move-block', [ClassAssignmentController::class, 'moveBlock'])
         ->name('class-assignments.move-block');
    
    // Ruta para ajustar duración de bloque
    Route::post('class-assignments/adjust-duration', [ClassAssignmentController::class, 'adjustDuration'])
         ->name('class-assignments.adjust-duration');
    
    // Resource de asignaciones de clases
    Route::resource('class-assignments', ClassAssignmentController::class);

    // Gestión de asistencias de docentes - Rutas personalizadas ANTES del resource
    Route::get('teacher-attendance/schedule/{user}', [TeacherAttendanceController::class, 'showSchedule'])
         ->name('teacher-attendance.schedule');
    
    Route::post('teacher-attendance/update', [TeacherAttendanceController::class, 'updateAttendance'])
         ->name('teacher-attendance.update');
    
    // Resource de asistencias de docentes
    Route::resource('teacher-attendance', TeacherAttendanceController::class);

    // Rutas de reportes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/export', [ReportController::class, 'export'])->name('reports.export');
});

// Asistencia por QR (disponible para todos los usuarios autenticados)
Route::middleware('auth', 'log')->prefix('attendance')->name('attendance.')->group(function () {
    // Admin: generar QR
    Route::get('/qr/admin', [QrAttendanceController::class, 'adminIndex'])->name('qr.admin');
    Route::post('/qr/generate', [QrAttendanceController::class, 'generateSession'])->name('qr.generate');
    Route::post('/qr/refresh', [QrAttendanceController::class, 'refreshToken'])->name('qr.refresh');
    Route::post('/qr/close', [QrAttendanceController::class, 'closeSession'])->name('qr.close');
    
    // Docente: escanear QR
    Route::get('/qr/scan', [QrAttendanceController::class, 'scanView'])->name('qr.scan');
    Route::post('/qr/process', [QrAttendanceController::class, 'processQrScan'])->name('qr.process');
    Route::post('/qr/confirm', [QrAttendanceController::class, 'confirmAttendance'])->name('qr.confirm');
});

require __DIR__ . '/auth.php';