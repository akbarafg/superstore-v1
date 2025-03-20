<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Customer;
class CustomerController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::all();
        return Inertia::render('Customer', [
            'customers' => $customers,
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
            'cust_name' => 'required',
            'mobile' => 'required',
            'address' => 'required'

        ]);
        Customer::create($validatedData);
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
            'cust_name' => 'required',
            'mobile' => 'required',
            'address' => 'required'

        ]);

        $customer = Customer::findOrFail($id);

        $customer->update([
            'cust_name' => $validatedData['cust_name'],
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
        Customer::find($id)->delete();
        return redirect()->back();
    }
}
