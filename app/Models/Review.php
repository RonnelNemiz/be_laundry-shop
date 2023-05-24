<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'ratings',
        'comments',
        'reply',
        'reply_at',
    ];
    // public function user(){
    //     return $this->belongsTo(User::class)->select('id', 'first_name', 'last_name');
    // }
    public function user()
{
    return $this->belongsTo(User::class);
}

}
