<?php

namespace App\Http\Controllers;

use App\Models\TeacherAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TeacherAttendanceRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TeacherAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $teacherAttendances = TeacherAttendance::paginate();

        return view('teacher-attendance.index', compact('teacherAttendances'))
            ->with('i', ($request->input('page', 1) - 1) * $teacherAttendances->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $teacherAttendance = new TeacherAttendance();

        return view('teacher-attendance.create', compact('teacherAttendance'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeacherAttendanceRequest $request): RedirectResponse
    {
        TeacherAttendance::create($request->validated());

        return Redirect::route('teacher-attendances.index')
            ->with('success', 'TeacherAttendance created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $teacherAttendance = TeacherAttendance::find($id);

        return view('teacher-attendance.show', compact('teacherAttendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $teacherAttendance = TeacherAttendance::find($id);

        return view('teacher-attendance.edit', compact('teacherAttendance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeacherAttendanceRequest $request, TeacherAttendance $teacherAttendance): RedirectResponse
    {
        $teacherAttendance->update($request->validated());

        return Redirect::route('teacher-attendances.index')
            ->with('success', 'TeacherAttendance updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        TeacherAttendance::find($id)->delete();

        return Redirect::route('teacher-attendances.index')
            ->with('success', 'TeacherAttendance deleted successfully');
    }
}
