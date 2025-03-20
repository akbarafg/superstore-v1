<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Supplier;
class SupplierController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $suppliers = Supplier::all();
        
        return Inertia::render('Supplier', [
            'suppliers' => $suppliers,
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
            'sup_name' => 'required',
            'contact_person' => 'required',
            'mobile' => 'required',
            'address' => 'required'

        ]);
        Supplier::create($validatedData);
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
            'sup_name' => 'required',
            'contact_person' => 'required',
            'mobile' => 'required',
            'address' => 'required'

        ]);

        $supplier = Supplier::findOrFail($id);

        $supplier->update([
            'sup_name' => $validatedData['sup_name'],
            'contact_person' => $validatedData['contact_person'],
            'mobile' => $validatedData['mobile'],
            'address' => $validatedData['address'],

        ]);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Supplier::find($id)->delete();
        return redirect()->back();
    }
}
