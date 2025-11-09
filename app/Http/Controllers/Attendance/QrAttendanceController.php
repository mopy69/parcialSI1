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
    const QR_EXPIRATION = 45;
    
    // Radio máximo en metros para validar ubicación (opcional)
    const MAX_DISTANCE_METERS = 100;

    /**
     * Muestra la vista principal de asistencia por QR para docentes
     */
    public function index(): View
    {
        $currentTerm = session('current_term');
        $user = Auth::user();
        
        // Obtener clases activas del docente para HOY
        $today = Carbon::today();
        $dayOfWeek = $this->getDayOfWeekName($today->dayOfWeek);
        
        $clasesHoy = ClassAssignment::where('docente_id', $user->id)
            ->whereHas('courseOffering', function($q) use ($currentTerm) {
                $q->where('term_id', $currentTerm->id);
            })
            ->whereHas('timeslot', function($q) use ($dayOfWeek) {
                $q->where('day', $dayOfWeek);
            })
            ->with(['courseOffering.subject', 'courseOffering.group', 'timeslot', 'classroom'])
            ->get();

        return view('attendance.qr-index', compact('clasesHoy', 'currentTerm'));
    }

    /**
     * Genera una sesión de QR para una clase específica
     */
    public function generateSession(Request $request): JsonResponse
    {
        $request->validate([
            'class_assignment_id' => 'required|exists:class_assignments,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        $classAssignment = ClassAssignment::findOrFail($request->class_assignment_id);
        
        // Verificar que el docente sea el dueño de esta clase
        if ($classAssignment->docente_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Generar token único
        $token = Str::random(32);
        $sessionKey = "qr_session_{$classAssignment->id}";
        
        // Datos de la sesión
        $sessionData = [
            'class_assignment_id' => $classAssignment->id,
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
                'caid' => $classAssignment->id, // class_assignment_id
                't' => $token,
                'exp' => $sessionData['expires_at']->timestamp
            ]),
            'expires_in' => self::QR_EXPIRATION
        ]);
    }

    /**
     * Regenera el token del QR (cada 45 segundos)
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $request->validate([
            'class_assignment_id' => 'required|exists:class_assignments,id'
        ]);

        $classAssignment = ClassAssignment::findOrFail($request->class_assignment_id);
        
        if ($classAssignment->docente_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $sessionKey = "qr_session_{$classAssignment->id}";
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
                'caid' => $classAssignment->id,
                't' => $newToken,
                'exp' => $existingSession['expires_at']->timestamp
            ]),
            'expires_in' => self::QR_EXPIRATION
        ]);
    }

    /**
     * Vista para escanear QR (estudiantes/visitantes)
     */
    public function scanView(): View
    {
        return view('attendance.qr-scan');
    }

    /**
     * Procesa el escaneo del QR y registra asistencia
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
            
            if (!isset($qrData['caid'], $qrData['t'], $qrData['exp'])) {
                return response()->json(['error' => 'QR inválido'], 400);
            }

            // Verificar que no haya expirado
            if ($qrData['exp'] < now()->timestamp) {
                return response()->json(['error' => 'QR expirado'], 400);
            }

            // Obtener sesión del cache
            $sessionKey = "qr_session_{$qrData['caid']}";
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

            // Verificar si ya registró asistencia hoy
            $today = Carbon::today()->format('Y-m-d');
            $existingAttendance = TeacherAttendance::where('class_assignment_id', $qrData['caid'])
                ->where('date', $today)
                ->where('type', 'entrada')
                ->first();

            if (!$existingAttendance) {
                return response()->json(['error' => 'No hay registro de asistencia pendiente'], 404);
            }

            // Actualizar asistencia
            $now = Carbon::now();
            $classAssignment = ClassAssignment::with('timeslot')->findOrFail($qrData['caid']);
            $classStartTime = Carbon::parse($classAssignment->timeslot->start);
            
            // Determinar estado según la hora
            $state = 'a tiempo';
            if ($now->greaterThan($classStartTime->addMinutes(10))) {
                $state = 'tarde';
            }

            $existingAttendance->update([
                'state' => $state
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Asistencia registrada correctamente',
                'state' => $state,
                'class' => $classAssignment->courseOffering->subject->name,
                'time' => $now->format('H:i')
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar QR: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cierra la sesión de QR
     */
    public function closeSession(Request $request): JsonResponse
    {
        $request->validate([
            'class_assignment_id' => 'required|exists:class_assignments,id'
        ]);

        $classAssignment = ClassAssignment::findOrFail($request->class_assignment_id);
        
        if ($classAssignment->docente_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $sessionKey = "qr_session_{$classAssignment->id}";
        Cache::forget($sessionKey);

        return response()->json(['success' => true, 'message' => 'Sesión cerrada']);
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
}
