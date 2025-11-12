<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\ClassAssignment;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class QrAttendanceController extends Controller
{
    // Tiempo de expiración del QR en segundos
    const QR_EXPIRATION = 30;
    
    // Radio máximo en metros para validar ubicación (opcional)
    const MAX_DISTANCE_METERS = 100;

    /**
     * Vista para administradores: generar QR global único
     */
    public function adminIndex(): View
    {
        $currentTerm = session('current_term');
        
        return view('attendance.admin-qr-index', compact('currentTerm'));
    }

    /**
     * Vista para docentes: escanear QR
     */
    public function docenteIndex(): View
    {
        $docente = Auth::user();
        $currentTerm = session('current_term');
        
        // Inicializar historial vacío
        $historialPorMateria = [];
        
        if ($currentTerm) {
            // Obtener todas las asistencias del docente en esta gestión
            $asistencias = TeacherAttendance::whereHas('classAssignment', function($q) use ($docente, $currentTerm) {
                $q->where('docente_id', $docente->id)
                  ->whereHas('courseOffering', function($q2) use ($currentTerm) {
                      $q2->where('term_id', $currentTerm->id);
                  });
            })
            ->with(['classAssignment.courseOffering.subject', 'classAssignment.courseOffering.group', 'classAssignment.timeslot'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
            // Agrupar por materia
            foreach ($asistencias as $asistencia) {
                $materia = $asistencia->classAssignment->courseOffering->subject->name;
                $grupo = $asistencia->classAssignment->courseOffering->group->name;
                $key = $materia . ' - ' . $grupo;
                
                if (!isset($historialPorMateria[$key])) {
                    $historialPorMateria[$key] = [
                        'materia' => $materia,
                        'grupo' => $grupo,
                        'asistencias' => [],
                        'estadisticas' => [
                            'total' => 0,
                            'a_tiempo' => 0,
                            'tarde' => 0,
                            'falta' => 0,
                            'temprano' => 0,
                            'puntual' => 0,
                            'pendiente' => 0
                        ]
                    ];
                }
                
                $historialPorMateria[$key]['asistencias'][] = $asistencia;
                $historialPorMateria[$key]['estadisticas']['total']++;
                
                // Contar estados
                if ($asistencia->state === 'a tiempo') {
                    $historialPorMateria[$key]['estadisticas']['a_tiempo']++;
                } elseif ($asistencia->state === 'tarde') {
                    $historialPorMateria[$key]['estadisticas']['tarde']++;
                } elseif ($asistencia->state === 'falta') {
                    $historialPorMateria[$key]['estadisticas']['falta']++;
                } elseif ($asistencia->state === 'temprano') {
                    $historialPorMateria[$key]['estadisticas']['temprano']++;
                } elseif ($asistencia->state === 'puntual') {
                    $historialPorMateria[$key]['estadisticas']['puntual']++;
                } elseif ($asistencia->state === 'pendiente') {
                    $historialPorMateria[$key]['estadisticas']['pendiente']++;
                }
            }
        }
        
        return view('attendance.qr-scan', [
            'historialPorMateria' => $historialPorMateria,
            'currentTerm' => $currentTerm
        ]);
    }

    /**
     * Genera sesión de QR global único (solo admin)
     */
    public function generateSession(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        // Generar token único global
        $token = Str::random(32);
        $sessionKey = "qr_global_session";
        
        $expiresAt = now()->addSeconds(self::QR_EXPIRATION);
        
        // Datos de la sesión global - guardar como primitivos, no objetos
        $sessionData = [
            'token' => $token,
            'expires_at' => $expiresAt->timestamp, // Guardar como timestamp
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_at' => now()->timestamp // Guardar como timestamp
        ];

        // Guardar en cache SIN TTL - manejaremos la expiración manualmente
        Cache::forever($sessionKey, $sessionData);

        return response()->json([
            'success' => true,
            'qr_data' => json_encode([
                't' => $token,
                'exp' => $expiresAt->timestamp
            ]),
            'expires_in' => self::QR_EXPIRATION
        ]);
    }

    /**
     * Regenera el token del QR global (cada 30 segundos) - solo admin
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $sessionKey = "qr_global_session";
        $existingSession = Cache::get($sessionKey);

        if (!$existingSession) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        // Nuevo token
        $newToken = Str::random(32);
        
        $expiresAt = now()->addSeconds(self::QR_EXPIRATION);
        
        // Recrear el array completo con timestamps (no objetos Carbon)
        $sessionData = [
            'token' => $newToken,
            'expires_at' => $expiresAt->timestamp,
            'latitude' => $existingSession['latitude'] ?? null,
            'longitude' => $existingSession['longitude'] ?? null,
            'created_at' => now()->timestamp
        ];

        // Guardar en cache SIN TTL - manejaremos la expiración manualmente
        Cache::forever($sessionKey, $sessionData);

        return response()->json([
            'success' => true,
            'qr_data' => json_encode([
                't' => $newToken,
                'exp' => $expiresAt->timestamp
            ]),
            'expires_in' => self::QR_EXPIRATION
        ]);
    }

    /**
     * Procesa el escaneo del QR y retorna las clases disponibles del docente
     */
    public function processQrScan(Request $request): JsonResponse
    {
        $request->validate([
            'qr_data' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        try {
            $qrData = json_decode($request->qr_data, true);
            
            if (!isset($qrData['t'])) {
                return response()->json(['error' => 'QR inválido - falta token'], 400);
            }

            // Obtener sesión global del cache
            $sessionKey = "qr_global_session";
            $session = Cache::get($sessionKey);

            // Si no hay sesión en cache, el QR expiró
            if (!$session) {
                return response()->json(['error' => 'QR expirado - no hay sesión en cache'], 400);
            }

            // Validar expiración manual (ya que usamos Cache::forever)
            if ($session['expires_at'] < now()->timestamp) {
                // Limpiar cache expirado
                Cache::forget($sessionKey);
                return response()->json(['error' => 'QR expirado - fuera de tiempo'], 400);
            }

            // Validar token
            if ($session['token'] !== $qrData['t']) {
                return response()->json(['error' => 'Token inválido'], 400);
            }

            // Validar geolocalización (si está configurada)
            if ($session['latitude'] && $session['longitude'] && $request->latitude && $request->longitude) {
                $distance = $this->calculateDistance(
                    $session['latitude'],
                    $session['longitude'],
                    $request->latitude,
                    $request->longitude
                );

                if ($distance > self::MAX_DISTANCE_METERS) {
                    return response()->json([
                        'error' => 'Estás demasiado lejos del aula',
                        'distance' => round($distance, 2)
                    ], 400);
                }
            }

            // Obtener docente autenticado
            $docente = Auth::user();
            
            // Obtener gestión actual
            $currentTerm = session('current_term');
            if (!$currentTerm) {
                return response()->json(['error' => 'No hay gestión académica activa'], 400);
            }

            // Obtener clases disponibles del docente (dentro de ventana de tiempo)
            $now = Carbon::now();
            $dayOfWeek = $this->getDayOfWeekName($now->dayOfWeek);
            $currentTime = $now->format('H:i:s');

            // Buscar todas las clases del docente para hoy
            $clasesDisponibles = ClassAssignment::where('docente_id', $docente->id)
                ->whereHas('courseOffering', function($q) use ($currentTerm) {
                    $q->where('term_id', $currentTerm->id);
                })
                ->whereHas('timeslot', function($q) use ($dayOfWeek) {
                    $q->where('day', $dayOfWeek);
                })
                ->with(['courseOffering.subject', 'courseOffering.group', 'timeslot', 'classroom'])
                ->orderBy('classroom_id')
                ->get();

            if ($clasesDisponibles->isEmpty()) {
                return response()->json(['error' => 'No tienes clases programadas para hoy'], 404);
            }

            // Agrupar clases consecutivas de la misma materia y grupo
            $clasesAgrupadas = $this->agruparClasesConsecutivas($clasesDisponibles);

            // Filtrar clases según ventana de tiempo y estado de asistencia
            $today = Carbon::today()->format('Y-m-d');
            $clasesConEstado = [];

            foreach ($clasesAgrupadas as $grupoClase) {
                $startTime = Carbon::parse($grupoClase['hora_inicio']);
                $endTime = Carbon::parse($grupoClase['hora_fin']);
                
                // Ventana de entrada: 5 min antes del inicio hasta el FIN de la clase
                $ventanaEntradaInicio = $startTime->copy()->subMinutes(5);
                $ventanaEntradaFin = $endTime; // Ya no hay extensión de 2 horas

                // Buscar asistencia existente en CUALQUIERA de los bloques del grupo
                $asistenciaEntrada = TeacherAttendance::whereIn('class_assignment_id', $grupoClase['ids'])
                    ->where('date', $today)
                    ->where('type', 'entrada')
                    ->first();

                $asistenciaSalida = TeacherAttendance::whereIn('class_assignment_id', $grupoClase['ids'])
                    ->where('date', $today)
                    ->where('type', 'salida')
                    ->first();

                $opcionesDisponibles = [];

                // Verificar si puede registrar ENTRADA
                // Solo puede registrar desde 5 min antes hasta el fin de la clase
                if ($now->greaterThanOrEqualTo($ventanaEntradaInicio) && $now->lessThanOrEqualTo($ventanaEntradaFin)) {
                    if (!$asistenciaEntrada || $asistenciaEntrada->state === 'pendiente') {
                        $opcionesDisponibles[] = [
                            'type' => 'entrada',
                            'label' => 'Registrar Entrada',
                            'estado_actual' => $asistenciaEntrada ? $asistenciaEntrada->state : 'pendiente'
                        ];
                    }
                } else if ($now->greaterThan($ventanaEntradaFin)) {
                    // Si ya pasó la hora de fin de clase, marcar como falta automáticamente
                    if ($asistenciaEntrada && $asistenciaEntrada->state === 'pendiente') {
                        foreach ($grupoClase['ids'] as $assignmentId) {
                            TeacherAttendance::where('class_assignment_id', $assignmentId)
                                ->where('date', $today)
                                ->where('type', 'entrada')
                                ->where('state', 'pendiente')
                                ->update(['state' => 'falta']);
                        }
                    }
                }

                // Verificar si puede registrar SALIDA
                // Solo si ya registró entrada (ventana: desde que registra entrada hasta 2 horas después del fin)
                if ($asistenciaEntrada && $asistenciaEntrada->state !== 'pendiente' && $asistenciaEntrada->state !== 'falta') {
                    $ventanaSalidaFin = $endTime->copy()->addHours(2);
                    if ($now->lessThanOrEqualTo($ventanaSalidaFin)) {
                        if (!$asistenciaSalida || $asistenciaSalida->state === 'pendiente') {
                            $opcionesDisponibles[] = [
                                'type' => 'salida',
                                'label' => 'Registrar Salida',
                                'estado_actual' => $asistenciaSalida ? $asistenciaSalida->state : 'pendiente'
                            ];
                        }
                    }
                }

                // Solo incluir clase si tiene opciones disponibles
                if (!empty($opcionesDisponibles)) {
                    $clasesConEstado[] = [
                        'id' => $grupoClase['id_principal'], // ID del primer bloque
                        'ids' => $grupoClase['ids'], // Todos los IDs del grupo
                        'materia' => $grupoClase['materia'],
                        'grupo' => $grupoClase['grupo'],
                        'aula' => $grupoClase['aula'],
                        'horario' => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
                        'hora_inicio' => $startTime->format('H:i'),
                        'hora_fin' => $endTime->format('H:i'),
                        'opciones' => $opcionesDisponibles
                    ];
                }
            }

            if (empty($clasesConEstado)) {
                return response()->json([
                    'error' => 'No tienes clases disponibles para registrar asistencia',
                    'mensaje' => 'Ya registraste todas tus asistencias de hoy o aún no es hora de registrar'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'clases' => $clasesConEstado,
                'hora_actual' => $now->format('H:i:s')
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar QR: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Confirma el registro de asistencia (entrada o salida)
     */
    public function confirmAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'class_assignment_id' => 'required|exists:class_assignments,id',
            'class_assignment_ids' => 'nullable|array', // IDs de todos los bloques del grupo
            'type' => 'required|in:entrada,salida'
        ]);

        try {
            $docente = Auth::user();
            $classAssignmentId = $request->class_assignment_id;
            $classAssignmentIds = $request->class_assignment_ids ?? [$classAssignmentId];
            $type = $request->type;
            
            // Verificar que la clase pertenezca al docente
            $clase = ClassAssignment::where('id', $classAssignmentId)
                ->where('docente_id', $docente->id)
                ->with(['courseOffering.subject', 'courseOffering.group', 'timeslot', 'classroom'])
                ->firstOrFail();

            $today = Carbon::today()->format('Y-m-d');
            $now = Carbon::now();

            // Buscar si ya hay asistencia registrada en CUALQUIERA de los bloques
            $attendanceExistente = TeacherAttendance::whereIn('class_assignment_id', $classAssignmentIds)
                ->where('date', $today)
                ->where('type', $type)
                ->where('state', '!=', 'pendiente')
                ->first();

            if ($attendanceExistente) {
                return response()->json(['error' => 'Ya registraste esta asistencia'], 400);
            }

            // Determinar estado según el tipo y la hora
            $state = 'a tiempo';
            
            if ($type === 'entrada') {
                $classStartTime = Carbon::parse($clase->timeslot->start);
                
                // Tarde si pasa más de 10 minutos del inicio
                if ($now->greaterThan($classStartTime->copy()->addMinutes(10))) {
                    $state = 'tarde';
                }
            } elseif ($type === 'salida') {
                // Buscar la hora de fin del último bloque del grupo
                $horaFinGrupo = Carbon::parse($clase->timeslot->end);
                
                // Si hay múltiples bloques, encontrar el último
                if (count($classAssignmentIds) > 1) {
                    $ultimoBloque = ClassAssignment::whereIn('id', $classAssignmentIds)
                        ->with('timeslot')
                        ->get()
                        ->sortByDesc(function($c) {
                            return Carbon::parse($c->timeslot->end);
                        })
                        ->first();
                    
                    if ($ultimoBloque) {
                        $horaFinGrupo = Carbon::parse($ultimoBloque->timeslot->end);
                    }
                }
                
                // Temprano: sale antes del fin de clase
                if ($now->lessThan($horaFinGrupo)) {
                    $state = 'temprano';
                }
                // Puntual: sale dentro de los 10 minutos después del fin
                elseif ($now->lessThanOrEqualTo($horaFinGrupo->copy()->addMinutes(10))) {
                    $state = 'puntual';
                }
                // Tarde: sale más de 10 minutos después del fin
                else {
                    $state = 'tarde';
                }
            }

            // Crear o actualizar asistencia en TODOS los bloques del grupo
            foreach ($classAssignmentIds as $assignmentId) {
                TeacherAttendance::updateOrCreate(
                    [
                        'class_assignment_id' => $assignmentId,
                        'date' => $today,
                        'type' => $type
                    ],
                    [
                        'state' => $state
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' registrada correctamente',
                'type' => $type,
                'state' => $state,
                'class' => $clase->courseOffering->subject->name,
                'group' => $clase->courseOffering->group->name,
                'time' => $now->format('H:i'),
                'classroom' => $clase->classroom->nro ?? 'N/A'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al confirmar asistencia: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cierra la sesión de QR global - solo admin
     */
    public function closeSession(Request $request): JsonResponse
    {
        $sessionKey = "qr_global_session";
        Cache::forget($sessionKey);

        return response()->json(['success' => true, 'message' => 'Sesión cerrada']);
    }

    /**
     * Vista pública para escanear QR (docentes)
     */
    public function scanView(): View
    {
        $docente = Auth::user();
        $currentTerm = session('current_term');
        
        // Marcar faltas automáticas antes de mostrar el historial
        $this->marcarFaltasAutomaticas();
        
        // Inicializar historial vacío
        $historialPorMateria = [];
        
        if ($currentTerm) {
            // Obtener todas las asistencias del docente en esta gestión
            $asistencias = TeacherAttendance::whereHas('classAssignment', function($q) use ($docente, $currentTerm) {
                $q->where('docente_id', $docente->id)
                  ->whereHas('courseOffering', function($q2) use ($currentTerm) {
                      $q2->where('term_id', $currentTerm->id);
                  });
            })
            ->with(['classAssignment.courseOffering.subject', 'classAssignment.courseOffering.group', 'classAssignment.timeslot'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
            // Agrupar por materia
            foreach ($asistencias as $asistencia) {
                $materia = $asistencia->classAssignment->courseOffering->subject->name;
                $grupo = $asistencia->classAssignment->courseOffering->group->name;
                $key = $materia . ' - ' . $grupo;
                
                if (!isset($historialPorMateria[$key])) {
                    $historialPorMateria[$key] = [
                        'materia' => $materia,
                        'grupo' => $grupo,
                        'asistencias' => [],
                        'estadisticas' => [
                            'total' => 0,
                            'a_tiempo' => 0,
                            'tarde' => 0,
                            'falta' => 0,
                            'temprano' => 0,
                            'puntual' => 0,
                            'pendiente' => 0
                        ]
                    ];
                }
                
                $historialPorMateria[$key]['asistencias'][] = $asistencia;
                $historialPorMateria[$key]['estadisticas']['total']++;
                
                // Contar estados
                if ($asistencia->state === 'a tiempo') {
                    $historialPorMateria[$key]['estadisticas']['a_tiempo']++;
                } elseif ($asistencia->state === 'tarde') {
                    $historialPorMateria[$key]['estadisticas']['tarde']++;
                } elseif ($asistencia->state === 'falta') {
                    $historialPorMateria[$key]['estadisticas']['falta']++;
                } elseif ($asistencia->state === 'temprano') {
                    $historialPorMateria[$key]['estadisticas']['temprano']++;
                } elseif ($asistencia->state === 'puntual') {
                    $historialPorMateria[$key]['estadisticas']['puntual']++;
                } elseif ($asistencia->state === 'pendiente') {
                    $historialPorMateria[$key]['estadisticas']['pendiente']++;
                }
            }
        }
        
        return view('attendance.qr-scan', [
            'historialPorMateria' => $historialPorMateria,
            'currentTerm' => $currentTerm
        ]);
    }

    /**
     * Calcula distancia entre dos puntos GPS en metros (fórmula de Haversine)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // metros

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Convierte número de día a nombre
     */
    private function getDayOfWeekName($dayNumber): string
    {
        $days = [
            0 => 'domingo',
            1 => 'lunes',
            2 => 'martes',
            3 => 'miércoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sábado'
        ];
        
        return $days[$dayNumber] ?? 'lunes';
    }

    /**
     * Agrupa clases consecutivas de la misma materia y grupo
     */
    private function agruparClasesConsecutivas($clases)
    {
        $grupos = [];
        $procesados = [];

        foreach ($clases as $clase) {
            // Si ya fue procesado, saltar
            if (in_array($clase->id, $procesados)) {
                continue;
            }

            // Iniciar nuevo grupo
            $grupo = [
                'id_principal' => $clase->id,
                'ids' => [$clase->id],
                'materia' => $clase->courseOffering->subject->name,
                'grupo' => $clase->courseOffering->group->name,
                'aula' => $clase->classroom->nro ?? 'N/A',
                'hora_inicio' => $clase->timeslot->start,
                'hora_fin' => $clase->timeslot->end,
                'course_offering_id' => $clase->course_offering_id,
                'classroom_id' => $clase->classroom_id
            ];

            $procesados[] = $clase->id;
            $horaFinActual = Carbon::parse($clase->timeslot->end);

            // Buscar bloques consecutivos
            foreach ($clases as $otraClase) {
                if (in_array($otraClase->id, $procesados)) {
                    continue;
                }

                // Verificar si es la misma materia, grupo y aula
                if ($otraClase->course_offering_id === $clase->course_offering_id &&
                    $otraClase->classroom_id === $clase->classroom_id) {
                    
                    $inicioOtra = Carbon::parse($otraClase->timeslot->start);
                    
                    // Si el inicio de la otra clase es igual al fin de la actual (consecutivas)
                    if ($inicioOtra->equalTo($horaFinActual)) {
                        $grupo['ids'][] = $otraClase->id;
                        $grupo['hora_fin'] = $otraClase->timeslot->end;
                        $horaFinActual = Carbon::parse($otraClase->timeslot->end);
                        $procesados[] = $otraClase->id;
                    }
                }
            }

            $grupos[] = $grupo;
        }

        return $grupos;
    }

    /**
     * Marca automáticamente como falta las asistencias pendientes de clases que ya pasaron.
     */
    private function marcarFaltasAutomaticas(): void
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
