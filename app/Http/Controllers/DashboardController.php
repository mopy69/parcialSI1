<?php

namespace App\Http\Controllers;

use App\Models\ClassAssignment;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Obtener la gestión actual o la más cercana
        $currentTerm = Session::get('current_term');
        
        if (!$currentTerm) {
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
        }

        // Obtener todas las gestiones para el selector
        $terms = Term::orderBy('start_date', 'desc')->get();

        // Estadísticas básicas
        $usersCount = \App\Models\User::count();
        $classroomsCount = \App\Models\Classroom::count();
        $currentAssignments = 0;

        if ($currentTerm) {
            $currentAssignments = ClassAssignment::whereHas('courseOffering', function($query) use ($currentTerm) {
                $query->where('term_id', $currentTerm->id);
            })->count();
        }

        return view('admin.dashboard', compact('currentTerm', 'terms', 'usersCount', 'classroomsCount', 'currentAssignments'));
    }

    public function changeTerm(Request $request): \Illuminate\Http\RedirectResponse
    {
        $term = Term::findOrFail($request->term_id);
        Session::put('current_term', $term);
        
        return redirect()->back()->with('success', 'Gestión cambiada exitosamente.');
    }
}