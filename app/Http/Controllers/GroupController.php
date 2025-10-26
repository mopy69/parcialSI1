<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\GroupRequest; // ¡Muy bien por usar esto!
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     * (Método 'index' en minúscula para que coincida con Route::resource)
     */
    public function index(): View
    {
        // CORREGIDO: Paginación simple y apuntando a la vista de admin
        $groups = Group::paginate(10);
        
        return view('admin.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // CORREGIDO: Ruta de la vista
        return view('admin.groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupRequest $request): RedirectResponse
    {
        // Tu lógica estaba perfecta.
        Group::create($request->validated());

        // CORREGIDO: Ruta de redirección con prefijo 'admin.'
        return redirect()->route('admin.groups.index')
            ->with('success', 'Group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group): View // CORREGIDO: Usamos Route-Model Binding
    {
        // CORREGIDO: Ruta de la vista
        return view('admin.groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group): View // CORREGIDO: Usamos Route-Model Binding
    {
        // CORREGIDO: Ruta de la vista
        return view('admin.groups.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GroupRequest $request, Group $group): RedirectResponse
    {
        // Tu lógica estaba perfecta
        $group->update($request->validated());

        // CORREGIDO: Ruta de redirección
        return redirect()->route('admin.groups.index')
            ->with('success', 'Group updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group): RedirectResponse // CORREGIDO: Usamos Route-Model Binding
    {
        // CORREGIDO: Añadimos lógica de seguridad (similar a Classroom)
        // Un Grupo (Group) se usa en 'course_offerings'
        if ($group->courseOfferings()->exists()) {
            return back()->with('error', 'Cannot delete group with associated course offerings.');
        }
        
        $group->delete();
        
        // CORREGIDO: Ruta de redirección
        return redirect()->route('admin.groups.index')
            ->with('success', 'Group deleted successfully');
    }
}