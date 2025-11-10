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
        return view('attendance.qr-scan');
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
        
        // Datos de la sesión global
        $sessionData = [
            'token' => $token,
            'expires_at' => now()->addSeconds(self::QR_EXPIRATION),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_at' => now()
        ];

        // Guardar en cache
        Cache::put($sessionKey, $sessionData, self::QR_EXPIRATION);

        return response()->json([
            'success' => true,
            'qr_data' => json_encode([
                't' => $token,
                'exp' => $sessionData['expires_at']->timestamp
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
        $existingSession['token'] = $newToken;
        $existingSession['expires_at'] = now()->addSeconds(self::QR_EXPIRATION);

        Cache::put($sessionKey, $existingSession, self::QR_EXPIRATION);

        return response()->json([
            'success' => true,
            'qr_data' => json_encode([
                't' => $newToken,
                'exp' => $existingSession['expires_at']->timestamp
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
            
            if (!isset($qrData['t'], $qrData['exp'])) {
                return response()->json(['error' => 'QR inválido'], 400);
            }

            // Verificar que no haya expirado
            if ($qrData['exp'] < now()->timestamp) {
                return response()->json(['error' => 'QR expirado'], 400);
            }

            // Obtener sesión global del cache
            $sessionKey = "qr_global_session";
            $session = Cache::get($sessionKey);

            if (!$session) {
                return response()->json(['error' => 'Sesión no encontrada o expirada'], 404);
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
                
                // Ventana de entrada: 5 min antes hasta 15 min después del inicio
                $ventanaEntradaInicio = $startTime->copy()->subMinutes(5);
                $ventanaEntradaFin = $startTime->copy()->addMinutes(15);
                
                // Ventana de salida: desde el inicio hasta 15 min después del fin
                $ventanaSalidaInicio = $startTime->copy();
                $ventanaSalidaFin = $endTime->copy()->addMinutes(15);

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
                if ($now->between($ventanaEntradaInicio, $ventanaEntradaFin)) {
                    if (!$asistenciaEntrada || $asistenciaEntrada->state === 'pendiente') {
                        $opcionesDisponibles[] = [
                            'type' => 'entrada',
                            'label' => 'Registrar Entrada',
                            'estado_actual' => $asistenciaEntrada ? $asistenciaEntrada->state : 'pendiente'
                        ];
                    }
                }

                // Verificar si puede registrar SALIDA
                // Solo si ya registró entrada y está en la ventana de tiempo
                if ($asistenciaEntrada && $asistenciaEntrada->state !== 'pendiente' && $now->between($ventanaSalidaInicio, $ventanaSalidaFin)) {
                    if (!$asistenciaSalida || $asistenciaSalida->state === 'pendiente') {
                        $opcionesDisponibles[] = [
                            'type' => 'salida',
                            'label' => 'Registrar Salida',
                            'estado_actual' => $asistenciaSalida ? $asistenciaSalida->state : 'pendiente'
                        ];
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
                    'error' => 'No tienes clases disponibles en este momento',
                    'mensaje' => 'Las clases solo se pueden registrar 5 minutos antes y hasta 15 minutos después de su inicio'
                ], 404);
            }

            // Regenerar QR inmediatamente después del escaneo exitoso
            $this->refreshToken($request);

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
                $classEndTime = Carbon::parse($clase->timeslot->end);
                
                // Temprano si sale antes del fin de clase
                if ($now->lessThan($classEndTime)) {
                    $state = 'temprano';
                }
            }

            // Actualizar asistencia en TODOS los bloques del grupo
            TeacherAttendance::whereIn('class_assignment_id', $classAssignmentIds)
                ->where('date', $today)
                ->where('type', $type)
                ->update(['state' => $state]);

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
        return view('attendance.qr-scan');
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
}
