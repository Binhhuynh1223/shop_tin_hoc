<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'order_item_id';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal'
    ];

    // Quan hệ: 1 item thuộc về 1 order
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Quan hệ: 1 item thuộc về 1 product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
