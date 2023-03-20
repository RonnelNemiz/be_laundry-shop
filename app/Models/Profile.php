<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'image',
        'purok',
        'brgy',
        'municipality',
        'contact_number',
        'land_mark',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function orders(){
        return $this->hasMany(Order::class);
    }
}
