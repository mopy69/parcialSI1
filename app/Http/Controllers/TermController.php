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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $terms = Term::paginate();

        return view('term.index', compact('terms'))
            ->with('i', ($request->input('page', 1) - 1) * $terms->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $term = new Term();

        return view('term.create', compact('term'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TermRequest $request): RedirectResponse
    {
        Term::create($request->validated());

        return Redirect::route('terms.index')
            ->with('success', 'Term created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $term = Term::find($id);

        return view('term.show', compact('term'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $term = Term::find($id);

        return view('term.edit', compact('term'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TermRequest $request, Term $term): RedirectResponse
    {
        $term->update($request->validated());

        return Redirect::route('terms.index')
            ->with('success', 'Term updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Term::find($id)->delete();

        return Redirect::route('terms.index')
            ->with('success', 'Term deleted successfully');
    }
}
