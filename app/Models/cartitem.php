<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';
    protected $primaryKey = 'cart_item_id';
    public $timestamps = false;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity'
    ];

    // 1 mục giỏ hàng thuộc về 1 giỏ hàng
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    // 1 mục giỏ hàng thuộc về 1 sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}