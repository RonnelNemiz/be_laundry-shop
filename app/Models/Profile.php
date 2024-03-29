<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'image',
        'purok',
        'brgy',
        'municipality',
        'contact_number',
        'land_mark',
    ];

    protected $appends = [
        'address',
    ];

    public function getAddressAttribute()
    {
        return implode(' ', [$this->purok, $this->brgy, $this->municipality]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
