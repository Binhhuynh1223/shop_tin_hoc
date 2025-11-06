<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'payment_method',
        'shipping_address',
        'email',
        'phone',
        'transaction_id',
        'order_date'
    ];

    // Quan hệ: 1 order thuộc về 1 user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Quan hệ: 1 order có nhiều items
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // Tạo order từ cart
    public static function createFromCart($cart, $data)
    {
        Capsule::beginTransaction();
        try {
            $order = self::create([
                'user_id' => $cart->user_id,
                'total_amount' => $cart->getTotalAttribute(),
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'shipping_address' => $data['shipping_address'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'order_date' => date('Y-m-d H:i:s'),
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price, // Lưu giá tại thời điểm order
                    'subtotal' => $item->quantity * $item->product->price,
                ]);
                // Giảm stock
                $product = Product::find($item->product_id);
                $product->stock -= $item->quantity;
                $product->save();
            }

            // Xóa cart
            $cart->items()->delete();

            Capsule::commit();
            return $order;
        } catch (\Exception $e) {
            Capsule::rollback();
            throw $e;
        }
    }

    // Cập nhật status sau payment
    public function updatePaymentStatus($status, $transactionId = null)
    {
        $this->status = $status;
        if ($transactionId) {
            $this->transaction_id = $transactionId;
        }
        return $this->save();
    }

    /**
     * Hủy đơn hàng, hoàn trả stock VÀ hoàn trả item về giỏ hàng.
     */
    public function cancel()
    {
        // Chỉ cho phép hủy đơn hàng đang 'pending'
        if ($this->status !== 'pending') {
            throw new \Exception('Không thể hủy đơn hàng ở trạng thái này.');
        }

        Capsule::beginTransaction();
        try {
            // Lấy hoặc tạo giỏ hàng cho user
            $cart = Cart::firstOrCreate(['user_id' => $this->user_id]);

            // Hoàn trả stock VÀ thêm lại sản phẩm vào giỏ hàng
            foreach ($this->items as $item) {

                // Hoàn trả stock
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->save();
                }

                // Kiểm tra xem item này đã có trong giỏ hàng chưa
                $cartItem = CartItem::where('cart_id', $cart->cart_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($cartItem) {
                    // Nếu đã có, cộng dồn số lượng
                    $cartItem->quantity += $item->quantity;
                    $cartItem->save();
                } else {
                    // Nếu chưa có, tạo mới (hoàn trả về giỏ hàng)
                    CartItem::create([
                        'cart_id' => $cart->cart_id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                    ]);
                }
            }

            // Xóa đơn hàng
            $this->delete();

            Capsule::commit();
            return true;
        } catch (\Exception $e) {
            Capsule::rollback();
            // Ghi log lỗi để debug
            Log::error('Lỗi khi hủy đơn hàng: ' . $e->getMessage());
            throw new \Exception('Lỗi hệ thống khi hủy đơn hàng.');
        }
    }
}
