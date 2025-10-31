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
        // Sửa dòng này: Đọc dữ liệu JSON từ request body
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
            if ($input['payment_method'] === 'vnpay') {
                $paymentUrl = $this->createVNPayPayment($order);
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'redirect' => $paymentUrl]);
            } else { // COD
                $order->updatePaymentStatus('processing');
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'redirect' => '/order/success/' . $order->order_id]);
            }
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            http_response_code(500);
        }
    }


    private function createVNPayPayment($order)
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_TmnCode = $_ENV['VNPAY_TMN_CODE'];
        $vnp_HashSecret = $_ENV['VNPAY_HASH_SECRET'];
        $vnp_Returnurl = "http://localhost/payment/vnpay/return";
        $vnp_TxnRef = $order->order_id;
        $vnp_OrderInfo = "Thanh toan don hang " . $order->order_id;
        $vnp_Amount = $order->total_amount * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_Command" => "pay",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => 'billpayment',
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];
        ksort($inputData);
        $query = http_build_query($inputData);
        $vnpSecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
        return $vnp_Url . '?' . $query . '&vnp_SecureHash=' . $vnpSecureHash;
    }


    public function vnpayReturn()
    {
        $input = $_GET;
        $orderId = $input['vnp_TxnRef'];
        $responseCode = $input['vnp_ResponseCode'];
        $order = Order::find($orderId);
        if (!$order) {
            $this->redirect('/cart', 'Đơn hàng không tồn tại', 'error');
        }
        if ($responseCode == '00') {
            $order->updatePaymentStatus('processing', $input['vnp_TransactionNo']);
            $this->redirect('/order/success/' . $orderId, 'Thanh toán thành công');
        } else {
            $order->updatePaymentStatus('cancelled');
            $this->redirect('/order/failure/' . $orderId, 'Thanh toán thất bại', 'error');
        }
    }


    public function vnpayIpn()
    {
        // Xử lý tương tự vnpayReturn, nhưng verify hash thủ công
        // Ví dụ đơn giản (thêm verify hash nếu cần)
        $input = $_POST; // IPN dùng POST
        $orderId = $input['vnp_TxnRef'];
        $responseCode = $input['vnp_ResponseCode'];
        $order = Order::find($orderId);
        if ($order && $responseCode == '00') {
            $order->updatePaymentStatus('processing', $input['vnp_TransactionNo']);
            header('Content-Type: application/json');
            echo json_encode(['RspCode' => '00', 'Message' => 'Confirm Success']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['RspCode' => '99', 'Message' => 'Fail']);
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
