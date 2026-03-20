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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            // Relationship with categories table
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->string('name'); // Medicine trade name
            $table->string('generic_name')->nullable(); // Scientific name
            $table->string('sku_code')->unique(); // Stock Keeping Unit (Unique code)
            
            $table->decimal('buy_price', 10, 2); // Purchase price
            $table->decimal('sell_price', 10, 2); // Selling price
            
            $table->integer('stock_quantity')->default(0); // Current stock level
            $table->date('expiry_date')->nullable(); // Medicine expiration date
            
            $table->string('image')->nullable(); // Path to medicine image
            $table->boolean('is_active')->default(true); // Status toggle

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
