<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;

class OrderController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }
    
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::all();

        $customers = Customer::select('id','cust_name', 'mobile', 'address')->get();

        $products = Product::select('id','product_name')->get();


        return Inertia::render('Order', [
            'orders' => $orders,
            'customers' => $customers,
            'products' => $products
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
            'customer_id' => 'required',
            'product_id' => 'required',
            'ord_date' => 'required',
            'total_amount' => 'required',
            'total_amount_paid' => 'required',
            'total_amount_remains' => 'required',
            'discount_amount' => 'required'

        ]);
        Order::create($validatedData);
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
            'customer_id' => 'required',
            'product_id' => 'required',
            'ord_date' => 'required',
            'total_amount' => 'required',
            'total_amount_paid' => 'required',
            'total_amount_remains' => 'required',
            'discount_amount' => 'required'

        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'customer_id' => $validatedData['customer_id'],
            'product_id' => $validatedData['product_id'],
            'ord_date' => $validatedData['ord_date'],
            'total_amount' => $validatedData['total_amount'],
            'total_amount_paid' => $validatedData['total_amount_paid'],
            'total_amount_remains' => $validatedData['total_amount_remains'],
            'discount_amount' => $validatedData['discount_amount'],

        ]);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Order::find($id)->delete();
        return redirect()->back();
    }
}
