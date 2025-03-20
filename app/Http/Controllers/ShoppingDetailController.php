<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Shopping;
use App\Models\Product;
use App\Models\ShoppingDetail;

class ShoppingDetailController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $shoppings = Shopping::select('total_amount')->get();

        $products = Product::select('id','product_name')->get();

        $shopping_details = ShoppingDetail::all();

        return Inertia::render('ShoppingDetail', [
            'shoppings' => $shoppings,
            'products' => $products,
            'shopping_details' => $shopping_details
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
            'shopping_id' => 'required',
            'product_id' => 'required',
            'buy_price' => 'required',
            'sales_price' => 'required',
            'stock_quantity' => 'required',
            'sub_total' => 'required',
            'expire_date' => 'required',
            'considetation' => 'required'
        ]);
        ShoppingDetail::create($validatedData);
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
            'shopping_id' => 'required',
            'product_id' => 'required',
            'buy_price' => 'required',
            'sales_price' => 'required',
            'stock_quantity' => 'required',
            'sub_total' => 'required',
            'expire_date' => 'required',
            'considetation' => 'required'
        ]);

        $shopping_detail = ShoppingDetail::findOrFail($id);

        $shopping_detail->update([
            'shopping_id' => $validatedData['shopping_id'],
            'product_id' => $validatedData['product_id'],
            'buy_price' => $validatedData['buy_price'],
            'sales_price' => $validatedData['sales_price'],
            'stock_quantity' => $validatedData['stock_quantity'],
            'sub_total' => $validatedData['sub_total'],
            'expire_date' => $validatedData['expire_date'],
            'considetation' => $validatedData['considetation']
        ]);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ShoppingDetail::find($id)->delete();
        return redirect()->back();
    }
}
