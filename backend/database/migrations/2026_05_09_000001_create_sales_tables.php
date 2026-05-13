<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $row) {
            $row->id();
            $row->string('invoice_number')->unique();
            $row->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $row->decimal('total_amount', 12, 2)->default(0);
            $row->decimal('discount', 12, 2)->default(0);
            $row->decimal('payable_amount', 12, 2)->default(0);
            $row->string('payment_method')->default('cash');
            $row->string('status')->default('completed');
            $row->text('note')->nullable();
            $row->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $row) {
            $row->id();
            $row->foreignId('sale_id')->constrained()->onDelete('cascade');
            $row->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $row->integer('quantity');
            $row->decimal('unit_price', 12, 2);
            $row->decimal('subtotal', 12, 2);
            $row->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
