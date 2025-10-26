<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeAreaUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\KnowledgeAreaUserRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class KnowledgeAreaUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $knowledgeAreaUsers = KnowledgeAreaUser::paginate();

        return view('knowledge-area-user.index', compact('knowledgeAreaUsers'))
            ->with('i', ($request->input('page', 1) - 1) * $knowledgeAreaUsers->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $knowledgeAreaUser = new KnowledgeAreaUser();

        return view('knowledge-area-user.create', compact('knowledgeAreaUser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KnowledgeAreaUserRequest $request): RedirectResponse
    {
        KnowledgeAreaUser::create($request->validated());

        return Redirect::route('knowledge-area-users.index')
            ->with('success', 'KnowledgeAreaUser created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $knowledgeAreaUser = KnowledgeAreaUser::find($id);

        return view('knowledge-area-user.show', compact('knowledgeAreaUser'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $knowledgeAreaUser = KnowledgeAreaUser::find($id);

        return view('knowledge-area-user.edit', compact('knowledgeAreaUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KnowledgeAreaUserRequest $request, KnowledgeAreaUser $knowledgeAreaUser): RedirectResponse
    {
        $knowledgeAreaUser->update($request->validated());

        return Redirect::route('knowledge-area-users.index')
            ->with('success', 'KnowledgeAreaUser updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        KnowledgeAreaUser::find($id)->delete();

        return Redirect::route('knowledge-area-users.index')
            ->with('success', 'KnowledgeAreaUser deleted successfully');
    }
}
