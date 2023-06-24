<?php

namespace App\Models;

use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;
    protected  $fillable = [
        'user_id',
        'profile_id',
        'service_id',
        'handling_id',
        'trans_number',
        'handling_status',
        'status'
    ];
    protected function generateTransactionNumber()
    {
        $prefix = 'LAUNDRY-';
        $lastNumber = DB::table('orders')->max('trans_number');
        $lastSequence = intval(substr($lastNumber, strlen($prefix)));
        $nextSequence = $lastSequence + 1;
        $nextNumber = $prefix . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
        return $nextNumber;
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function handling(): BelongsTo
    {
        return $this->belongsTo(Handling::class, 'handling_id');
    }
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'user_id');
    }
}
