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
        'service_id',
        'fabcon_id',
        'detergent_id',
        'trans_number',
        'payment_status',
        'status',
        'total',
        'ref_num',
        'change',
        'amount',
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
    public function handling()
    {
        return $this->belongsTo(Handling::class);
    }
    public function fabcon()
    {
        return $this->belongsTo(Fabcon::class);
    }
    public function detergent()
    {
        return $this->belongsTo(Detergent::class);
    }
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_user', 'order_id', 'category_id')
            ->withPivot('order_id', 'user_id', 'quantity', 'kilo');
    }

    public function updateStatus($newStatus)
    {
     
        if ($newStatus === 'ready to pickup' && $this->status === 'pending') {
            $this->status = 'ready to pickup';
            $this->save();
        }
        elseif ($newStatus === 'in progress' && ($this->status === 'ready to pickup' || $this->status === 'pending')) {
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
