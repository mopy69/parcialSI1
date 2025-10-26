<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CourseOfferingRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CourseOfferingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $courseOfferings = CourseOffering::paginate();

        return view('course-offering.index', compact('courseOfferings'))
            ->with('i', ($request->input('page', 1) - 1) * $courseOfferings->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $courseOffering = new CourseOffering();

        return view('course-offering.create', compact('courseOffering'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseOfferingRequest $request): RedirectResponse
    {
        CourseOffering::create($request->validated());

        return Redirect::route('course-offerings.index')
            ->with('success', 'CourseOffering created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $courseOffering = CourseOffering::find($id);

        return view('course-offering.show', compact('courseOffering'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $courseOffering = CourseOffering::find($id);

        return view('course-offering.edit', compact('courseOffering'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseOfferingRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $courseOffering->update($request->validated());

        return Redirect::route('course-offerings.index')
            ->with('success', 'CourseOffering updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        CourseOffering::find($id)->delete();

        return Redirect::route('course-offerings.index')
            ->with('success', 'CourseOffering deleted successfully');
    }
}
