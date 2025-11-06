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
    public function showSchedule(User $user): View
    {
        $docente = $user;

        $clasesAsignadas = ClassAssignment::where('docente_id', $user->id)
            ->with(['courseOffering.subject', 'classroom', 'timeslot'])
            ->get()
            ->keyBy(function ($item) {
                // --- ¡CAMBIO AQUÍ! ---
                // Formatea la hora a HH:MM para crear la clave
                return $item->timeslot->day . '-' . Carbon::parse($item->timeslot->start)->format('H:i');
            });

        $courseOfferings = CourseOffering::with(['term', 'subject', 'group'])->get();
        $timeslots = Timeslot::all();
        $classrooms = Classroom::all();

        $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        
        $franjasHorarias = [
            '07:00', '07:15', '07:30', '07:45',
            '08:00', '08:15', '08:30', '08:45',
            '09:00', '09:15', '09:30', '09:45',
            '10:00', '10:15', '10:30', '10:45',
            '11:00', '11:15', '11:30', '11:45',
            '12:00', '12:15', '12:30', '12:45',
            '13:00', '13:15', '13:30', '13:45',
            '14:00', '14:15', '14:30', '14:45',
            '15:00', '15:15', '15:30', '15:45',
            '16:00', '16:15', '16:30', '16:45',
            '17:00', '17:15', '17:30', '17:45',
            '18:00', '18:15', '18:30', '18:45',
            '19:00', '19:15', '19:30', '19:45',
            '20:00', '20:15', '20:30', '20:45',
            '21:00', '21:15', '21:30', '21:45',
            '22:00', '22:15', '22:30', '22:45',
            '23:00'
        ];

        return view('admin.class-assignments.schedule', compact(
            'docente', 
            'clasesAsignadas', 
            'courseOfferings', 
            'timeslots', 
            'classrooms',
            'dias',
            'franjasHorarias'
        ));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create(): View
    {
        $courseOfferings = CourseOffering::with(['term', 'subject', 'group'])->get();
        $timeslots = Timeslot::all();
        $classrooms = Classroom::all();
        $docentes = User::whereHas('role', fn($q) => $q->where('name', 'Docente'))->get();

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
            return Redirect::back()
                ->withInput()
                ->with('error', 'Error al crear las asignaciones: ' . $e->getMessage());
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
        $courseOfferings = CourseOffering::with(['term', 'subject', 'group'])->get();
        $timeslots = Timeslot::all();
        $classrooms = Classroom::all();
        $docentes = User::whereHas('role', fn($q) => $q->where('name', 'Docente'))->get();

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