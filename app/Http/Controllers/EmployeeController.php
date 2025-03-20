<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Employee;
class EmployeeController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $employees = Employee::all();

        return Inertia::render('Employee', [
            'employees' => $employees,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate( [
            'emp_name' => 'required',
            'position' => 'required',
            'hire_date' => 'required',
        ]);
        Employee::create($validatedData);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate( [
            'emp_name' => 'required',
            'position' => 'required',
            'hire_date' => 'required',
        ]);

        $employee = Employee::findOrFail($id);

        $employee->update([
            'emp_name' => $validatedData['emp_name'],
            'position' => $validatedData['position'],
            'hire_date' => $validatedData['hire_date'],
        ]);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Employee::find($id)->delete();
        return redirect()->back();
    }
}
