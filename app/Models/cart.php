<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';
    protected $primaryKey = 'cart_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'created_at'
    ];

    // Quan hệ: 1 giỏ hàng thuộc về 1 người dùng
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Quan hệ: 1 giỏ hàng có nhiều sản phẩm
    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    // Tính tổng tiền của giỏ hàng
    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
    }
}
