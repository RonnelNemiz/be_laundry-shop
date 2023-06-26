<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'cost',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_consumables');
    }
}
