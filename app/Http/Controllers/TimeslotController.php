<?php

namespace App\Http\Controllers;

use App\Models\Timeslot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TimeslotRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TimeslotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $timeslots = Timeslot::paginate();

        return view('timeslot.index', compact('timeslots'))
            ->with('i', ($request->input('page', 1) - 1) * $timeslots->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $timeslot = new Timeslot();

        return view('timeslot.create', compact('timeslot'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TimeslotRequest $request): RedirectResponse
    {
        Timeslot::create($request->validated());

        return Redirect::route('timeslots.index')
            ->with('success', 'Timeslot created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $timeslot = Timeslot::find($id);

        return view('timeslot.show', compact('timeslot'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $timeslot = Timeslot::find($id);

        return view('timeslot.edit', compact('timeslot'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TimeslotRequest $request, Timeslot $timeslot): RedirectResponse
    {
        $timeslot->update($request->validated());

        return Redirect::route('timeslots.index')
            ->with('success', 'Timeslot updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Timeslot::find($id)->delete();

        return Redirect::route('timeslots.index')
            ->with('success', 'Timeslot deleted successfully');
    }
}
