<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
class ProductController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $categories = Category::select('id','cate_name')->get();

        $suppliers = Supplier::select('id','sup_name')->get();

        $products = Product::all();
     

        return Inertia::render('Product', [
            'categories' => $categories,
            'suppliers' => $suppliers,
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
            'category_id' => 'required',
            'supplier_id' => 'required',
            'product_name' => 'required',
            'barcode' => 'required',
            'company' => 'required',
            'stock_quantity' => 'required',
            'stock_amount' => 'required',

        ]);
        Product::create($validatedData);
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
            'category_id' => 'required',
            'supplier_id' => 'required',
            'product_name' => 'required',
            'barcode' => 'required',
            'company' => 'required',
            'stock_quantity' => 'required',
            'stock_amount' => 'required',

        ]);

        $product = Product::findOrFail($id);

        $product->update([
            'category_id' => $validatedData['category_id'],
            'supplier_id' => $validatedData['supplier_id'],
            'product_name' => $validatedData['product_name'],
            'barcode' => $validatedData['barcode'],
            'company' => $validatedData['company'],
            'stock_quantity' => $validatedData['stock_quantity'],
            'stock_amount' => $validatedData['stock_amount'],

        ]);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Product::find($id)->delete();
        return redirect()->back();
    }
}
