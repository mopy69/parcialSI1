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
    public function index(): View
    {
        // Añadido ->with(...) para Eager Loading (Optimización N+1)
        $courseOfferings = CourseOffering::with(['term', 'subject', 'group'])->paginate(10);

        return view('admin.course-offerings.index', compact('courseOfferings'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create(): View
    {
        // Añadido: Carga los datos necesarios para los <select> del formulario
        $terms = Term::all();
        $subjects = Subject::all();
        $groups = Group::all();

        return view('admin.course-offerings.create', compact('terms', 'subjects', 'groups'));
    }

    /**
     * Guarda la nueva oferta de curso.
     */
    public function store(CourseOfferingRequest $request): RedirectResponse
    {
        try {
            
            // 1. Intenta crear la oferta de curso
            CourseOffering::create($request->validated());

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
    public function edit(CourseOffering $courseOffering): View // Usa RMB
    {
        // Añadido: Carga los datos necesarios para los <select> del formulario
        $terms = Term::all();
        $subjects = Subject::all();
        $groups = Group::all();

        return view('admin.course-offerings.edit', compact('courseOffering', 'terms', 'subjects', 'groups'));
    }

    /**
     * Actualiza la oferta de curso.
     */
    public function update(CourseOfferingRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $courseOffering->update($request->validated());

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
}