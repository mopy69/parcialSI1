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
    /*
    |--------------------------------------------------------------------------
    | panel para las vistas del administrador, (apartado de materias)
    |--------------------------------------------------------------------------
    */

    // panel para la gestion de materias
    public function index(): View
    {
        $subjects = Subject::paginate(10);
        return view('admin.subjects.index', compact('subjects'));
    }

    // panel para la creacion de materias
    public function create(): View
    {
        return view('admin.subjects.create');
    }

    // metodo para crear la materia
    public function store(SubjectRequest $request): RedirectResponse
    {
        Subject::create($request->validated());
        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    // panel para mostrar una materia
    public function show(Subject $subject): View 
    {
        return view('admin.subjects.show', compact('subject'));
    }

    // panel para la edicion de materias
    public function edit(Subject $subject): View
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    // metodo para actualizar una materia
    public function update(SubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully');
    }

    // metodo para eliminar una materia
    public function destroy(Subject $subject): RedirectResponse 
    {
        if ($subject->courseOfferings()->exists()) {
            return back()->with('error', 'Cannot delete subject with associated course offerings.');
        }
        
        $subject->delete();
        
        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully');
    }
}