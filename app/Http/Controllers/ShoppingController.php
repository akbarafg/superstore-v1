<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Shopping;
use App\Models\Supplier;
class ShoppingController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $suppliers = Supplier::select('id','sup_name')->get();
        
        $shoppings = Shopping::all();

        return Inertia::render('Shopping', [
            'suppliers' => $suppliers,
            'shoppings' => $shoppings
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
            'supplier_id' => 'required',
            'total_amount' => 'required',
            'total_amount_paid' => 'required',

        ]);
        Shopping::create($validatedData);
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
            'supplier_id' => 'required',
            'total_amount' => 'required',
            'total_amount_paid' => 'required',

        ]);

        $shopping = Shopping::findOrFail($id);

        $shopping->update([
            'supplier_id' => $validatedData['supplier_id'],
            'total_amount' => $validatedData['total_amount'],
            'total_amount_paid' => $validatedData['total_amount_paid'],
        ]);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Shopping::find($id)->delete();
        return redirect()->back();
    }
}
