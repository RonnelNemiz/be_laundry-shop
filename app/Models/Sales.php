<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Sales extends Model
{
    use HasFactory;
    protected $fillable = [
        'total_sales',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
