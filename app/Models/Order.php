<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'product_id',
        'ord_date',
        'total_amount',
        'total_amount_paid',
        'total_amount_remains',
        'discount_amount'
    ];
}
