<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\GroupRequest; 
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class GroupController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | paneles para las vistas del administrador, (apartado de grupos)
    |--------------------------------------------------------------------------
    */

    //panel para la gestion de grupos
    public function index(): View
    {
        $groups = Group::paginate(10);
        
        return view('admin.groups.index', compact('groups'));
    }

    // panel para la creacion de grupos
    public function create(): View
    {
        return view('admin.groups.create');
    }

    //panel para mostrar un grupo
    public function show(Group $group): View 
    {
        return view('admin.groups.show', compact('group'));
    }

    // panel para la edicion de grupo
    public function edit(Group $group): View 
    {
        return view('admin.groups.edit', compact('group'));
    }


    /*
    |--------------------------------------------------------------------------
    | metodos que trabajan con la base de datos para los grupos
    |--------------------------------------------------------------------------
    */

    // metodo para la creacion de grupos
    public function store(GroupRequest $request): RedirectResponse
    {
        Group::create($request->validated());
        return redirect()->route('admin.groups.index')
            ->with('success', 'Group created successfully.');
    }

    
    // metodo para la edcion de grupos
    public function update(GroupRequest $request, Group $group): RedirectResponse
    {
        $group->update($request->validated());

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group updated successfully');
    }

    // metodo para eliminar un grupo
    public function destroy(Group $group): RedirectResponse 
    {
        if ($group->courseOfferings()->exists()) {
            return back()->with('error', 'Cannot delete group with associated course offerings.');
        }
        
        $group->delete();
        return redirect()->route('admin.groups.index')
            ->with('success', 'Group deleted successfully');
    }
}