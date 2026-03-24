<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SaleItem;

class Sale extends Model
{
    protected $fillable = [
        'invoice_number', 
        'customer_id', 
        'total_amount', 
        'discount', 
        'payable_amount', 
        'payment_method', 
        'status', 
        'note'
        ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function items() {
        return $this->hasMany(SaleItem::class);
    }
}