<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    // Ensure this is spelled correctly: $fillable (double 'l')
    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    /**
     * Relationship: Category has many Medicines
     */
    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class);
    }
}
