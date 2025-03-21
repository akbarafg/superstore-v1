<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'product_name',
        'barcode',
        'company', 
        'stock_quantity',
        'stock_amount'
    ];

}
