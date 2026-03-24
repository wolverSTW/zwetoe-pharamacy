<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    // Ensure this is spelled correctly: $fillable (double 'l')
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Relationship: Category has many Medicines
     */
    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class);
    }
}
