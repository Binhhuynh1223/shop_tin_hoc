<?php include __DIR__ . '/partials/header.php'; ?>
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 text-center">
    <h1 class="text-2xl font-bold text-red-600">Thanh toán thất bại!</h1>
    <p class="mt-4">Mã đơn hàng: <?= $order->order_id ?></p>
    <p>Vui lòng thử lại hoặc liên hệ hỗ trợ.</p>
    <a href="/cart" class="mt-6 inline-block px-5 py-3 bg-indigo-600 text-white rounded-md">Quay về giỏ hàng</a>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>