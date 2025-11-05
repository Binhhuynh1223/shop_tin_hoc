<section class="p-6 lg:p-8 border-t border-gray-200">
    <header class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Tổng quan hoạt động kinh doanh</h2>
        <p class="text-sm text-gray-500 mt-1">Chi tiết về hoạt động kinh doanh</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-900">Tổng doanh thu</h3>
            <p class="text-3xl font-bold text-green-600 mt-2"><?= number_format($totalRevenue) ?>₫</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-900">Tổng đơn hàng</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2"><?= $totalOrders ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-900">Tổng sản phẩm</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2"><?= $totalProducts ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-900">Tổng người dùng</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2"><?= $totalUsers ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white rounded-lg shadow-md overflow-hidden">
            <h3 class="text-lg font-semibold text-gray-900 p-4 border-b">Top 5 Sản phẩm bán chạy</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                            <th class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase">Đã bán</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($topProducts as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="<?= $item->product->image_url ?? 'https://via.placeholder.com/40' ?>" alt="Ảnh" class="w-10 h-10 object-cover rounded-md border">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item->product->product_name ?? 'Sản phẩm đã bị xóa') ?></p>
                                            <p class="text-xs text-gray-500">ID: <?= $item->product_id ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center text-sm font-bold text-indigo-600"><?= $item->total_sold ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <h3 class="text-lg font-semibold text-gray-900 p-4 border-b">Top 5 Sản phẩm sắp hết hàng</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                            <th class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase">Tồn kho</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($lowStockProducts as $product): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($product->product_name) ?></td>
                                <td class="py-4 px-4 text-center text-sm font-bold text-red-600"><?= $product->stock ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="mt-6 bg-white rounded-lg shadow-md overflow-hidden">
        <h3 class="text-lg font-semibold text-gray-900 p-4 border-b">Top 5 Khách hàng chi tiêu nhiều nhất</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase">Khách hàng</th>
                        <th class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase">Số đơn đã mua</th>
                        <th class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase">Tổng chi tiêu</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($topCustomers as $customer): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="<?= $customer->avatar ?? 'https://via.placeholder.com/40' ?>" alt="Avatar" class="w-10 h-10 object-cover rounded-full border">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($customer->full_name ?? $customer->username) ?></p>
                                        <p class="text-xs text-gray-500">@<?= htmlspecialchars($customer->username) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center text-sm text-gray-700"><?= $customer->total_orders ?></td>
                            <td class="py-4 px-4 text-right text-sm font-bold text-green-600"><?= number_format($customer->total_spent) ?>₫</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow-md overflow-hidden">
        <h3 class="text-lg font-semibold text-gray-900 p-4 border-b">Biểu đồ doanh thu 12 tháng gần nhất</h3>
        <div class="p-4">
            <canvas id="revenueChart" class="w-full h-64"></canvas>
        </div>
    </div>

</section>

<script>
    // Vẽ biểu đồ doanh thu
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                label: 'Doanh thu (VND)',
                data: <?= json_encode($chartData) ?>,
                backgroundColor: 'rgba(79, 70, 229, 0.6)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(value);
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                },
                legend: {
                    display: false
                }
            }
        }
    });
</script>