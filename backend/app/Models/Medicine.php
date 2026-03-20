<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medicine extends Model
{
    // Add all the fields we defined in the migration
    protected $fillable = [
        'category_id',
        'name',
        'generic_name',
        'sku_code',
        'buy_price',
        'sell_price',
        'stock_quantity',
        'expiry_date',
        'image',
        'is_active',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected $appends = ['image_url', 'full_image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . str_replace('\\', '/', $this->image)) : null;
    }

    public function getFullImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . str_replace('\\', '/', $this->image)) : null;
    }
}