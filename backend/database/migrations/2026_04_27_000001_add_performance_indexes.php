<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->index('status', 'customers_status_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['is_active', 'name'], 'categories_active_name_index');
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->index(['is_active', 'category_id', 'name'], 'medicines_active_category_name_index');
            $table->index('generic_name', 'medicines_generic_name_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'payment_status'], 'orders_status_payment_status_index');
            $table->index(['customer_id', 'created_at'], 'orders_customer_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_payment_status_index');
            $table->dropIndex('orders_customer_created_at_index');
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->dropIndex('medicines_active_category_name_index');
            $table->dropIndex('medicines_generic_name_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_active_name_index');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_status_index');
        });
    }
};
