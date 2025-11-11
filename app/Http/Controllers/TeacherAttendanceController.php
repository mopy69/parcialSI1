<?php

namespace App\Http\Controllers;

use App\Models\TeacherAttendance;
use App\Models\User;
use App\Models\ClassAssignment;
use App\Models\Timeslot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TeacherAttendanceRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Carbon\Carbon;

class TeacherAttendanceController extends Controller
{
    /**
     * Muestra una lista de Docentes para gestión de asistencias.
     */
    public function index(Request $request): View
    {
        // Obtener la gestión actual
        $currentTerm = session('current_term');
        
        $query = User::whereHas('role', fn($q) => $q->where('name', 'Docente'));
        
        // Si hay gestión actual, contar las asistencias registradas
        if ($currentTerm) {
            $query->withCount(['classAssignmentsDocente as total_attendances' => function($q) use ($currentTerm) {
                $q->whereHas('courseOffering', function($subQ) use ($currentTerm) {
                    $subQ->where('term_id', $currentTerm->id);
                })
                ->whereHas('teacherAttendances');
            }]);
        } else {
            $query->withCount('classAssignmentsDocente as total_attendances');
        }
        
        // Búsqueda
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Ordenamiento
        $sortColumn = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');
        
        $allowedSorts = ['name', 'email', 'total_attendances', 'created_at'];
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        }
        
        $docentes = $query->paginate(10)->withQueryString();

