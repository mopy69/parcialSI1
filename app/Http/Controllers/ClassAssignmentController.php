<?php

namespace App\Http\Controllers;

use App\Models\ClassAssignment;
use App\Models\CourseOffering;
use App\Models\Timeslot;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ClassAssignmentRequest; 
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon; // <-- Asegúrate de importar Carbon

class ClassAssignmentController extends Controller
{
    /**
     * Muestra una lista de Docentes para la asignación.
     */
    public function index(): View
    {
        $docentes = User::whereHas('role', fn($q) => $q->where('name', 'Docente'))
                        ->withCount('classAssignmentsDocente') 
                        ->paginate(10); 

        return view('admin.class-assignments.index', ['users' => $docentes]);
    }

    /**
     * Muestra la cuadrícula del horario para un docente específico.
     */
    public function showSchedule(User $user): View|RedirectResponse
    {
        // Obtener la gestión actual
        $currentTerm = session('current_term');
        if (!$currentTerm) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Por favor, seleccione una gestión primero.');
        }

        $docente = $user;

        // Filtrar las clases asignadas por la gestión actual
        $clasesAsignadas = ClassAssignment::where('docente_id', $user->id)
            ->whereHas('courseOffering', function($query) use ($currentTerm) {
                $query->where('term_id', $currentTerm->id);
            })
            ->with(['courseOffering.subject', 'courseOffering.group', 'classroom', 'timeslot'])
            ->get()
            ->keyBy(function ($item) {
                return $item->timeslot->day . '-' . Carbon::parse($item->timeslot->start)->format('H:i');
            });

        // Solo mostrar ofertas de curso de la gestión actual (solo campos necesarios)
        $courseOfferings = CourseOffering::with(['term:id,name', 'subject:id,name', 'group:id,name'])
            ->where('term_id', $currentTerm->id)
            ->get(['id', 'term_id', 'subject_id', 'group_id']);
        
        $timeslots = Timeslot::all(['id', 'day', 'start', 'end']);
        $classrooms = Classroom::all(['id', 'name']);

        // No pasar estos arrays grandes a la vista, mejor generarlos en la vista
        // $dias y $franjasHorarias se pueden generar directamente en Blade

        $shouldOpenModal = session()->pull('openModal', false);

        return view('admin.class-assignments.schedule', compact(
            'docente',
            'currentTerm',
            'clasesAsignadas', 
            'courseOfferings', 
            'timeslots', 
            'classrooms',
            'shouldOpenModal'
        ));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create(): View
    {
        $courseOfferings = CourseOffering::with(['term:id,name', 'subject:id,name', 'group:id,name'])
            ->get(['id', 'term_id', 'subject_id', 'group_id']);
        $timeslots = Timeslot::all(['id', 'day', 'start', 'end']);
        $classrooms = Classroom::all(['id', 'name']);
        $docentes = User::whereHas('role', fn($q) => $q->where('name', 'Docente'))
            ->get(['id', 'name']);

        return view('admin.class-assignments.create', compact('courseOfferings', 'timeslots', 'classrooms', 'docentes'));
    }

    /**
     * Guarda la nueva asignación de clase.
     */
    public function store(ClassAssignmentRequest $request): RedirectResponse
    {
        try {
            $validatedData = $request->validated();
            $validatedData['coordinador_id'] = Auth::id();

            foreach ($validatedData['timeslot_ids'] as $timeslot_id) {
                ClassAssignment::create([
                    'coordinador_id' => $validatedData['coordinador_id'],
                    'docente_id' => $validatedData['docente_id'],
                    'course_offering_id' => $validatedData['course_offering_id'],
                    'classroom_id' => $validatedData['classroom_id'],
                    'timeslot_id' => $timeslot_id,
                ]);
            }

            return Redirect::route('admin.class-assignments.schedule', $validatedData['docente_id'])
                ->with('success', 'Asignaciones de clase creadas correctamente.');
        } catch (\Exception $e) {
            // No usar withInput() aquí para evitar cookies grandes
            return Redirect::back()
                ->with('error', 'Error al crear las asignaciones: ' . $e->getMessage())
                ->with('openModal', true);
        }
    }

    /**
     * Muestra una asignación específica.
     */
    public function show(ClassAssignment $classAssignment): View
    {
        return view('admin.class-assignments.show', compact('classAssignment'));
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(ClassAssignment $classAssignment): View
    {
        $courseOfferings = CourseOffering::with(['term:id,name', 'subject:id,name', 'group:id,name'])
            ->get(['id', 'term_id', 'subject_id', 'group_id']);
        $timeslots = Timeslot::all(['id', 'day', 'start', 'end']);
        $classrooms = Classroom::all(['id', 'name']);
        $docentes = User::whereHas('role', fn($q) => $q->where('name', 'Docente'))
            ->get(['id', 'name']);

        return view('admin.class-assignments.edit', compact('classAssignment', 'courseOfferings', 'timeslots', 'classrooms', 'docentes'));
    }

    /**
     * Actualiza la asignación de clase.
     */
    public function update(ClassAssignmentRequest $request, ClassAssignment $classAssignment): RedirectResponse
    {
        $docenteId = $classAssignment->docente_id;
        
        // Preparar los datos validados
        $validatedData = $request->validated();
        
        // Si viene timeslot_id, lo usamos (para edición)
        if (isset($validatedData['timeslot_id'])) {
            $validatedData['timeslot_ids'] = [$validatedData['timeslot_id']];
            unset($validatedData['timeslot_id']);
        }
        
        // Actualizar la asignación
        $classAssignment->update([
            'coordinador_id' => $validatedData['coordinador_id'],
            'docente_id' => $validatedData['docente_id'],
            'course_offering_id' => $validatedData['course_offering_id'],
            'classroom_id' => $validatedData['classroom_id'],
            'timeslot_id' => $validatedData['timeslot_ids'][0] ?? $classAssignment->timeslot_id,
        ]);

        return Redirect::route('admin.class-assignments.schedule', $docenteId)
            ->with('success', 'Asignación de clase actualizada correctamente.');
    }

    /**
     * Elimina la asignación de clase.
     */
    public function destroy(ClassAssignment $classAssignment): RedirectResponse
    {
        if ($classAssignment->teacherAttendances()->exists()) {
             return redirect()->back()->with('error', 'No se puede eliminar una asignación que ya tiene asistencias registradas.');
        }
        
        $docenteId = $classAssignment->docente_id;
        $classAssignment->delete();

        return Redirect::route('admin.class-assignments.schedule', $docenteId)
            ->with('success', 'Asignación de clase eliminada correctamente.');
    }
}