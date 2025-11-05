<?php
// Dữ liệu $order được truyền từ OrderController
$status = $order->status;
$statusText = 'Không rõ';
$statusColorClass = 'bg-gray-100 text-gray-800'; // Default, fallback
switch ($status) {
    case 'pending':
        $statusText = 'Chờ thanh toán';
        $statusColorClass = 'bg-yellow-100 text-yellow-800';
        break;
    case 'completed':
        $statusText = 'Hoàn thành';
        $statusColorClass = 'bg-green-100 text-green-800';
        break;
    case 'cancelled':
        $statusText = 'Đã hủy';
        $statusColorClass = 'bg-red-100 text-red-800';
        break;
}
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Hóa đơn <?= $order->order_id ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background-color: white;
            }

            #print-button {
                display: none;
            }

            .no-print {
                display: none;
            }

            .max-w-4xl {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body class="bg-gray-50 print:bg-white">
    <div class="max-w-4xl mx-auto p-6 sm:p-10 my-8 bg-white shadow-xl rounded-lg border border-gray-200 print:shadow-none print:border-none">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-gray-200">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-4xl font-extrabold text-indigo-800 tracking-tight">HÓA ĐƠN</h1>
                <p class="text-gray-600 mt-2 text-sm">Mã hóa đơn: <span class="font-semibold text-gray-800">#<?= $order->order_id ?></span></p>
                <p class="text-gray-600 text-sm">Ngày đặt: <span class="font-semibold text-gray-800"><?= date('d/m/Y H:i', strtotime($order->order_date)) ?></span></p>
            </div>
            <div class="text-left sm:text-right">
                <h2 class="text-2xl font-bold text-gray-800">Shop Tin Học</h2>
                <p class="text-gray-600 text-sm">123 Đường A, Quận B, TP. XYZ</p>
                <p class="text-gray-600 text-sm">Hotline: 0123 456 789</p>
                <p class="text-gray-600 text-sm">Email: info@tinhocac.com</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Thông tin khách hàng</h3>
                <div class="text-sm space-y-2 text-gray-700">
                    <p><strong>Tên khách hàng:</strong> <span class="font-medium"><?= htmlspecialchars($order->user->full_name ?? 'N/A') ?></span></p>
                    <p><strong>Username:</strong> <span class="font-medium">@<?= htmlspecialchars($order->user->username ?? 'N/A') ?></span></p>
                    <p><strong>Email:</strong> <span class="font-medium"><?= htmlspecialchars($order->email) ?></span></p>
                    <p><strong>Điện thoại:</strong> <span class="font-medium"><?= htmlspecialchars($order->phone) ?></span></p>
                    <p><strong>Địa chỉ giao hàng:</strong> <span class="font-medium"><?= htmlspecialchars($order->shipping_address) ?></span></p>
                </div>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Thông tin thanh toán</h3>
                <div class="text-sm space-y-2 text-gray-700">
                    <p><strong>Phương thức:</strong> <span class="font-medium uppercase text-indigo-700"><?= htmlspecialchars($order->payment_method) ?></span></p>
                    <p><strong>Trạng thái đơn hàng:</strong>
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full <?= $statusColorClass ?>">
                            <?= $statusText ?>
                        </span>
                    </p>
                    <p><strong>Mã giao dịch VNPAY:</strong> <span class="font-medium"><?= htmlspecialchars($order->transaction_id ?? 'N/A (COD)') ?></span></p>
                    <p><strong>Tổng tiền:</strong> <span class="font-bold text-red-600 text-lg"><?= number_format($order->total_amount, 0, ',', '.') ?>₫</span></p>
                </div>
            </div>
        </div>

        <div class="mt-10">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Chi tiết sản phẩm</h3>
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3.5 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sản phẩm</th>
                            <th scope="col" class="py-3.5 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Số lượng</th>
                            <th scope="col" class="py-3.5 px-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Đơn giá</th>
                            <th scope="col" class="py-3.5 px-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($order->items as $item): ?>
                            <tr>
                                <td class="py-4 px-4">
                                    <p class="font-medium text-sm text-gray-900"><?= htmlspecialchars($item->product->product_name ?? 'Sản phẩm đã bị xóa/không tồn tại') ?></p>
                                    <p class="text-xs text-gray-500">ID: <?= $item->product_id ?></p>
                                </td>
                                <td class="py-4 px-4 text-center text-sm text-gray-700"><?= $item->quantity ?></td>
                                <td class="py-4 px-4 text-right text-sm text-gray-700"><?= number_format($item->price, 0, ',', '.') ?>₫</td>
                                <td class="py-4 px-4 text-right text-sm font-semibold text-gray-900"><?= number_format($item->subtotal, 0, ',', '.') ?>₫</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="py-4 px-4 text-right text-base font-bold text-gray-800">TỔNG CỘNG</td>
                            <td class="py-4 px-4 text-right text-base font-bold text-red-600">
                                <?= number_format($order->total_amount, 0, ',', '.') ?>₫
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-10 pt-8 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-600 mb-6">Cảm ơn quý khách đã tin tưởng và mua hàng tại cửa hàng của chúng tôi! Hẹn gặp lại quý khách.</p>
            <button id="print-button"
                onclick="window.print()"
                class="no-print px-8 py-3 bg-indigo-700 text-white rounded-lg font-semibold shadow-md hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                In hóa đơn
            </button>
        </div>
    </div>
</body>

</html>