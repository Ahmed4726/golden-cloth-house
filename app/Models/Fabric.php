<?php

namespace App\Models;

use Database\Factories\FabricFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fabric extends Model
{
    /** @use HasFactory<FabricFactory> */
    use HasFactory;

    protected $fillable = ['name', 'active', 'sort_order'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function kameezProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'kameez_fabric_id');
    }

    public function trouserProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'trouser_fabric_id');
    }

    public function dupattaProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'dupatta_fabric_id');
    }
}
