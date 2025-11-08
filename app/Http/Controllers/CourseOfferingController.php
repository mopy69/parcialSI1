<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Models\Term;
use App\Models\Subject;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CourseOfferingRequest; // Asegúrate de que este Request exista
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CourseOfferingController extends Controller
{
    /**
     * Muestra la lista de ofertas de cursos.
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Obtener la gestión actual desde la sesión
        $currentTerm = session('current_term');
        
        if (!$currentTerm) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Por favor, seleccione una gestión primero.');
        }

        // Filtrar ofertas por la gestión actual
        $query = CourseOffering::with(['term', 'subject', 'group'])
            ->where('term_id', $currentTerm->id);

        // Búsqueda
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('subject', function($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('code', 'like', "%{$search}%");
                })
                ->orWhereHas('group', function($groupQuery) use ($search) {
                    $groupQuery->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        // Ordenamiento
        $sortColumn = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'desc');
        
        // Validar columnas permitidas para ordenar
        $allowedSorts = ['id'];
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        }
        
        $courseOfferings = $query->paginate(10)->withQueryString();

        // Obtener otras gestiones para copiar
        $availableTerms = Term::where('id', '!=', $currentTerm->id)
            ->orderBy('name', 'desc')
            ->get();

        return view('admin.course-offerings.index', compact('courseOfferings', 'currentTerm', 'availableTerms'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create(): View|RedirectResponse
    {
        // Obtener la gestión actual desde la sesión
        $currentTerm = session('current_term');
        if (!$currentTerm) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Por favor, seleccione una gestión primero.');
        }

        $subjects = Subject::all();
        $groups = Group::all();

        return view('admin.course-offerings.create', compact('currentTerm', 'subjects', 'groups'));
    }

    /**
     * Guarda la nueva oferta de curso.
     */
    public function store(CourseOfferingRequest $request): RedirectResponse
    {
        try {
            // Obtener la gestión actual
            $currentTerm = session('current_term');
            if (!$currentTerm) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Por favor, seleccione una gestión primero.');
            }

            // Crear la oferta de curso con la gestión actual
            $validatedData = $request->validated();
            $validatedData['term_id'] = $currentTerm->id;
            
            CourseOffering::create($validatedData);

            // 2. Si tiene éxito, redirige con el mensaje de éxito (verde)
            return Redirect::route('admin.course-offerings.index')
                ->with('success', 'Oferta de curso creada correctamente.');

        } catch (QueryException $e) {
            
            // 3. Si la base de datos falla (ej. error de 'UNIQUE')
            //    Atrapa el error y redirige DE VUELTA con un mensaje de error (rojo)
            
            // (Opcional: puedes revisar el código de error, ej. 23505 para duplicado)
            
            return Redirect::back()
                ->withInput() // Esto re-llena el formulario con los datos que el usuario envió
                ->with('error', 'Error al crear la oferta. Es posible que esta combinación de Gestión, Materia y Grupo ya exista.');
        
        } catch (\Exception $e) {
            
            // 4. Atrapa cualquier otro error inesperado
            return Redirect::back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Muestra una oferta de curso específica.
     */
    public function show(CourseOffering $courseOffering): View // Usa RMB
    {
        return view('admin.course-offerings.show', compact('courseOffering'));
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(CourseOffering $courseOffering): View|RedirectResponse
    {
        // Obtener la gestión actual desde la sesión
        $currentTerm = session('current_term');
        if (!$currentTerm) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Por favor, seleccione una gestión primero.');
        }

        $subjects = Subject::all();
        $groups = Group::all();

        return view('admin.course-offerings.edit', compact('courseOffering', 'currentTerm', 'subjects', 'groups'));
    }

    /**
     * Actualiza la oferta de curso.
     */
    public function update(CourseOfferingRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        // Obtener la gestión actual
        $currentTerm = session('current_term');
        if (!$currentTerm) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Por favor, seleccione una gestión primero.');
        }

        // Actualizar con la gestión actual
        $validatedData = $request->validated();
        $validatedData['term_id'] = $currentTerm->id;
        
        $courseOffering->update($validatedData);

        return Redirect::route('admin.course-offerings.index')
            ->with('success', 'Oferta de curso actualizada correctamente.');
    }

    /**
     * Elimina la oferta de curso.
     */
    public function destroy(CourseOffering $courseOffering): RedirectResponse // Usa RMB
    {
        // Añadido: Lógica de protección (basada en tu modelo)
        if ($courseOffering->classAssignments()->exists()) {
             return redirect()->back()->with('error', 'No se puede eliminar una oferta que ya tiene clases asignadas.');
        }
        
        $courseOffering->delete();

        return Redirect::route('admin.course-offerings.index')
            ->with('success', 'Oferta de curso eliminada correctamente.');
    }

    /**
     * Copia ofertas de curso desde otra gestión a la gestión actual.
     */
    public function copyFromTerm(Request $request): RedirectResponse
    {
        $currentTerm = session('current_term');
        if (!$currentTerm) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Por favor, seleccione una gestión primero.');
        }

        $sourceTerm = $request->input('source_term_id');
        
        if (!$sourceTerm) {
            return redirect()->back()->with('error', 'Debe seleccionar una gestión de origen.');
        }

        if ($sourceTerm == $currentTerm->id) {
            return redirect()->back()->with('error', 'No puede copiar de la misma gestión.');
        }

        try {
            // Obtener ofertas de la gestión origen
            $sourceOfferings = CourseOffering::where('term_id', $sourceTerm)->get();
            
            if ($sourceOfferings->isEmpty()) {
                return redirect()->back()->with('warning', 'No hay ofertas para copiar en la gestión seleccionada.');
            }

            $copied = 0;
            $skipped = 0;

            foreach ($sourceOfferings as $offering) {
                // Verificar si ya existe la combinación en la gestión actual
                $exists = CourseOffering::where('term_id', $currentTerm->id)
                    ->where('subject_id', $offering->subject_id)
                    ->where('group_id', $offering->group_id)
                    ->exists();

                if (!$exists) {
                    CourseOffering::create([
                        'term_id' => $currentTerm->id,
                        'subject_id' => $offering->subject_id,
                        'group_id' => $offering->group_id,
                    ]);
                    $copied++;
                } else {
                    $skipped++;
                }
            }

            $message = "Se copiaron {$copied} ofertas correctamente.";
            if ($skipped > 0) {
                $message .= " Se omitieron {$skipped} ofertas que ya existían.";
            }

            return redirect()->route('admin.course-offerings.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al copiar ofertas: ' . $e->getMessage());
        }
    }
}