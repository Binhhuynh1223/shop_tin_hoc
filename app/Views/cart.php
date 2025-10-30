<?php
if ($_SESSION['user']['id'] ?? false) {
    $userId = $_SESSION['user']['id'];
    $model = new \App\Models\Cart();
    $cart = $model->where('user_id', $userId)->with(['items.product'])->first();
} else {
    $cart = null;
}
?>
<?php include __DIR__ . '/partials/header.php'; ?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <section>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Giỏ hàng</h1>
            <a id="checkout-button" href="/checkout" class="inline-block px-5 py-3 rounded-md bg-indigo-600 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 <?= $cart && $cart->items->count() ? '' : 'hidden' ?>">Thanh toán</a>
        </div>

        <div id="cart-content">
            <?php if ($cart && $cart->items->count()): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="hidden sm:grid grid-cols-12 gap-4 p-4 bg-gray-100 font-semibold text-sm text-gray-700">
                        <div class="col-span-5">Sản phẩm</div>
                        <div class="col-span-2 text-center">Giá</div>
                        <div class="col-span-2 text-center">Số lượng</div>
                        <div class="col-span-2 text-center">Tổng</div>
                        <div class="col-span-1"></div>
                    </div>
                    <?php foreach ($cart->items as $item): ?>
                        <div id="cart-item-<?= $item->cart_item_id ?>" class="grid grid-cols-1 sm:grid-cols-12 gap-4 p-4 border-b last:border-b-0 items-center">
                            <div class="col-span-5 flex items-center gap-4">
                                <img src="<?= htmlspecialchars($item->product->image_url ?? 'https://via.placeholder.com/80') ?>" alt="<?= htmlspecialchars($item->product->product_name) ?>" class="w-20 h-20 object-cover rounded-md">
                                <div>
                                    <h3 class="font-semibold"><?= htmlspecialchars($item->product->product_name) ?></h3>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($item->product->brand) ?></p>
                                </div>
                            </div>
                            <div class="col-span-2 text-center font-medium"><?= number_format($item->product->price, 0, ',', '.') ?>₫</div>
                            <div class="col-span-2 flex items-center justify-center">
                                <input type="number" value="<?= $item->quantity ?>" min="1" class="w-16 text-center border" onchange="updateQuantity(<?= $item->cart_item_id ?>, this.value)">
                            </div>
                            <div id="subtotal-<?= $item->cart_item_id ?>" class="col-span-2 text-center font-medium"><?= number_format($item->quantity * $item->product->price, 0, ',', '.') ?>₫</div>
                            <div class="col-span-1 text-center">
                                <button onclick="removeItem(<?= $item->cart_item_id ?>)" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-6 flex justify-end">
                    <div id="cart-total" class="text-lg font-bold">Tổng cộng: <?= number_format($cart->total ?? 0, 0, ',', '.') ?>₫</div>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <p class="text-gray-600">Giỏ hàng của bạn đang trống.</p>
                    <a href="/products" class="mt-4 inline-block px-5 py-3 rounded-md bg-indigo-600 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Tiếp tục mua sắm</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    function updateQuantity(itemId, quantity) {
        if (quantity < 1) return;

        fetch(`/cart/update/${itemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.removed) {
                        document.getElementById(`cart-item-${itemId}`).remove();
                    } else {
                        const input = document.querySelector(`#cart-item-${itemId} input[type=number]`);
                        input.value = data.item.quantity;
                        document.getElementById(`subtotal-${itemId}`).textContent = new Intl.NumberFormat('vi-VN').format(data.item.subtotal) + '₫';
                    }
                    document.getElementById('cart-total').textContent = 'Tổng cộng: ' + new Intl.NumberFormat('vi-VN').format(data.total) + '₫';

                    if (data.item_count === 0) {
                        document.getElementById('cart-content').innerHTML = `
                        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                            <p class="text-gray-600">Giỏ hàng của bạn đang trống.</p>
                            <a href="/products" class="mt-4 inline-block px-5 py-3 rounded-md bg-indigo-600 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Tiếp tục mua sắm</a>
                        </div>
                    `;
                        document.getElementById('checkout-button').classList.add('hidden');
                    } else {
                        document.getElementById('checkout-button').classList.remove('hidden');
                    }
                } else {
                    alert(data.message);
                }
            });
    }

    function changeQuantity(itemId, delta) {
        const input = document.querySelector(`#cart-item-${itemId} input[type=number]`);
        const newQuantity = parseInt(input.value) + delta;
        if (newQuantity >= 1) {
            input.value = newQuantity;
            updateQuantity(itemId, newQuantity);
        }
    }

    function removeItem(itemId) {
        if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
            fetch(`/cart/remove/${itemId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`cart-item-${itemId}`).remove();
                        document.getElementById('cart-total').textContent = 'Tổng cộng: ' + new Intl.NumberFormat('vi-VN').format(data.total) + '₫';

                        if (data.item_count === 0) {
                            document.getElementById('cart-content').innerHTML = `
                            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                                <p class="text-gray-600">Giỏ hàng của bạn đang trống.</p>
                                <a href="/products" class="mt-4 inline-block px-5 py-3 rounded-md bg-indigo-600 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Tiếp tục mua sắm</a>
                            </div>
                        `;
                            document.getElementById('checkout-button').classList.add('hidden');
                        }
                    } else {
                        alert(data.message);
                    }
                });
        }
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>