<!-- Tổng quan -->
<section class="p-6 lg:p-8 border-t border-gray-200">
    <header class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Tổng quan</h2>
        <p class="text-sm text-gray-500 mt-1">Tóm tắt hoạt động kinh doanh</p>
    </header>

    <!-- Cards thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
            <h3 class="text-lg font-medium text-gray-900">Tổng sản phẩm</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2"><?= $totalProducts ?></p>
            <p class="text-sm text-gray-500 mt-1">Sản phẩm đang có</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
            <h3 class="text-lg font-medium text-gray-900">Tổng đơn hàng</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2"><?= $totalOrders ?></p>
            <p class="text-sm text-gray-500 mt-1">Đơn hàng đã xử lý</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
            <h3 class="text-lg font-medium text-gray-900">Tổng người dùng</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2"><?= $totalUsers ?></p>
            <p class="text-sm text-gray-500 mt-1">Người dùng đăng ký</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
            <h3 class="text-lg font-medium text-gray-900">Doanh thu</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2"><?= number_format($totalRevenue) ?>₫</p>
            <p class="text-sm text-gray-500 mt-1">Doanh thu tổng</p>
        </div>
    </div>

    <!-- Biểu đồ doanh thu -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Biểu đồ doanh thu theo tháng</h3>
        <canvas id="revenueChart" class="w-full h-64"></canvas>
    </div>
</section>

<script>
    // Vẽ biểu đồ doanh thu
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                label: 'Doanh thu (VND)',
                data: <?= json_encode($chartData) ?>,
                borderColor: 'rgba(99, 102, 241, 1)',
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>