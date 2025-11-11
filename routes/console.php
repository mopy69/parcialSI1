<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use App\Models\TeacherAttendance;
use App\Models\ClassAssignment;
use App\Models\Term;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Comando para marcar automáticamente las faltas
Artisan::command('attendance:mark-absences', function () {
    $now = Carbon::now();
    $today = Carbon::today()->format('Y-m-d');
    
    $this->info('Marcando asistencias pendientes como falta...');

    // Buscar todas las asistencias pendientes de hoy
    $asistenciasPendientes = TeacherAttendance::where('date', '<=', $today)
        ->where('state', 'pendiente')
        ->with(['classAssignment.timeslot'])
        ->get();

    $contador = 0;

    foreach ($asistenciasPendientes as $asistencia) {
        $clase = $asistencia->classAssignment;
        if (!$clase || !$clase->timeslot) {
            continue;
        }

        // Crear fecha/hora completa de la clase
        $fechaClase = Carbon::parse($asistencia->date);
        $horaFin = Carbon::parse($clase->timeslot->end);
        
        // Combinar fecha de la asistencia con hora de fin de la clase
        $finClase = $fechaClase->copy()
            ->setTime($horaFin->hour, $horaFin->minute, $horaFin->second);

        // Ventana de gracia: 2 horas después del fin de clase
        $ventanaGracia = $finClase->copy()->addHours(2);

        // Si ya pasó la ventana de gracia, marcar como falta
        if ($now->greaterThan($ventanaGracia)) {
            $asistencia->state = 'falta';
            $asistencia->save();
            $contador++;
        }
    }

    $this->info("✓ Se marcaron {$contador} asistencias como falta");
    
    return 0;
})->purpose('Marca automáticamente como falta las asistencias pendientes que ya pasaron');

// Programar la tarea para que se ejecute cada hora
Schedule::command('attendance:mark-absences')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();

// Comando para limpiar sesiones expiradas
Artisan::command('session:clean', function () {
    $this->info('Limpiando sesiones expiradas...');
    
    $deleted = DB::table('sessions')
        ->where('last_activity', '<', now()->subMinutes(config('session.lifetime'))->timestamp)
        ->delete();
    
    $this->info("✓ Se eliminaron {$deleted} sesiones expiradas");
    
    return 0;
})->purpose('Limpia las sesiones expiradas de la base de datos');

// Programar limpieza de sesiones cada 6 horas
Schedule::command('session:clean')
    ->everySixHours()
    ->withoutOverlapping()
    ->runInBackground();

// Comando para activar/desactivar gestiones automáticamente según fechas
Artisan::command('terms:update-status', function () {
    $this->info('Actualizando estado de gestiones académicas...');
    
    $today = Carbon::today();
    $activated = 0;
    $deactivated = 0;
    
    // Obtener todas las gestiones
    $terms = Term::all();
    
    foreach ($terms as $term) {
        $startDate = Carbon::parse($term->start_date);
        $endDate = Carbon::parse($term->end_date);
        
        // Determinar si la gestión debe estar activa
        $shouldBeActive = $today->greaterThanOrEqualTo($startDate) && $today->lessThanOrEqualTo($endDate);
        
        // Si el estado actual es diferente al que debería ser, actualizar
        if ($shouldBeActive && !$term->asset) {
            // Activar gestión
            $term->asset = true;
            $term->save();
            $activated++;
            $this->line("  ✓ Activada: {$term->name} ({$term->start_date} - {$term->end_date})");
        } elseif (!$shouldBeActive && $term->asset) {
            // Desactivar gestión
            $term->asset = false;
            $term->save();
            $deactivated++;
            $this->line("  ✗ Desactivada: {$term->name} ({$term->start_date} - {$term->end_date})");
        }
    }
    
    $this->info("✓ Gestiones activadas: {$activated}");
    $this->info("✓ Gestiones desactivadas: {$deactivated}");
    
    return 0;
})->purpose('Activa y desactiva gestiones académicas según sus fechas de inicio y fin');

// Programar actualización de gestiones diariamente a la medianoche
Schedule::command('terms:update-status')
    ->daily()
    ->withoutOverlapping()
    ->runInBackground();
