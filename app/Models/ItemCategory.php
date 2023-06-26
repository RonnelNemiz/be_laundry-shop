<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'service_id', 'price'
    ];

    public function itemTypes()
    {
        return $this->hasMany(ItemType::class, 'category_id');
    }

    public function services()
    {
        return $this->belongsTo(ItemType::class, 'id');
    }
}
