<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'payment_method_id',
        'tendered',
        'change',
        'staff_id',
        'status'
    ];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
