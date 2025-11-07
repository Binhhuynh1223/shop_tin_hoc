<?php

use App\Middleware\AuthMiddleware;
use App\Models\User;
use App\Models\Order;

// Require authentication
AuthMiddleware::requireAuth();

$model = new User();
$user = $model->find($_SESSION['user']['id']);

// Lấy lịch sử đơn hàng, sắp xếp mới nhất lên trước
$orders = Order::where('user_id', $_SESSION['user']['id'])
    ->with('items.product')
    ->orderBy('order_date', 'desc')
    ->get();

include __DIR__ . '/partials/header.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <section class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold mb-6">Thông tin cá nhân</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Avatar section -->
            <div class="text-center">
                <img
                    id="avatar-preview"
                    src="<?= htmlspecialchars($user->avatar ?? 'https://tse4.mm.bing.net/th/id/OIP.ouH9rIJGbsiTDhM6JVKy8AHaHa') ?>"
                    alt="Avatar"
                    class="w-32 h-32 rounded-full mx-auto object-cover mb-4" />
                <label for="avatar-upload" class="cursor-pointer inline-block px-4 py-2 bg-accent text-white rounded-md text-sm font-medium hover:bg-accent-dark focus-ring">
                    Tải ảnh lên
                </label>
                <p class="text-sm text-red-600 mt-2">*.jpg, .png, tối đa 2MB</p>
            </div>

            <!-- Information form -->
            <div class="md:col-span-2">
                <form action="/profile/update" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="file" name="avatar" accept="image/*" class="hidden" id="avatar-upload" />

                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700">Tên đăng nhập</label>
                        <input
                            type="text"
                            id="username"
                            value="<?= htmlspecialchars($user->username) ?>"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-accent focus:ring-accent sm:text-sm bg-slate-100 cursor-not-allowed"
                            disabled />
                    </div>

                    <div>
                        <label for="full_name" class="block text-sm font-medium text-slate-700">Họ và tên</label>
                        <input
                            type="text"
                            name="full_name"
                            id="full_name"
                            value="<?= htmlspecialchars($user->full_name ?? '') ?>"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-accent focus:ring-accent sm:text-sm" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="<?= htmlspecialchars($user->email ?? '') ?>"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-accent focus:ring-accent sm:text-sm" />
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700">Số điện thoại</label>
                        <input
                            type="tel"
                            name="phone"
                            id="phone"
                            value="<?= htmlspecialchars($user->phone ?? '') ?>"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-accent focus:ring-accent sm:text-sm" />
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-slate-700">Địa chỉ (dùng cho giao hàng)</label>
                        <textarea
                            name="address"
                            id="address"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-accent focus:ring-accent sm:text-sm"><?= htmlspecialchars($user->address ?? '') ?></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="px-6 py-2 bg-accent text-white rounded-md font-medium hover:bg-accent-dark focus-ring">
                            Cập nhật thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-lg shadow-sm p-6 mt-8">
        <h2 id="order-history" class="text-xl font-bold mb-6">Lịch sử đơn hàng</h2>

        <div class="space-y-6">
            <?php if ($orders->isEmpty()): ?>
                <p class="text-gray-600">Bạn chưa có đơn hàng nào.</p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                    // Trạng thái đơn hàng
                    $status = $order->status;

                    switch ($status) {
                        case 'pending':
                            $statusText = 'Chờ thanh toán';
                            $statusColorClass = 'bg-yellow-100 text-yellow-800';
                            break;
                        case 'completed':
                            $statusText = 'Đã hoàn thành';
                            $statusColorClass = 'bg-green-100 text-green-800';
                            break;
                        case 'cancelled':
                            $statusText = 'Đã hủy';
                            $statusColorClass = 'bg-red-100 text-red-800';
                            break;
                    }
                    ?>

                    <div class="border rounded-lg overflow-hidden shadow-md">

                        <div class="bg-gray-50 p-4 border-b flex flex-col md:flex-row justify-between md:items-center gap-4">

                            <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                                <div>
                                    <p class="text-gray-500">Mã đơn hàng</p>
                                    <p class="font-medium text-gray-900">#<?= $order->order_id ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Ngày đặt</p>
                                    <p class="font-medium text-gray-900"><?= date('d/m/Y H:i', strtotime($order->order_date)) ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Tổng tiền</p>
                                    <p class="font-medium text-red-600"><?= number_format($order->total_amount, 0, ',', '.') ?>₫</p>
                                </div>
                            </div>

                            <div class="flex-shrink-0 flex flex-col items-start md:items-end gap-2">
                                <span id="status-<?= $order->order_id ?>" class="px-3 py-1 text-sm font-medium rounded-full <?= $statusColorClass ?>">
                                    <?= $statusText ?>
                                </span>

                                <?php if ($order->status === 'pending'): ?>
                                    <button
                                        id="cancel-btn-<?= $order->order_id ?>"
                                        onclick="cancelOrder(<?= $order->order_id ?>)"
                                        class="px-3 py-1 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus-ring">
                                        Hủy đơn
                                    </button>
                                <?php endif; ?>
                            </div>

                        </div>

                        <div class="p-4 space-y-4">
                            <?php foreach ($order->items as $item): ?>
                                <div class="flex items-center gap-4">
                                    <img src="<?= htmlspecialchars($item->product->image_url ?? '') ?>"
                                        alt="<?= htmlspecialchars($item->product->product_name ?? 'Sản phẩm') ?>"
                                        class="w-16 h-16 object-cover rounded-md border">
                                    <div class="flex-grow">
                                        <p class="font-medium"><?= htmlspecialchars($item->product->product_name ?? 'Sản phẩm đã bị xóa') ?></p>
                                        <p class="text-sm text-gray-600">Số lượng: <?= $item->quantity ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium"><?= number_format($item->price, 0, ',', '.') ?>₫</p>
                                        <p class="text-sm text-gray-500">Subtotal: <?= number_format($item->subtotal, 0, ',', '.') ?>₫</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    // Preview avatar
    const uploadInput = document.getElementById('avatar-upload');
    const previewImg = document.getElementById('avatar-preview');

    uploadInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Hàm hủy đơn hàng
    function cancelOrder(orderId) {
        if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.')) {
            return;
        }

        const button = document.getElementById(`cancel-btn-${orderId}`);
        button.disabled = true;
        button.textContent = 'Đang hủy...';

        fetch(`/order/cancel/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {

                    // Cập nhật trạng thái
                    const statusSpan = document.getElementById(`status-${orderId}`);
                    statusSpan.textContent = 'Đã hủy';
                    statusSpan.className = 'px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800';

                    // Xóa nút hủy
                    if (button) {
                        button.remove();
                    }
                } else {
                    alert('Lỗi: ' + data.message);
                    // Hiện lại nút nếu hủy thất bại
                    button.disabled = false;
                    button.textContent = 'Hủy đơn';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi kết nối. Vui lòng thử lại.');
                // Hiện lại nút nếu có lỗi
                button.disabled = false;
                button.textContent = 'Hủy đơn';
            });
    }
</script>

<?php
include __DIR__ . '/partials/footer.php';
?>