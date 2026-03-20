<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_number',
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

    public static function booted()
    {
        static::creating(function ($order) {
            if (!$order->invoice_number) {
                $datePart = now()->format('Y/m');
                $lastOrder = static::where('invoice_number', 'like', "INV/{$datePart}/%")->orderBy('id', 'desc')->first();
                
                $sequence = 1;
                if ($lastOrder) {
                    $parts = explode('/', $lastOrder->invoice_number);
                    $sequence = (int)end($parts) + 1;
                }
                
                $order->invoice_number = "INV/{$datePart}/" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function items() {
        return $this->hasMany( OrderItem::class );
    }
}
