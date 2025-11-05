<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Services\VNPAYService; // Thêm use VNPAYService

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
        $paymentMethod = $input['payment_method'] ?? null;

        if (empty($input['full_name']) || empty($input['shipping_address']) || empty($paymentMethod) || empty($input['email']) || empty($input['phone'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin'], 400);
            exit;
        }

        $cart = Cart::where('user_id', $userId)->with(['items.product'])->first();
        if (!$cart || $cart->items->isEmpty()) {
            $this->jsonResponse(['success' => false, 'message' => 'Giỏ hàng trống'], 400);
            exit;
        }

        // Kiểm tra stock
        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->quantity) {
                $this->jsonResponse(['success' => false, 'message' => 'Sản phẩm ' . htmlspecialchars($item->product->product_name) . ' không đủ hàng'], 400);
                exit;
            }
        }

        try {
            // Tạo đơn hàng trước
            $order = Order::createFromCart($cart, $input);

            if ($paymentMethod === 'cod') {
                // Đơn COD sẽ ở trạng thái 'pending' chờ admin xác nhận
                $order->updatePaymentStatus('pending');
                $this->jsonResponse(['success' => true, 'redirect' => '/order/success/' . $order->order_id]);
                exit;
            }

            // Nếu là VNPAY, tạo URL thanh toán
            if ($paymentMethod === 'vnpay') {
                $vnpayService = new VNPAYService();
                $paymentUrl = $vnpayService->createPaymentUrl(
                    $order->order_id,
                    $order->total_amount,
                    "Thanh toan don hang #" . $order->order_id
                );

                // Trả về URL VNPAY để client redirect
                $this->jsonResponse(['success' => true, 'payment_url' => $paymentUrl]);
                exit;
            }

            // Các phương thức thanh toán khác (nếu có)
            $this->jsonResponse(['success' => false, 'message' => 'Phương thức thanh toán không hợp lệ'], 400);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
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
            // Hủy đơn hàng (Model đã xử lý hoàn stock)
            $order->cancel();
            $this->jsonResponse(['success' => true, 'message' => 'Hủy đơn hàng thành công.']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Chuyển trạng thái đơn hàng từ Pending -> Completed (Admin)
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

            // Chỉ cho phép cập nhật khi đang 'pending'
            if ($order->status !== 'pending') {
                $this->jsonResponse(['success' => false, 'message' => 'Chỉ có thể hoàn thành đơn hàng đang "Chờ thanh toán" (COD).'], 400);
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

    /**
     * Xử lý VNPAY Return (Client-side)
     * Người dùng sẽ được redirect về URL này sau khi thanh toán
     */
    public function vnpayReturn()
    {
        $vnpayService = new VNPAYService();
        $vnpData = $_GET;
        $orderId = $vnpData['vnp_TxnRef'] ?? null;

        if (!$orderId) {
            $this->redirect('/');
            return;
        }

        $order = Order::with('items')->find($orderId);
        if (!$order) {
            $this->redirect('/');
            return;
        }

        // Xác thực chữ ký
        if (!$vnpayService->validateResponse($vnpData)) {
            $this->redirect('/order/failure/' . $orderId);
            return;
        }

        // Kiểm tra mã phản hồi (vnp_ResponseCode)
        if ($vnpData['vnp_ResponseCode'] === '00') {
            // Kiểm tra trạng thái đơn hàng
            if ($order->status === 'pending') {
                $order->updatePaymentStatus('completed', $vnpData['vnp_TransactionNo']);
            }
            // Redirect đến thông báo đơn hàng thành công
            $this->redirect('/order/success/' . $orderId);
        } else {
            // Thanh toán thất bại -> Hủy đơn hàng và hoàn stock
            if ($order->status === 'cancelled') {
                $order->cancel();
            }
            // Hủy thanh toán
            $order->cancel();
            $this->redirect('/order/failure/' . $orderId);
        }
    }

    /**
     * Xử lý VNPAY IPN (Server-side)
     * VNPAY sẽ gọi URL này để xác nhận thanh toán
     */
    public function vnpayIpn()
    {
        $vnpayService = new VNPAYService();
        $vnpData = $_GET;

        // Phản hồi mặc định cho VNPAY
        $response = ['RspCode' => '97', 'Message' => 'Invalid Input'];

        try {
            // Kiểm tra TmnCode
            if ($vnpData['vnp_TmnCode'] !== $vnpayService->getTmnCode()) {
                $response = ['RspCode' => '01', 'Message' => 'Invalid TmnCode'];
                echo json_encode($response);
                exit;
            }

            // Kiểm tra Chữ ký
            if (!$vnpayService->validateResponse($vnpData)) {
                $response = ['RspCode' => '97', 'Message' => 'Invalid Signature'];
                echo json_encode($response);
                exit;
            }

            $orderId = $vnpData['vnp_TxnRef'];
            $order = Order::find($orderId);

            // Kiểm tra đơn hàng
            if (!$order) {
                $response = ['RspCode' => '01', 'Message' => 'Order not found'];
                echo json_encode($response);
                exit;
            }

            // Kiểm tra số tiền
            $vnpAmount = (float)$vnpData['vnp_Amount'] / 100;
            if ($vnpAmount != (float)$order->total_amount) {
                $response = ['RspCode' => '04', 'Message' => 'Invalid Amount'];
                echo json_encode($response);
                exit;
            }

            // Kiểm tra trạng thái đơn hàng (tránh xử lý lại)
            if ($order->status !== 'pending') {
                // Nếu VNPAY IPN gọi lại mà đơn đã 'completed' do vnpayReturn xử lý thì vẫn báo thành công
                if ($order->status === 'completed') {
                    $response = ['RspCode' => '00', 'Message' => 'Confirm Success (Order already completed)'];
                } else {
                    $response = ['RspCode' => '02', 'Message' => 'Order already confirmed or cancelled'];
                }
                echo json_encode($response);
                exit;
            }

            // Xử lý kết quả thanh toán
            if ($vnpData['vnp_ResponseCode'] === '00' && $vnpData['vnp_TransactionStatus'] === '00') {
                // Thành công
                $order->updatePaymentStatus('completed', $vnpData['vnp_TransactionNo']);
                $response = ['RspCode' => '00', 'Message' => 'Confirm Success'];
            } else {
                // Thất bại
                $order->cancel();
                $response = ['RspCode' => '00', 'Message' => 'Confirm Success (Failed Payment)'];
            }

            echo json_encode($response);
            exit;
        } catch (\Exception $e) {
            echo json_encode($response);
            exit;
        }
    }

    /**
     * *** HÀM TẠO HÓA ĐƠN ***
     */
    public function generateInvoice($orderId)
    {
        try {
            AuthMiddleware::requireAdmin();

            // Lấy tất cả thông tin liên quan: user, items, và product trong từng item
            $order = Order::with(['user', 'items.product'])->find($orderId);

            if (!$order) {
                echo "Không tìm thấy đơn hàng.";
                exit;
            }

            // Hiển thị hóa đơn
            $this->render('manager/invoice_template', ['order' => $order]);
        } catch (\Exception $e) {
            echo "Đã xảy ra lỗi: " . $e->getMessage();
            exit;
        }
    }
}
