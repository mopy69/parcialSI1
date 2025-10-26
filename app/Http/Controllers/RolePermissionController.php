<?php

namespace App\Http\Controllers;

use App\Models\RolePermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RolePermissionRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $rolePermissions = RolePermission::paginate();

        return view('role-permission.index', compact('rolePermissions'))
            ->with('i', ($request->input('page', 1) - 1) * $rolePermissions->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $rolePermission = new RolePermission();

        return view('role-permission.create', compact('rolePermission'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RolePermissionRequest $request): RedirectResponse
    {
        RolePermission::create($request->validated());

        return Redirect::route('role-permissions.index')
            ->with('success', 'RolePermission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $rolePermission = RolePermission::find($id);

        return view('role-permission.show', compact('rolePermission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $rolePermission = RolePermission::find($id);

        return view('role-permission.edit', compact('rolePermission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RolePermissionRequest $request, RolePermission $rolePermission): RedirectResponse
    {
        $rolePermission->update($request->validated());

        return Redirect::route('role-permissions.index')
            ->with('success', 'RolePermission updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        RolePermission::find($id)->delete();

        return Redirect::route('role-permissions.index')
            ->with('success', 'RolePermission deleted successfully');
    }
}
