<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\FuncCall;

class Order extends Model
{
    use HasFactory;
    protected  $fillable=[
        'profile_id',
        'service_id',
        'payment_id',
        'price_id',
    ];
    public function order(){
        return $this->belongsTo(Profile::class);
    }
    public function reviews(){
        return $this->hasMany(Review::class);
    }
}
