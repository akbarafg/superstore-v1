<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\OrderDetail;
class OrderDetailsController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $orders = Order::select('id')->get();

        $order_details = OrderDetail::all();

        return Inertia::render('OrderDetail', [
            'order_details' => $order_details,
            'orders' => $orders
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
            'order_id' => 'required',
            'order_quantity' => 'required',
            'sub_total' => 'required',

        ]);
        OrderDetail::create($validatedData);
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
            'order_id' => 'required',
            'order_quantity' => 'required',
            'sub_total' => 'required',

        ]);

        $order_details = OrderDetail::findOrFail($id);

        $order_details->update([
            'order_id' => $validatedData['order_id'],
            'order_quantity' => $validatedData['order_quantity'],
            'sub_total' => $validatedData['sub_total'],
        ]);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        OrderDetail::find($id)->delete();
        return redirect()->back();
    }
}
