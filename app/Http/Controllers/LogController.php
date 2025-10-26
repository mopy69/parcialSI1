<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\LogRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $logs = Log::paginate();

        return view('log.index', compact('logs'))
            ->with('i', ($request->input('page', 1) - 1) * $logs->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $log = new Log();

        return view('log.create', compact('log'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LogRequest $request): RedirectResponse
    {
        Log::create($request->validated());

        return Redirect::route('logs.index')
            ->with('success', 'Log created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $log = Log::find($id);

        return view('log.show', compact('log'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $log = Log::find($id);

        return view('log.edit', compact('log'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LogRequest $request, Log $log): RedirectResponse
    {
        $log->update($request->validated());

        return Redirect::route('logs.index')
            ->with('success', 'Log updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Log::find($id)->delete();

        return Redirect::route('logs.index')
            ->with('success', 'Log deleted successfully');
    }
}
