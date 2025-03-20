<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'shopping_id',
        'product_id',
        'buy_price',
        'sales_price',
        'stock_quantity',
        'sub_total',
        'expire_date',
        'considetation'
    ];
}
