<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;

class OrderController extends BaseController
{
    public function checkout()
    {
        AuthMiddleware::requireAuth();
        $userId = $_SESSION['user']['id'];
        $cart = Cart::where('user_id', $userId)->with(['items.product'])->first();
        if (!$cart || $cart->items->isEmpty()) {
            $this->redirect('/cart', 'Giỏ hàng trống', 'error');
        }
        $user = User::find($userId);
        $this->render('checkout', ['cart' => $cart, 'user' => $user]);
    }


    public function create()
    {
        AuthMiddleware::requireAuth();
        $userId = $_SESSION['user']['id'];
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['full_name']) || empty($input['shipping_address']) || empty($input['payment_method']) || empty($input['email']) || empty($input['phone'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
            http_response_code(400);
            exit;
        }
        $cart = Cart::where('user_id', $userId)->with(['items.product'])->first();
        if (!$cart || $cart->items->isEmpty()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
            http_response_code(400);
            exit;
        }
        // Kiểm tra stock trước khi tạo order
        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->quantity) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sản phẩm ' . htmlspecialchars($item->product->product_name) . ' không đủ hàng']);
                http_response_code(400);
                exit;
            }
        }
        try {
            $order = Order::createFromCart($cart, $input);
            $order->updatePaymentStatus('processing');
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'redirect' => '/order/success/' . $order->order_id]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            http_response_code(500);
        }
    }

    public function success($orderId)
    {
        $order = Order::with('items.product')->find($orderId);
        $this->render('order_success', ['order' => $order]);
    }
    public function failure($orderId)
    {
        $order = Order::with('items.product')->find($orderId);
        $this->render('order_failure', ['order' => $order]);
    }


    /**
     * Xử lý việc hủy đơn hàng từ người dùng
     */
    public function cancelOrder($orderId)
    {
        AuthMiddleware::requireAuth();

        // Load đơn hàng và các 'items' của nó
        $order = Order::with('items')->find($orderId);

        if (!$order) {
            $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy đơn hàng.'], 404);
            return;
        }

        // Kiểm tra đúng chủ sở hữu đơn hàng
        if ($order->user_id != $_SESSION['user']['id']) {
            $this->jsonResponse(['success' => false, 'message' => 'Không có quyền truy cập.'], 403);
            return;
        }

        try {
            // Hủy đơn hàng
            $order->cancel();
            $this->jsonResponse(['success' => true, 'message' => 'Hủy đơn hàng thành công.']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Chuyển trạng thái đơn hàng từ Processing -> Completed (Admin)
     */
    public function completeOrder($orderId)
    {
        try {
            AuthMiddleware::requireAdmin();

            $order = Order::find($orderId);
            if (!$order) {
                $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy đơn hàng'], 404);
                return;
            }

            // Chỉ cho phép cập nhật khi đang 'processing'
            if ($order->status !== 'processing') {
                $this->jsonResponse(['success' => false, 'message' => 'Chỉ có thể hoàn thành đơn hàng đang "Xử lý".'], 400);
                return;
            }

            // Cập nhật trạng thái
            $order->status = 'completed';
            $order->save();

            $this->jsonResponse(['success' => true, 'message' => 'Đã cập nhật đơn hàng thành "Hoàn thành".']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
