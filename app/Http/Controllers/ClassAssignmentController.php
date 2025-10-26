<?php

namespace App\Http\Controllers;

use App\Models\ClassAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ClassAssignmentRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ClassAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $classAssignments = ClassAssignment::paginate();

        return view('class-assignment.index', compact('classAssignments'))
            ->with('i', ($request->input('page', 1) - 1) * $classAssignments->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $classAssignment = new ClassAssignment();

        return view('class-assignment.create', compact('classAssignment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClassAssignmentRequest $request): RedirectResponse
    {
        ClassAssignment::create($request->validated());

        return Redirect::route('class-assignments.index')
            ->with('success', 'ClassAssignment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $classAssignment = ClassAssignment::find($id);

        return view('class-assignment.show', compact('classAssignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $classAssignment = ClassAssignment::find($id);

        return view('class-assignment.edit', compact('classAssignment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClassAssignmentRequest $request, ClassAssignment $classAssignment): RedirectResponse
    {
        $classAssignment->update($request->validated());

        return Redirect::route('class-assignments.index')
            ->with('success', 'ClassAssignment updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        ClassAssignment::find($id)->delete();

        return Redirect::route('class-assignments.index')
            ->with('success', 'ClassAssignment deleted successfully');
    }
}
