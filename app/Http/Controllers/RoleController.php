<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RoleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | panel para las vistas del administrador, (apartado de roles)
    |--------------------------------------------------------------------------
    */

    //panel para la gestion de roles
    public function index(Request $request): View
    {
        $roles = Role::with(['permissions'])->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    //panel para la creacion de roles
    public function create(): View
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    //panel que muestra un rol
    public function show($id): View
    {
        $role = Role::find($id);

        return view('role.show', compact('role'));
    }


    /*
    |--------------------------------------------------------------------------
    | metodos para roles que trabajan con la base de datos
    |--------------------------------------------------------------------------
    */

    // metodo que crea el rol
    public function store(RoleRequest $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $validated['name']]);
        
        if(isset($validated['permissions'])) {
            foreach($validated['permissions'] as $permissionId) {
                RolePermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permissionId
                ]);
            }
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol creado correctamente');
    }

    // metodo que edita el rol con su permiso
    public function edit(Role $role): View
    {
        $permissions = Permission::all();
        $rolePermissionIds = $role->permissions()->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    // metodo que actualiza el rol
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $validated['name']]);

        // si se cambia un rol se elimina todos los permisos antiguos para volverlos a asignar
        RolePermission::where('role_id', $role->id)->delete();        
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol actualizado correctamente');
    }

    // metodo que elimina un rol
        public function delete(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return back()->with('error', 'No se puede eliminar el rol porque tiene usuarios asociados');
        }
        
        $role->permissions()->detach();
        $role->delete();
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol eliminado correctamente');
    }

}
