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

        $assignments = ClassAssignment::with([
                'courseOffering.subject',
                'courseOffering.group',
                'classroom',
                'timeslot',
            ])
            ->where('docente_id', Auth::id())
            ->when($currentTerm, function ($query) use ($currentTerm) {
                $query->whereHas('courseOffering', function ($q) use ($currentTerm) {
                    $q->where('term_id', $currentTerm->id);
                });
            })
            ->get();

        $clases = $assignments->map(function ($assignment) {
            $timeslot = $assignment->timeslot;

            return (object) [
                'dia' => optional($timeslot)->day,
                'hora_inicio' => optional($timeslot)->start,
                'courseOffering' => $assignment->courseOffering,
                'classroom' => $assignment->classroom,
            ];
        })->filter(function ($clase) {
            return $clase->dia && $clase->hora_inicio;
        })->values();

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

                return round($fin->diffInMinutes($inicio) / 60, 2);
            }),
            'aulas_asignadas' => $assignments->pluck('classroom.id')->filter()->unique()->count(),
        ];

        return view('dashboard', compact('clases', 'estadisticas'));
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