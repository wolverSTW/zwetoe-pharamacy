<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'shipping_method',
        'address'
    ];

    protected $casts = [
        'address' => 'array',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function items() {
        return $this->hasMany( OrderItem::class );
    }
}
