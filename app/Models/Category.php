<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use HasFactory, NodeTrait;

    protected $fillable = [
        'name', 'parent_id', 'price'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'category_user', 'category_id', 'order_id')
            ->withPivot('order_id', 'quantity', 'kilo');
    }
}
