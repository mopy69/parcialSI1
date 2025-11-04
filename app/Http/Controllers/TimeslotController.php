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
     * Muestra la lista de franjas horarias.
     */
    public function index(): View
    {
        $timeslots = Timeslot::paginate();

        // Apunta a la vista dentro del panel de admin
        return view('admin.timeslots.index', compact('timeslots'));
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario de creación.
     */
    public function create(): View
    {
        // Apunta a la vista dentro del panel de admin
        return view('admin.timeslots.create');
    }

    /**
     * Store a newly created resource in storage.
     * Guarda la nueva franja horaria.
     */
    public function store(TimeslotRequest $request): RedirectResponse
    {
        Timeslot::create($request->validated());
        return redirect()->route('admin.timeslots.index')
            ->with('success', 'Horario creado correcatamente.');
    }

    /**
     * Display the specified resource.
     * Muestra una franja horaria específica.
     */
    public function show(Timeslot $timeslot): View
    {
        // Usa Route Model Binding (Timeslot $timeslot)
        return view('admin.timeslots.show', compact('timeslot'));
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario de edición.
     */
    public function edit(Timeslot $timeslot): View
    {
        // Usa Route Model Binding (Timeslot $timeslot)
        return view('admin.timeslots.edit', compact('timeslot'));
    }

    /**
     * Update the specified resource in storage.
     * Actualiza la franja horaria.
     */
    public function update(TimeslotRequest $request, Timeslot $timeslot): RedirectResponse
    {
        $timeslot->update($request->validated());

        return Redirect::route('admin.timeslots.index')
            ->with('success', 'Franja horaria actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Elimina la franja horaria.
     */
    public function destroy(Timeslot $timeslot): RedirectResponse
    {
        // Lógica de protección (basada en tu referencia)
        // (Asegúrate de que tu modelo 'Timeslot' tenga una relación, 
        // por ejemplo 'classAssignments()', para comprobar)
        
        // if ($timeslot->classAssignments()->exists()) {
        //     return back()->with('error', 'No se puede eliminar una franja horaria que está en uso.');
        // }

        $timeslot->delete();

        return Redirect::route('admin.timeslots.index')
            ->with('success', 'Franja horaria eliminada correctamente.');
    }
}