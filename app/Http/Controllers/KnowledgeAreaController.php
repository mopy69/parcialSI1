<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\KnowledgeAreaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class KnowledgeAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $knowledgeAreas = KnowledgeArea::paginate();

        return view('knowledge-area.index', compact('knowledgeAreas'))
            ->with('i', ($request->input('page', 1) - 1) * $knowledgeAreas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $knowledgeArea = new KnowledgeArea();

        return view('knowledge-area.create', compact('knowledgeArea'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KnowledgeAreaRequest $request): RedirectResponse
    {
        KnowledgeArea::create($request->validated());

        return Redirect::route('knowledge-areas.index')
            ->with('success', 'KnowledgeArea created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $knowledgeArea = KnowledgeArea::find($id);

        return view('knowledge-area.show', compact('knowledgeArea'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $knowledgeArea = KnowledgeArea::find($id);

        return view('knowledge-area.edit', compact('knowledgeArea'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KnowledgeAreaRequest $request, KnowledgeArea $knowledgeArea): RedirectResponse
    {
        $knowledgeArea->update($request->validated());

        return Redirect::route('knowledge-areas.index')
            ->with('success', 'KnowledgeArea updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        KnowledgeArea::find($id)->delete();

        return Redirect::route('knowledge-areas.index')
            ->with('success', 'KnowledgeArea deleted successfully');
    }
}
