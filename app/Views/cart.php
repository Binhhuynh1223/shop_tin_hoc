<?php
// Thêm use statement cho User model
use App\Models\User;
use App\Models\Cart;

if ($_SESSION['user']['id'] ?? false) {
    $userId = $_SESSION['user']['id'];

    // Lấy giỏ hàng
    $cartModel = new Cart();
    $cart = $cartModel->where('user_id', $userId)->with(['items.product'])->first();

    // Lấy thông tin user đầy đủ từ DB
    $userModel = new User();
    $user = $userModel->find($userId);
} else {
    $cart = null;
    $user = null; // Khởi tạo user là null nếu chưa đăng nhập
}
?>
<?php include __DIR__ . '/partials/header.php'; ?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <section>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Giỏ hàng</h1>
            <a id="checkout-button" onclick="openModal()" class="inline-block px-5 py-3 rounded-md bg-indigo-600 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 <?= $cart && $cart->items->count() ? '' : 'hidden' ?>">Thanh toán</a>
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

    <!-- Modal Checkout -->
    <div id="checkout-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold">Xác nhận thanh toán</h2>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Thông tin sản phẩm -->
                <div>
                    <h3 class="font-semibold mb-4">Sản phẩm trong giỏ</h3>
                    <div class="space-y-4">
                        <?php foreach ($cart->items as $item): ?>
                            <div class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <p class="font-medium"><?= htmlspecialchars($item->product->product_name) ?></p>
                                    <p class="text-sm text-gray-600">Số lượng: <?= $item->quantity ?></p>
                                </div>
                                <p class="font-medium"><?= number_format($item->quantity * $item->product->price, 0, ',', '.') ?>₫</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 flex justify-between font-bold">
                        <span>Tổng cộng:</span>
                        <span><?= number_format($cart->getTotalAttribute(), 0, ',', '.') ?>₫</span>
                    </div>
                </div>

                <!-- Thông tin cá nhân và form -->
                <div>
                    <h3 class="font-semibold mb-4">Thông tin nhận hàng</h3>
                    <form id="checkout-form" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium">Họ và tên</label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($user->full_name ?? '') ?>" class="w-full border rounded-md p-2" reaquired>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>" class="w-full border rounded-md p-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Số điện thoại</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>" class="w-full border rounded-md p-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Địa chỉ giao hàng</label>
                            <textarea name="shipping_address" class="w-full border rounded-md p-2" required><?= htmlspecialchars($user->address ?? '') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Phương thức thanh toán</label>
                            <select name="payment_method" class="w-full border rounded-md p-2" required>
                                <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                                <!-- <option value="bank_transfer">Chuyển khoản ngân hàng</option> -->
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-md font-medium hover:bg-indigo-700">Xác nhận thanh toán</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function openModal() {
        document.getElementById('checkout-modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('checkout-modal').classList.add('hidden');
    }

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

    <?php
    // Tạo một mảng PHP với dữ liệu
    $profileData = [
        'full_name' => $user->full_name ?? '',
        'email' => $user->email ?? '',
        'phone' => $user->phone ?? '',
        'shipping_address' => $user->address ?? ''
    ];
    ?>
    // Gán đối tượng JavaScript bằng cách mã hóa JSON mảng PHP
    const profileInfo = <?php echo json_encode($profileData); ?>;

    // Tự fill vào thông tin thanh toán
    function fillProfileInfo() {
        document.querySelector('[name="full_name"]').value = profileInfo.full_name;
        document.querySelector('[name="email"]').value = profileInfo.email;
        document.querySelector('[name="phone"]').value = profileInfo.phone;
        document.querySelector('[name="shipping_address"]').value = profileInfo.shipping_address;
    }

    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        fetch('/order/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    alert(result.message);
                }
            })
            .catch(error => alert('Lỗi: ' + error));
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>