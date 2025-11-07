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
    public function index(Request $request): View
    {
        // Obtener la gestión actual
        $currentTerm = session('current_term');
        
        $query = User::whereHas('role', fn($q) => $q->where('name', 'Docente'));
        
        // Si hay gestión actual, contar solo las asignaciones de esa gestión
        if ($currentTerm) {
            $query->withCount(['classAssignmentsDocente' => function($q) use ($currentTerm) {
                $q->whereHas('courseOffering', function($query) use ($currentTerm) {
                    $query->where('term_id', $currentTerm->id);
                });
            }]);
        } else {
            $query->withCount('classAssignmentsDocente');
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
        
        // Validar columnas permitidas para ordenar
        $allowedSorts = ['name', 'email', 'class_assignments_docente_count', 'created_at'];
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        }
        
        $docentes = $query->paginate(10)->withQueryString();

        return view('admin.class-assignments.index', ['users' => $docentes, 'currentTerm' => $currentTerm]);
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
            ->get();
        
        // Agrupar clases consecutivas del mismo curso
        $clasesAgrupadas = [];
        $clasesRendered = []; // Para rastrear qué celdas ya fueron renderizadas
        
        foreach ($clasesAsignadas as $clase) {
            $dia = $clase->timeslot->day;
            $horaInicio = Carbon::parse($clase->timeslot->start)->format('H:i');
            $key = $dia . '-' . $horaInicio;
            
            // Si esta celda ya fue procesada, saltarla
            if (isset($clasesRendered[$key])) {
                continue;
            }
            
            // Buscar clases consecutivas de la misma oferta y aula
            $clasesContinuas = collect([$clase]);
            $horaActual = Carbon::parse($clase->timeslot->start);
            
            while (true) {
                $horaSiguiente = $horaActual->copy()->addMinutes(15)->format('H:i');
                $siguienteClase = $clasesAsignadas->first(function($c) use ($dia, $horaSiguiente, $clase) {
                    return $c->timeslot->day === $dia 
                        && Carbon::parse($c->timeslot->start)->format('H:i') === $horaSiguiente
                        && $c->course_offering_id === $clase->course_offering_id
                        && $c->classroom_id === $clase->classroom_id;
                });
                
                if ($siguienteClase) {
                    $clasesContinuas->push($siguienteClase);
                    $horaActual = Carbon::parse($siguienteClase->timeslot->start);
                } else {
                    break;
                }
            }
            
            // Almacenar el grupo de clases SOLO en la primera celda
            $clasesAgrupadas[$key] = [
                'clases' => $clasesContinuas,
                'rowspan' => $clasesContinuas->count(),
                'primera' => $clase
            ];
            
            // Marcar TODAS las celdas del grupo como ocupadas
            foreach ($clasesContinuas as $c) {
                $keyRendered = $c->timeslot->day . '-' . Carbon::parse($c->timeslot->start)->format('H:i');
                $clasesRendered[$keyRendered] = true;
            }
        }
        
        $clasesAsignadasAgrupadas = collect($clasesAgrupadas);
        $clasesRenderedSet = collect($clasesRendered);

        // Solo mostrar ofertas de curso de la gestión actual
        $courseOfferings = CourseOffering::with(['term', 'subject', 'group'])
            ->where('term_id', $currentTerm->id)
            ->get();
        
        $timeslots = Timeslot::all();
        $classrooms = Classroom::all();

        $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        
        // Generar franjas horarias con intervalos de 15 minutos
        $franjasHorarias = [];
        for ($hour = 7; $hour <= 23; $hour++) {
            foreach ([0, 15, 30, 45] as $minute) {
                if ($hour == 23 && $minute > 0) break; // Detener después de 23:00
                $inicio = sprintf('%02d:%02d', $hour, $minute);
                
                // Calcular el fin (siguiente intervalo de 15 min)
                $nextMinute = $minute + 15;
                $nextHour = $hour;
                if ($nextMinute >= 60) {
                    $nextMinute = 0;
                    $nextHour++;
                }
                $fin = sprintf('%02d:%02d', $nextHour, $nextMinute);
                
                $franjasHorarias[] = ['inicio' => $inicio, 'fin' => $fin];
            }
        }

        $shouldOpenModal = session()->pull('openModal', false);

        return view('admin.class-assignments.schedule', compact(
            'docente',
            'currentTerm',
            'clasesAsignadasAgrupadas',
            'clasesRenderedSet',
            'courseOfferings', 
            'timeslots', 
            'classrooms',
            'dias',
            'franjasHorarias',
            'shouldOpenModal'
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

    /**
     * Elimina un grupo de asignaciones de clases.
     */
    public function destroyGroup(Request $request): RedirectResponse
    {
        $classIds = $request->input('class_ids', []);
        
        if (empty($classIds)) {
            return redirect()->back()->with('error', 'No se especificaron asignaciones para eliminar.');
        }
        
        $assignments = ClassAssignment::whereIn('id', $classIds)->get();
        
        // Verificar que ninguna tenga asistencias
        foreach ($assignments as $assignment) {
            if ($assignment->teacherAttendances()->exists()) {
                return redirect()->back()->with('error', 'No se puede eliminar el grupo porque al menos una asignación tiene asistencias registradas.');
            }
        }
        
        $docenteId = $assignments->first()->docente_id;
        
        
        // Eliminar todas las asignaciones
        ClassAssignment::whereIn('id', $classIds)->delete();
        
        return Redirect::route('admin.class-assignments.schedule', $docenteId)
            ->with('success', 'Se eliminaron ' . count($classIds) . ' asignaciones correctamente.');
    }
}
