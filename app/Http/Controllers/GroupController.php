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
    public function index(Request $request): View
    {
        $query = Group::query();
        
        // Búsqueda
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('semester', 'like', "%{$search}%");
            });
        }
        
        // Ordenamiento
        $sortColumn = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');
        
        // Validar columnas permitidas para ordenar
        $allowedSorts = ['name', 'semester', 'created_at'];
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        }
        
        $groups = $query->paginate(10)->withQueryString();
        
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