<?php

namespace App\Http\Controllers;

use App\Models\ClassAssignment;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $currentTerm = $this->ensureCurrentTerm();

        // Si no hay gestión actual, mostrar datos vacíos
        if (!$currentTerm) {
            return view('dashboard', [
                'clases' => collect([]),
                'estadisticas' => [
                    'total_materias' => 0,
                    'total_grupos' => 0,
                    'dias_clase' => 0,
                    'horas_semanales' => 0,
                    'aulas_asignadas' => 0,
                ],
                'currentTerm' => null,
            ]);
        }

        $assignments = ClassAssignment::with([
                'courseOffering.subject',
                'courseOffering.group',
                'classroom',
                'timeslot',
            ])
            ->where('docente_id', Auth::id())
            ->whereHas('courseOffering', function ($q) use ($currentTerm) {
                $q->where('term_id', $currentTerm->id);
            })
            ->get()
            ->sortBy(function($assignment) {
                if (!$assignment->timeslot) return 0;
                // Ordenar por día y hora
                $dayOrder = ['lunes' => 1, 'martes' => 2, 'miércoles' => 3, 'jueves' => 4, 'viernes' => 5, 'sábado' => 6];
                return ($dayOrder[$assignment->timeslot->day] ?? 0) * 10000 + 
                       Carbon::parse($assignment->timeslot->start)->hour * 100 + 
                       Carbon::parse($assignment->timeslot->start)->minute;
            })
            ->values();

        // Agrupar clases consecutivas
        $clasesAgrupadas = [];
        $clasesYaProcesadas = [];
        
        foreach ($assignments as $assignment) {
            $timeslot = $assignment->timeslot;
            if (!$timeslot) continue;
            
            $dia = $timeslot->day;
            $horaInicio = Carbon::parse($timeslot->start);
            
            // Si ya fue procesada, saltar
            if (in_array($assignment->id, $clasesYaProcesadas)) continue;
            
            // Buscar clases consecutivas
            $clasesContinuas = collect([$assignment]);
            $clasesYaProcesadas[] = $assignment->id;
            $horaActual = $horaInicio->copy();
            
            while (true) {
                $horaSiguiente = $horaActual->copy()->addMinutes(15);
                $siguienteClase = $assignments->first(function($a) use ($dia, $horaSiguiente, $assignment, $clasesYaProcesadas) {
                    if (!$a->timeslot) return false;
                    if (in_array($a->id, $clasesYaProcesadas)) return false;
                    if ($a->timeslot->day !== $dia) return false;
                    if ($a->course_offering_id !== $assignment->course_offering_id) return false;
                    if ($a->classroom_id !== $assignment->classroom_id) return false;
                    
                    $startTime = Carbon::parse($a->timeslot->start);
                    return $startTime->equalTo($horaSiguiente);
                });
                
                if ($siguienteClase) {
                    $clasesContinuas->push($siguienteClase);
                    $clasesYaProcesadas[] = $siguienteClase->id;
                    $horaActual = Carbon::parse($siguienteClase->timeslot->start);
                } else {
                    break;
                }
            }
            
            // Crear objeto de clase agrupada
            $horaInicioGrupo = Carbon::parse($clasesContinuas->first()->timeslot->start);
            $horaFinGrupo = Carbon::parse($clasesContinuas->last()->timeslot->end);
            
            $clasesAgrupadas[] = (object) [
                'dia' => $dia,
                'hora_inicio' => $horaInicioGrupo->format('H:i:s'),
                'hora_fin' => $horaFinGrupo->format('H:i:s'),
                'courseOffering' => $assignment->courseOffering,
                'classroom' => $assignment->classroom,
            ];
        }
        
        $clases = collect($clasesAgrupadas);

        $estadisticas = [
            'total_materias' => $assignments->pluck('courseOffering.subject.id')->filter()->unique()->count(),
            'total_grupos' => $assignments->pluck('courseOffering.group.id')->filter()->unique()->count(),
            'dias_clase' => $assignments->pluck('timeslot.day')->filter()->unique()->count(),
            'horas_semanales' => $assignments->sum(function ($assignment) {
                $timeslot = $assignment->timeslot;
                if (!$timeslot) {
                    return 0;
                }

                $inicio = Carbon::parse($timeslot->start);
                $fin = Carbon::parse($timeslot->end);

                return $inicio->diffInMinutes($fin);
            }),
            'aulas_asignadas' => $assignments->pluck('classroom.id')->filter()->unique()->count(),
        ];

        return view('dashboard', compact('clases', 'estadisticas', 'currentTerm'));
    }

    protected function ensureCurrentTerm(): ?Term
    {
        $currentTerm = Session::get('current_term');

        if ($currentTerm instanceof Term) {
            return $currentTerm;
        }

        $currentTerm = Term::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$currentTerm) {
            $currentTerm = Term::where('start_date', '>', now())
                ->orderBy('start_date', 'asc')
                ->first();
        }

        if ($currentTerm) {
            Session::put('current_term', $currentTerm);
        }

        return $currentTerm;
    }
}