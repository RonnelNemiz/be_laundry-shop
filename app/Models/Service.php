<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'description',
        'image',
    ];
    // public function price(){
    //     return $this->belongsTo(Price::class);
    // }
    public function price(){
        return $this->hasOne(Price::class);
    }
}
