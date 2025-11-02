<section class="p-6 lg:p-8 border-t border-gray-200">
    <header class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Quản lý Đơn hàng</h2>
    </header>

    <!-- Hiển thị các đơn hàng -->
    <div class="space-y-6">

        <?php if ($orders->isEmpty()): ?>
            <p class="text-center text-gray-600">Không tìm thấy đơn hàng nào phù hợp.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <?php
                $status = $order->status;
                $statusText = 'Không rõ';
                $statusColorClass = 'bg-gray-100 text-gray-800';
                switch ($status) {
                    case 'pending':
                        $statusText = 'Chờ thanh toán';
                        $statusColorClass = 'bg-yellow-100 text-yellow-800';
                        break;
                    case 'processing':
                        $statusText = 'Đang xử lý';
                        $statusColorClass = 'bg-blue-100 text-blue-800';
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

                <div class="bg-white rounded-lg shadow-md overflow-hidden" id="order-card-<?= $order->order_id ?>">
                    <div class="bg-gray-50 p-4 border-b grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
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
                        <div>
                            <p class="text-gray-500">Phương thức</p>
                            <p class="font-medium text-gray-900 uppercase"><?= htmlspecialchars($order->payment_method) ?></p>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Khách hàng</h4>
                            <div class="flex items-center space-x-3 mb-4">
                                <img src="<?= htmlspecialchars($order->user->avatar ?? 'https://via.placeholder.com/60') ?>"
                                    alt="Avatar" class="w-14 h-14 rounded-full border">
                                <div>
                                    <p class="font-medium"><?= htmlspecialchars($order->user->full_name ?? 'N/A') ?></p>
                                    <p class="text-sm text-gray-600">@<?= htmlspecialchars($order->user->username ?? 'N/A') ?></p>
                                </div>
                            </div>
                            <div class="text-sm space-y-2">
                                <p><strong class="text-gray-600 w-20 inline-block">Email:</strong> <?= htmlspecialchars($order->email) ?></p>
                                <p><strong class="text-gray-600 w-20 inline-block">Phone:</strong> <?= htmlspecialchars($order->phone) ?></p>
                                <p><strong class="text-gray-600 w-20 inline-block">Địa chỉ:</strong> <?= htmlspecialchars($order->shipping_address) ?></p>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Sản phẩm</h4>
                            <div class="space-y-4 max-h-60 overflow-y-auto pr-2">
                                <?php foreach ($order->items as $item): ?>
                                    <div class="flex items-center gap-4 border-b pb-2 last:border-b-0">
                                        <img src="<?= htmlspecialchars($item->product->image_url ?? '') ?>"
                                            alt="<?= htmlspecialchars($item->product->product_name ?? '') ?>"
                                            class="w-12 h-12 object-cover rounded-md border">
                                        <div class="flex-grow">
                                            <p class="font-medium text-sm"><?= htmlspecialchars($item->product->product_name ?? 'Sản phẩm đã bị xóa') ?></p>
                                            <p class="text-xs text-gray-600">
                                                SL: <strong class="text-red-600"><?= $item->quantity ?></strong>
                                            </p>
                                        </div>
                                        <div class="text-right text-sm">
                                            <p class="font-medium"><?= number_format($item->price, 0, ',', '.') ?>₫</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 border-t flex justify-between items-center">
                        <div>
                            <p class="text-gray-500 text-sm">Trạng thái hiện tại:</p>
                            <span id="status-badge-<?= $order->order_id ?>" class="px-3 py-1 text-sm font-medium rounded-full <?= $statusColorClass ?>">
                                <?= $statusText ?>
                            </span>
                        </div>

                        <div id="action-container-<?= $order->order_id ?>">
                            <?php if ($order->status === 'processing'): ?>
                                <button
                                    onclick="completeOrder(<?= $order->order_id ?>)"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 focus-ring">
                                    Xác nhận đơn hàng
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div> <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($ordersTotalPages) && $ordersTotalPages > 1): ?>
        <div class="mt-6 flex items-center justify-center space-x-2">
            <?php if ($ordersCurrentPage > 1): ?>
                <a href="/manager/orders?page=<?= $ordersCurrentPage - 1 ?>" class="px-3 py-1 bg-gray-200 rounded-md text-sm hover:bg-gray-300">&laquo; Trước</a>
            <?php else: ?>
                <div class="px-3 py-1 bg-gray-200 rounded-md text-sm invisible">&laquo; Trước</div>
            <?php endif; ?>

            <div class="px-3 py-1 text-sm text-gray-600">Trang <?= $ordersCurrentPage ?> / <?= $ordersTotalPages ?></div>

            <?php if ($ordersCurrentPage < $ordersTotalPages): ?>
                <a href="/manager/orders?page=<?= $ordersCurrentPage + 1 ?>" class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Tiếp &raquo;</a>
            <?php else: ?>
                <div class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm invisible">Tiếp &raquo;</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<script>
    function completeOrder(orderId) {
        if (!confirm('Bạn có chắc chắn xác nhận đơn hàng này đã hoàn thành? Hành động này không thể hoàn tác.')) {
            return;
        }

        const button = event.target;
        button.disabled = true;
        button.textContent = 'Đang cập nhật...';

        fetch(`/manager/orders/complete/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cập nhật đơn hàng thành công!');

                    // Cập nhật giao diện
                    const statusSpan = document.getElementById(`status-badge-${orderId}`);
                    statusSpan.textContent = 'Hoàn thành';
                    statusSpan.className = 'px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800';

                    // Xóa nút
                    document.getElementById(`action-container-${orderId}`).innerHTML = '';
                } else {
                    alert('Lỗi: ' + data.message);
                    button.disabled = false;
                    button.textContent = 'Xác nhận đơn hàng';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi kết nối.');
                button.disabled = false;
                button.textContent = 'Xác nhận đơn hàng';
            });
    }
</script>