<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fabcon extends Model
{
    use HasFactory;
    protected $fillable = [
        'fabcon_name',
        'fabcon_price',
        'fabcon_scoop',
        'image',
    ];
}
