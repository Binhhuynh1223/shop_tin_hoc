<?php include __DIR__ . '/partials/header.php'; ?>

<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 mb-12">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-green-600 p-6 text-white text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h1 class="text-3xl font-bold mt-4">Đặt hàng thành công!</h1>
            <p class="mt-2 text-green-100">Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đã được ghi nhận.</p>
        </div>

        <div class="p-6 md:p-8 space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b pb-3">Chi tiết đơn hàng</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                <div>
                    <p class="text-gray-500">Mã đơn hàng</p>
                    <p class="font-medium text-gray-900">#<?= htmlspecialchars($order->order_id) ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Ngày đặt</p>
                    <p class="font-medium text-gray-900"><?= date('d/m/Y H:i', strtotime($order->order_date)) ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Phương thức thanh toán</p>
                    <p class="font-medium text-gray-900 uppercase"><?= htmlspecialchars($order->payment_method) ?></p>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Thông tin giao hàng</h3>
                <div class="text-sm space-y-1 p-4 bg-gray-50 rounded-lg border">
                    <p><strong>Họ tên:</strong> <?= htmlspecialchars($order->user->full_name ?? $order->email) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order->email) ?></p>
                    <p><strong>Điện thoại:</strong> <?= htmlspecialchars($order->phone) ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order->shipping_address) ?></p>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Sản phẩm đã đặt</h3>
                <div class="space-y-4">
                    <?php if (isset($order->items) && $order->items->count() > 0): ?>
                        <?php foreach ($order->items as $item): ?>
                            <div class="flex items-center gap-4 border-b pb-4 last:border-b-0 last:pb-0">
                                <img src="<?= htmlspecialchars($item->product->image_url ?? '') ?>"
                                    alt="<?= htmlspecialchars($item->product->product_name ?? 'Sản phẩm') ?>"
                                    class="w-16 h-16 object-cover rounded-md border">
                                <div class="flex-grow">
                                    <p class="font-medium"><?= htmlspecialchars($item->product->product_name ?? 'Sản phẩm đã bị xóa') ?></p>
                                    <p class="text-sm text-gray-600">Số lượng: <?= $item->quantity ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium"><?= number_format($item->subtotal, 0, ',', '.') ?>₫</p>
                                    <p class="text-sm text-gray-500">(<?= number_format($item->price, 0, ',', '.') ?>₫ x <?= $item->quantity ?>)</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">Không thể tải chi tiết sản phẩm.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="border-t pt-4 text-right">
                <p class="text-lg font-bold text-gray-800">
                    Tổng cộng:
                    <span class="text-2xl font-bold text-red-600 ml-2"><?= number_format($order->total_amount, 0, ',', '.') ?>₫</span>
                </p>
            </div>

            <div class="flex flex-col sm:flex-row justify-center gap-4 pt-4 border-t">
                <a href="/products" class="w-full sm:w-auto text-center px-6 py-3 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700 focus-ring">
                    Tiếp tục mua sắm
                </a>
                <a href="/profile" class="w-full sm:w-auto text-center px-6 py-3 bg-gray-200 text-gray-800 rounded-md font-medium hover:bg-gray-300 focus-ring">
                    Xem lịch sử đơn hàng
                </a>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>