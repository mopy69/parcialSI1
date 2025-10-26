<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\SubjectRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     * (Nombre de método 'index' en minúscula)
     */
    public function index(): View
    {
        // CORREGIDO: Paginación simple y apuntando a la vista de admin
        $subjects = Subject::paginate(10);

        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // CORREGIDO: Ruta de la vista
        return view('admin.subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubjectRequest $request): RedirectResponse
    {
        // Tu lógica estaba perfecta.
        Subject::create($request->validated());

        // CORREGIDO: Ruta de redirección con prefijo 'admin.'
        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject): View // CORREGIDO: Usamos Route-Model Binding
    {
        // CORREGIDO: Ruta de la vista
        return view('admin.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject): View // CORREGIDO: Usamos Route-Model Binding
    {
        // CORREGIDO: Ruta de la vista
        return view('admin.subjects.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubjectRequest $request, Subject $subject): RedirectResponse
    {
        // Tu lógica estaba perfecta
        $subject->update($request->validated());

        // CORREGIDO: Ruta de redirección
        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject): RedirectResponse // CORREGIDO: Usamos Route-Model Binding
    {
        // CORREGIDO: Añadimos lógica de seguridad
        // Chequeamos si la materia (Subject) está siendo usada en 'course_offerings'
        if ($subject->courseOfferings()->exists()) {
            return back()->with('error', 'Cannot delete subject with associated course offerings.');
        }
        
        $subject->delete();
        
        // CORREGIDO: Ruta de redirección
        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully');
    }
}