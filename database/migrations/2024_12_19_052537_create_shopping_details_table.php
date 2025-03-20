<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shopping_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_id')->constrained('shoppings');
            $table->foreignId('product_id')->constrained('products');
            $table->string('buy_price');
            $table->string('sales_price');
            $table->string('stock_quantity');
            $table->string('sub_total');
            $table->date('expire_date');
            $table->string('considetation');
            $table->timestamps();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_details');
    }
};
