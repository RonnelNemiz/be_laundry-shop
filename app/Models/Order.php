<?php

namespace App\Models;

use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected  $fillable = [
        'user_id',
        'payment_id',
        'handling_id',
        'trans_number',
        'payment_status',
        'status',
        'total',
        'approved_by',
        'created_at',
    ];
    protected function generateTransactionNumber()
    {
        $prefix = 'LAUNDRY-';
        $lastNumber = DB::table('orders')->max('trans_number');
        $lastSequence = intval(substr($lastNumber, strlen($prefix)));
        $nextSequence = $lastSequence + 1;
        $nextNumber = $prefix . str_pad($nextSequence, 6, '0', STR_PAD_LEFT);
        return $nextNumber;
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function handlings()
    {
        return $this->belongsToMany(Handling::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_user', 'order_id', 'category_id')
        ->withPivot('order_id', 'quantity', 'kilo');
    }

    public function updateStatus($newStatus)
    {
        if ($newStatus === 'in progress' && $this->status === 'pending') {
            $this->status = 'in progress';
            $this->save();
        } elseif ($newStatus === 'completed' && $this->status === 'in progress') {
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
