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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('avatar_url')->nullable();
            
            // Delivery Address Details
            $table->string('region')->nullable();
            $table->string('township')->nullable();
            $table->string('town')->nullable();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();

            // Customer Tracking
            $table->string('status')->default('active'); // active, pending, rejected
            $table->text('reject_reason')->nullable();
            $table->decimal('total_spent', 15, 2)->default(0);
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
