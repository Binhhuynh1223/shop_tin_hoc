<?php

namespace App\Services;

class VNPAYService
{
    private $vnp_TmnCode;
    private $vnp_HashSecret;
    private $vnp_Url;
    private $vnp_ReturnUrl;
    private $vnp_IpnUrl;

    public function __construct()
    {
        // Cấu hình VNPay
        $this->vnp_TmnCode = $_ENV['VNP_TMN_CODE'];
        $this->vnp_HashSecret = $_ENV['VNP_HASH_SECRET'];
        $this->vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

        // Lấy URL cơ sở
        $baseUrl = $_ENV['APP_URL'] . $_SERVER['HTTP_HOST'];

        $this->vnp_ReturnUrl = $baseUrl . $_ENV['VNP_RETURN_URL'];
        $this->vnp_IpnUrl = $baseUrl . $_ENV['VNP_IPN_URL'];
    }

    /**
     * Tạo URL thanh toán VNPAY
     * @param int $orderId Mã đơn hàng
     * @param float $amount Số tiền
     * @param string $orderInfo Thông tin đơn hàng
     * @param string $bankCode Mã ngân hàng (nếu có)
     * @return string URL thanh toán
     */
    public function createPaymentUrl($orderId, $amount, $orderInfo, $bankCode = '')
    {
        $vnp_TxnRef = $orderId;
        $vnp_OrderInfo = $orderInfo;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $amount * 100; // VNPAY yêu cầu số tiền * 100
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $this->vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        if ($bankCode) {
            $inputData['vnp_BankCode'] = $bankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $this->vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return $vnp_Url;
    }

    /**
     * Xác thực chữ ký từ VNPAY trả về
     * @param array $vnpData Dữ liệu VNPAY trả về
     * @return bool
     */
    public function validateResponse($vnpData)
    {
        $vnp_SecureHash = $vnpData['vnp_SecureHash'];
        unset($vnpData['vnp_SecureHash']);

        ksort($vnpData);
        $hashData = "";
        $i = 0;
        foreach ($vnpData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);
        return $secureHash == $vnp_SecureHash;
    }

    /**
     * Lấy TmnCode
     */
    public function getTmnCode()
    {
        return $this->vnp_TmnCode;
    }

    /**
     * Lấy HashSecret
     */
    public function getHashSecret()
    {
        return $this->vnp_HashSecret;
    }
}
