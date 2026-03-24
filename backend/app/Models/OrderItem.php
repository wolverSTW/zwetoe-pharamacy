<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    // Allow mass assignment for order item details
    protected $fillable = [
        'order_id',
        'medicine_id',
        'quantity',
        'unit_price',
        'subtotal'
    ];

    public function order() 
    {
        return $this->belongsTo(Order::class);
    }

    public function medicine() 
    {
        return $this->belongsTo(Medicine::class);
    }
}
