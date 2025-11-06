<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TermRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TermController extends Controller
{
   public function index(): View
    {
        $terms = Term::paginate(10);

        // Apunta a la vista dentro del panel de admin
        return view('admin.terms.index', compact('terms'));
    }

    public function create(): View
    {
        // Apunta a la vista dentro del panel de admin
        return view('admin.terms.create');
    }

    public function edit(Term $term): View
    {
        return view('admin.terms.edit', compact('term'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TermRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $validatedData['asset'] = true; 

        Term::create($validatedData);

        return Redirect::route('admin.terms.index')
            ->with('success', 'Term created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $term = Term::find($id);

        return view('admin.terms.show', compact('term'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TermRequest $request, Term $term): RedirectResponse
    {
        $term->update($request->validated());

        return Redirect::route('admin.terms.index')
            ->with('success', 'Term updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id): RedirectResponse
    {
        Term::find($id)->delete();

        return Redirect::route('admin.terms.index')
            ->with('success', 'Term deleted successfully');
    }
}
