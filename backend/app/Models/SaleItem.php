<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 
        'medicine_id', 
        'quantity', 
        'unit_price', 
        'subtotal'
    ];

    public function medicine() {
        return $this->belongsTo(Medicine::class);
    }

    // Automatically update medicine stock quantity when a sale item is created
    protected static function booted()
    {
        static::created(function ($saleItem) {
            $medicine = $saleItem->medicine;
            $medicine->decrement('stock_quantity', $saleItem->quantity);
        });
    }
}
