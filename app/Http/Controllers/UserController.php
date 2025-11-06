<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use app\Http\Controllers\RoleController;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | paneles para las vistas del administrador, (apartado de usuarios)
    |--------------------------------------------------------------------------
    */

    // panel de control del administrador
    public function dashboard(): View
    {
        $usersCount = User::count();
        $rolesCount = Role::count();
        $permissionsCount = Permission::count();

        return view('admin.dashboard', compact('usersCount', 'rolesCount', 'permissionsCount'));
    }

    // panel de gestion de usuarios
    public function Index(): View
    {
        $users = User::with(['role'])->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // panel de creación de usuarios
    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    // panel de edición de usuarios
    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }



    /*
    |--------------------------------------------------------------------------
    | metodos que trabajan con la base de datos
    |--------------------------------------------------------------------------
    */

    // funcion que crea los usuarios de forma masiva
    public function createMassive(Request $request)
    {

        if(!$request->hasFile('import_file')){
            return redirect()->back()->with('error', '¡No se ha seleccionado ningún archivo!');
        }

        $file = $request->file('import_file');

        if (!in_array($file->getClientOriginalExtension(), ['xlsx', 'xls'])) {
            return redirect()->back()->with('error', '¡El archivo debe ser de tipo Excel (xlsx, xls)!');
        }

        $file = $request->file('import_file');

        Excel::import(new UsersImport, $file);

        return redirect()->route('admin.users.index')->with('success', '¡Usuarios importados correctamente!');
    }

    // funcion que almacena el usuario creado
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id'
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id']
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente');
    }


    // funcion que actualiza el usuario editado
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id'
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    // funcion que elimina el usuario
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente');
    }
}
