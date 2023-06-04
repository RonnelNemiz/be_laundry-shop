<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detergent extends Model
{
    use HasFactory;
    protected $fillable = [
        'detergent_name',
        'detergent_price',
        'detergent_scoop',
        'image',
    ];
}
