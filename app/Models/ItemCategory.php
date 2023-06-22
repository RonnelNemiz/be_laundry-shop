<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price'
    ];

    public function itemTypes()
    {
        return $this->hasMany(ItemType::class, 'parent_category');
    }
}
