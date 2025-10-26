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
    /**
     * Display a listing of the resource.
     */
    public function Index(): View
    {
        // ERROR CORREGIDO: La variable debe ser 'classrooms'
        $classrooms = Classroom::paginate(10);
        
        // ERROR CORREGIDO: Debes pasar 'classrooms' a la vista
        return view('admin.classrooms.index', compact('classrooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // (Esto estaba bien)
        return view('admin.classrooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // CORREGIDO: Usamos ClassroomRequest para validar
    public function store(ClassroomRequest $request): RedirectResponse
    {
        // Ya no necesitas validar aquí, ClassroomRequest lo hace.
        // La migración usa 'nro_aula' y 'tipo', tu request usa 'nro' y 'type'
        // ¡Asegúrate de que coincidan!
        
        Classroom::create($request->validated());

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Classroom created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom): View // CORREGIDO: Usa Route-Model Binding
    {
        // CORREGIDO: La vista debe estar en admin.classrooms.show
        return view('admin.classrooms.show', compact('classroom'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom): View // CORREGIDO: El nombre era 'permission'
    {
        // CORREGIDO: Pasamos la variable correcta
        return view('admin.classrooms.edit', compact('classroom'));
    }

    /**
     * Update the specified resource in storage.
     */
    // ¡¡ESTE ES EL MÉTODO QUE PEDISTE CORREGIR!!
    public function update(ClassroomRequest $request, Classroom $classroom): RedirectResponse
    {
        // Actualiza el aula con los datos validados
        $classroom->update($request->validated());

        // Redirige al índice de aulas
        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Classroom updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     * (Cambiado de 'delete' a 'destroy' para que coincida con Route::resource)
     */
    public function destroy(Classroom $classroom): RedirectResponse // CORREGIDO: El nombre era 'permission'
    {
        // CORREGIDO: Lógica copiada de PermissionController
        // Una aula (Classroom) no tiene roles.
        // Debemos chequear si tiene 'classAssignments'
        if ($classroom->classAssignments()->exists()) {
            return back()->with('error', 'Cannot delete classroom with associated classes');
        }
        
        $classroom->delete();
        
        // CORREGIDO: Redirigía a 'admin.permissions.index'
        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Classroom deleted successfully');
    }
}