        return view('admin.teacher-attendance.index', [
            'docentes' => $docentes,
            'currentTerm' => $currentTerm
        ]);
    }

    /**
     * Muestra el horario con asistencias para un docente específico.
     */
    public function showSchedule(User $user): View|RedirectResponse
    {
        // Obtener la gestión actual
        $currentTerm = session('current_term');
        if (!$currentTerm) {
            return Redirect::route('admin.teacher-attendance.index')
                ->with('error', 'Debe seleccionar una gestión académica primero.');
        }

        $docente = $user;

        // Marcar faltas automáticas antes de mostrar el horario
        $this->marcarFaltasAutomaticas();

        // Obtener todas las asignaciones de clase del docente para la gestión actual
        $clasesAsignadas = ClassAssignment::where('docente_id', $user->id)
            ->whereHas('courseOffering', function($q) use ($currentTerm) {
                $q->where('term_id', $currentTerm->id);
            })
            ->with(['courseOffering.subject', 'courseOffering.group', 'timeslot', 'classroom', 'teacherAttendances'])
            ->get();

        // Generar todas las fechas del período académico
        $startDate = Carbon::parse($currentTerm->start_date);
        $endDate = Carbon::parse($currentTerm->end_date);
        $today = Carbon::today();
        
        // Crear asistencias para todas las clases hasta hoy
        foreach ($clasesAsignadas as $clase) {
            $currentDate = $startDate->copy();
            $dayOfWeek = $this->getDayOfWeekNumber($clase->timeslot->day);
            
            while ($currentDate->lte(min($endDate, $today))) {
                if ($currentDate->dayOfWeek === $dayOfWeek) {
                    // Verificar si ya existen las asistencias (entrada y salida)
                    $existingEntrada = TeacherAttendance::where('class_assignment_id', $clase->id)
                        ->where('date', $currentDate->format('Y-m-d'))
                        ->where('type', 'entrada')
                        ->first();
                    
                    $existingSalida = TeacherAttendance::where('class_assignment_id', $clase->id)
                        ->where('date', $currentDate->format('Y-m-d'))
                        ->where('type', 'salida')
                        ->first();
                    
                    if (!$existingEntrada) {
                        // Crear asistencia de entrada
                        TeacherAttendance::create([
                            'class_assignment_id' => $clase->id,
                            'date' => $currentDate->format('Y-m-d'),
                            'type' => 'entrada',
                            'state' => 'pendiente'
                        ]);
                    }
                    
                    if (!$existingSalida) {
                        // Crear asistencia de salida
                        TeacherAttendance::create([
                            'class_assignment_id' => $clase->id,
                            'date' => $currentDate->format('Y-m-d'),
                            'type' => 'salida',
                            'state' => 'pendiente'
                        ]);
                    }
                }
                $currentDate->addDay();
            }
        }

        // Recargar las clases con las asistencias actualizadas
        $clasesAsignadas = ClassAssignment::where('docente_id', $user->id)
            ->whereHas('courseOffering', function($q) use ($currentTerm) {
                $q->where('term_id', $currentTerm->id);
            })
            ->with(['courseOffering.subject', 'courseOffering.group', 'timeslot', 'classroom', 'teacherAttendances'])
            ->get();

        // Agrupar clases consecutivas
        $clasesAgrupadas = [];
        $clasesRendered = [];
        
        foreach ($clasesAsignadas as $clase) {
            $key = $clase->timeslot->day . '_' . $clase->timeslot->start . '_' . $clase->course_offering_id;
            
            if (!in_array($key, $clasesRendered)) {
                $horaInicio = Carbon::parse($clase->timeslot->start);
                $classIds = [$clase->id];
                $duration = 15;
                
                $horaActual = $horaInicio->copy();
                while (true) {
                    $horaSiguiente = $horaActual->copy()->addMinutes(15);
                    $siguienteClase = $clasesAsignadas->first(function($c) use ($clase, $horaSiguiente) {
                        return $c->timeslot->day === $clase->timeslot->day
                            && $c->timeslot->start === $horaSiguiente->format('H:i:s')
                            && $c->course_offering_id === $clase->course_offering_id
                            && $c->classroom_id === $clase->classroom_id;
                    });
                    
                    if ($siguienteClase) {
                        $classIds[] = $siguienteClase->id;
                        $duration += 15;
                        $keySig = $siguienteClase->timeslot->day . '_' . $siguienteClase->timeslot->start . '_' . $siguienteClase->course_offering_id;
                        $clasesRendered[] = $keySig;
                        $horaActual = $horaSiguiente;
                    } else {
                        break;
                    }
                }
                
                $clasesAgrupadas[] = [
                    'class' => $clase,
                    'duration' => $duration,
                    'class_ids' => $classIds,
                    'rowspan' => $duration / 15
                ];
                
                $clasesRendered[] = $key;
            }
        }

        $clasesAsignadasAgrupadas = collect($clasesAgrupadas);
        $clasesRenderedSet = collect($clasesRendered);

        $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        
        // Generar franjas horarias
        $franjasHorarias = [];
        for ($hour = 7; $hour <= 23; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 15) {
                $franjasHorarias[] = sprintf('%02d:%02d', $hour, $minute);
            }
        }

        return view('admin.teacher-attendance.schedule', compact(
            'docente',
            'clasesAsignadasAgrupadas',
            'clasesRenderedSet',
            'dias',
            'franjasHorarias',
            'currentTerm'
        ));
    }

    /**
     * Actualiza el estado de una asistencia.
     */
    public function updateAttendance(Request $request): RedirectResponse
    {
        $request->validate([
            'attendance_id' => 'required|exists:teacher_attendances,id',
            'type' => 'required|in:entrada,salida',
            'state' => 'required|in:a tiempo,tarde,falta,temprano,puntual,justificado,pendiente'
        ]);

        $attendance = TeacherAttendance::findOrFail($request->attendance_id);
        $docenteId = $attendance->classAssignment->docente_id;

        $attendance->update([
            'type' => $request->type,
            'state' => $request->state
        ]);

        return Redirect::route('admin.teacher-attendance.schedule', $docenteId)
            ->with('success', 'Asistencia actualizada correctamente.');
    }

    /**
     * Convierte el nombre del día a número de día de la semana.
     */
    private function getDayOfWeekNumber($dayName): int
    {
        $days = [
            'lunes' => 1,
            'martes' => 2,
            'miércoles' => 3,
            'jueves' => 4,
            'viernes' => 5,
            'sábado' => 6,
            'domingo' => 0
        ];
        
        return $days[strtolower($dayName)] ?? 1;
    }

    /**
     * Marca automáticamente como falta las asistencias pendientes de clases que ya pasaron.
     */
    public function marcarFaltasAutomaticas(): void
    {
        $currentTerm = session('current_term');
        if (!$currentTerm) {
            return;
        }

        $now = Carbon::now();
        $today = Carbon::today()->format('Y-m-d');

        // Buscar todas las asistencias pendientes hasta hoy (incluyendo fechas pasadas)
        $asistenciasPendientes = TeacherAttendance::where('date', '<=', $today)
            ->where('state', 'pendiente')
            ->with(['classAssignment.timeslot', 'classAssignment.courseOffering'])
            ->whereHas('classAssignment.courseOffering', function($q) use ($currentTerm) {
                $q->where('term_id', $currentTerm->id);
            })
            ->get();

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
            }
        }
    }
}

