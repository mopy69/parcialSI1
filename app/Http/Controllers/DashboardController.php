<?php

namespace App\Http\Controllers;

use App\Models\ClassAssignment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        
        // Obtener las clases asignadas al docente
        $clases = ClassAssignment::where('docente_id', $user->id)
            ->with(['courseOffering.subject', 'courseOffering.group', 'classroom', 'timeslot'])
            ->get()
            ->map(function ($asignacion) {
                return (object)[
                    'dia' => $asignacion->timeslot->day,
                    'hora_inicio' => $asignacion->timeslot->start,
                    'courseOffering' => $asignacion->courseOffering,
                    'classroom' => $asignacion->classroom
                ];
            });

        // Calcular estadísticas
        $estadisticas = [
            'total_materias' => $clases->unique('courseOffering.subject.id')->count(),
            'total_grupos' => $clases->unique('courseOffering.group.id')->count(),
            'dias_clase' => $clases->unique('dia')->count(),
            'horas_semanales' => $clases->count(),
            'aulas_asignadas' => $clases->unique('classroom.id')->count()
        ];

        return view('dashboard', compact('user', 'clases', 'estadisticas'));
    }
}