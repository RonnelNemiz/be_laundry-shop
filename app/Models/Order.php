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

    public function updateStatus($newStatus)
    {

        if ($newStatus === 'ready to pickup' && $this->status === 'pending') {
            $this->status = 'ready to pickup';
            $this->save();
        } elseif ($newStatus === 'in progress' && ($this->status === 'ready to pickup' || $this->status === 'pending')) {
            $this->status = 'in progress';
            $this->save();
        } elseif ($newStatus === 'ready for pickup' && $this->status === 'in progress') {
            $this->status = 'ready for pickup';
            $this->save();
        } elseif ($newStatus === 'ready to deliver' && ($this->status === 'ready for pickup' || $this->status === 'in progress')) {
            $this->status = 'ready to deliver';
            $this->save();
        } elseif ($newStatus === 'completed' && ($this->status === 'ready to deliver' || $this->status === 'ready for pickup')) {
            $this->status = 'completed';
            $this->save();
        }
    }
    public function updatePaymentStatus($newPaymentStatus)
    {
        if ($newPaymentStatus === 'paid' && $this->payment_status === 'unpaid') {
            $this->payment_status = 'paid';
            $this->save();
        }
    }
}
