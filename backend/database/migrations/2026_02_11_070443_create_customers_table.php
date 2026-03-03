<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('avatar_url')->nullable();
            
            // Structured Address Fields
            $table->string('region')->nullable();
            $table->string('township')->nullable();
            $table->string('town')->nullable();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            
            // Account Status
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};