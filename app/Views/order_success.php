<?php include __DIR__ . '/partials/header.php'; ?>
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 text-center">
    <h1 class="text-2xl font-bold text-green-600">Đặt hàng thành công! Bạn có thể xem thông tin đơn hàng trong trang cá nhân.</h1>
    <p class="mt-4">Mã đơn hàng: <?= $order->order_id ?></p>
    <p>Tổng tiền: <?= number_format($order->total_amount, 0, ',', '.') ?>₫</p>
    <p>Phương thức: <?= strtoupper($order->payment_method) ?></p>
    <a href="/products" class="mt-6 inline-block px-5 py-3 bg-indigo-600 text-white rounded-md">Tiếp tục mua sắm</a>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>