<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Employee;
use App\Models\Expense;
class ExpenseController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $employees = Employee::select('id','emp_name')->get();
        $expenses = Expense::all();
     
        return Inertia::render('Expense', [
            'employees' => $employees,
            'expenses' => $expenses,
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
            'employee_id' => 'required',
            'expense_total' => 'required',
            'expense_date' => 'required',
            'desc' => 'required',
        ]);
        Expense::create($validatedData);
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
            'employee_id' => 'required',
            'expense_total' => 'required',
            'expense_date' => 'required',
            'desc' => 'required',
        ]);

        $expense = Expense::findOrFail($id);

        $expense->update([
            'employee_id' => $validatedData['employee_id'],
            'expense_total' => $validatedData['expense_total'],
            'expense_date' => $validatedData['expense_date'],
            'desc' => $validatedData['desc'],
        ]);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Expense::find($id)->delete();
        return redirect()->back();
    }
}
