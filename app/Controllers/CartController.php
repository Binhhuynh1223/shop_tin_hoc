<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartController extends BaseController
{
    // Hiển thị giỏ hàng
    public function index($userId)
    {
        $cart = Cart::where('user_id', $userId)->with(['items.product'])->first();
        if (!$cart) {
            $cart = Cart::create(['user_id' => $userId]);
        }
        $this->render('cart', ['cart' => $cart]);
    }
    // Thêm sản phẩm vào giỏ
    public function addToCart($userId, $data)
    {
        $productId = $data['product_id'];
        $quantity = $data['quantity'] ?? 1;
        $cart = Cart::firstOrCreate(['user_id' => $userId]);
        $product = Product::find($productId);
        if (!$product) {
            throw new \Exception('Sản phẩm không tồn tại');
        }
        if ($product->stock < $quantity) {
            throw new \Exception('Sản phẩm không đủ hàng');
        }
        $item = CartItem::where('cart_id', $cart->cart_id)
            ->where('product_id', $productId)
            ->first();
        if ($item) {
            if ($item->quantity + $quantity > $product->stock) {
                throw new \Exception('Sản phẩm không đủ hàng');
            }
            $item->quantity += $quantity;
            $item->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->cart_id,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }
    // Cập nhật số lượng
    public function updateQuantity($cartItemId)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $quantity = $input['quantity'] ?? 0;
        $item = CartItem::with('product')->find($cartItemId);
        if (!$item) {
            $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ'], 404);
            return;
        }
        if ($quantity > 0 && $quantity > $item->product->stock) {
            $this->jsonResponse(['success' => false, 'message' => 'Sản phẩm không đủ hàng'], 400);
            return;
        }
        if ($quantity <= 0) {
            $item->delete();
            $removed = true;
        } else {
            $item->quantity = $quantity;
            $item->save();
            $removed = false;
        }
        $cart = $item->cart;
        $cart->load('items.product');
        $total = $cart->total;
        $this->jsonResponse([
            'success' => true,
            'removed' => $removed,
            'item' => $removed ? null : [
                'id' => $item->cart_item_id,
                'quantity' => $item->quantity,
                'subtotal' => $item->quantity * $item->product->price
            ],
            'total' => $total,
            'item_count' => $cart->items->count()
        ]);
    }
    // Xóa sản phẩm
    public function remove($cartItemId)
    {
        $item = CartItem::find($cartItemId);
        if (!$item) {
            $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
            return;
        }
        $item->delete();
        $cart = $item->cart;
        $cart->load('items.product');
        $total = $cart->total;
        $this->jsonResponse([
            'success' => true,
            'total' => $total,
            'item_count' => $cart->items->count()
        ]);
    }
    // Xóa toàn bộ giỏ
    public function clear($userId)
    {
        $cart = Cart::where('user_id', $userId)->first();
        if ($cart) {
            $cart->items()->delete();
        }
        header('Location: /cart');
    }
}
