<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
// ¡Importante! Usa tu Request en 'store' y 'update'
use App\Http\Requests\ClassroomRequest; 
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ClassroomController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | paneles para las vistas del administrador, (apartado de aulas)
    |--------------------------------------------------------------------------
    */

    // panel para la gestion de aulas
    public function Index(Request $request): View
    {
        $query = Classroom::query();
        
        // Búsqueda
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nro', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }
        
        // Ordenamiento
        $sortColumn = $request->input('sort', 'nro');
        $sortDirection = $request->input('direction', 'asc');
        
        // Validar columnas permitidas para ordenar
        $allowedSorts = ['nro', 'type', 'capacity', 'created_at'];
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        }
        
        $classrooms = $query->paginate(10)->withQueryString();
        
        return view('admin.classrooms.index', compact('classrooms'));
    }

    // panel para la creacion de aulas
    public function create(): View
    {
        return view('admin.classrooms.create');
    }

    //panel que muestra un aula para editar
    public function show(Classroom $classroom): View 
    {
        return view('admin.classrooms.show', compact('classroom'));
    }

    //panel para la edicion de aulas
    public function edit(Classroom $classroom): View 
    {
        return view('admin.classrooms.edit', compact('classroom'));
    }

    /*
    |--------------------------------------------------------------------------
    | metodos que trabajan con la base de datos para las aulas
    |--------------------------------------------------------------------------
    */

    // metodo que crea el aula
    public function store(ClassroomRequest $request): RedirectResponse
    {
        Classroom::create($request->validated());

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Aula creada correctamente');
    }

    //metodo para actualizar el aula
    public function update(ClassroomRequest $request, Classroom $classroom): RedirectResponse
    {
        $classroom->update($request->validated());

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Aula actualizada correctamente');
    }

    // metodo para eliminar el aula
    public function destroy(Classroom $classroom): RedirectResponse 
    {
        if ($classroom->classAssignments()->exists()) {
            return back()->with('error', 'No se puede eliminar un aula con clases asignadas');
        }
        
        $classroom->delete();
        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Aula eliminada correctamente');
    }
}