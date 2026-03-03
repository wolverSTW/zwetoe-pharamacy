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
        'sku',
        'buying_price',
        'price',
        'stock_quantity',
        'expiry_date',
        'image',
        'is_active',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected $appends = ['full_image_url'];
    public function getFullImageUrlAttribute() {
    return $this->image ? asset('storage/' . str_replace('\\', '/', $this->image)) : null;
}
}