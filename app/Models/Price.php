<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;
    protected $fillable=[
        'price_id',
        'price_value',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
