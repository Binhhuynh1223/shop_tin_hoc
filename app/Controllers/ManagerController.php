<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Capsule\Manager as DB;

class ManagerController
{
    private function getAdminInfo()
    {
        $user = User::find($_SESSION['user']['id']);
        $name = $user->full_name ?? $user->username ?? 'Admin';

        return [
            'name' => $name,
            'avatar' => $user->avatar ?? 'https://via.placeholder.com/40'
        ];
    }

    private function getDashboardData()
    {
        $productModel = new Product();
        $userModel = new User();
        $orderModel = new Order();

        // Lấy dữ liệu đơn hàng
        $totalProducts = $productModel->all()->count();
        $totalOrders = $orderModel->all()->count();
        $totalUsers = $userModel->all()->count();
        // Chỉ tính doanh thu từ các đơn hàng đã 'hoàn thành' (completed)
        $totalRevenue = $orderModel->where('status', 'completed')->sum('total_amount');

        // Lấy doanh thu của 12 tháng gần nhất
        $revenueData = Order::where('status', 'completed')
            ->where('order_date', '>=', date('Y-m-d H:i:s', strtotime('-12 months')))
            ->select(
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw("DATE_FORMAT(order_date, '%Y-%m') as month_year")
            )
            ->groupBy('month_year')
            ->orderBy('month_year', 'asc')
            ->get()
            ->pluck('total_revenue', 'month_year');

        // 12 tháng (từ 11 tháng trước -> Hiện tại)
        $chartLabels = [];
        $chartData = [];

        for ($i = 11; $i >= 0; $i--) {
            $monthKey = date('Y-m', strtotime("-$i months"));
            $chartLabels[] = $monthKey;
            // Nếu tháng đó có doanh thu thì lấy, không có thì = 0
            $chartData[] = $revenueData[$monthKey] ?? 0;
        }
        return [
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData
        ];
    }

    public function index()
    {
        \App\Middleware\AuthMiddleware::requireAdmin();
        $adminInfo = $this->getAdminInfo();
        $dashboardData = $this->getDashboardData();


        // Top 5 sản phẩm bán chạy
        $topProducts = \App\Models\OrderItem::select(
            'product_id',
            DB::raw('SUM(order_items.quantity) as total_sold')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->where('orders.status', 'completed')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->with('product')
            ->get();

        // Top 5 khách hàng chi tiêu nhiều nhất
        $topCustomers = User::select(
            'users.id',
            'users.username',
            'users.full_name',
            'users.avatar',
            DB::raw('SUM(orders.total_amount) as total_spent'),
            DB::raw('COUNT(orders.order_id) as total_orders')
        )
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.status', 'completed')
            ->groupBy('users.id', 'users.username', 'users.full_name', 'users.avatar')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        // Tình trạng kho
        $lowStockProducts = Product::orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        include __DIR__ . '/../Views/manager/layouts/admin.php';
    }

    public function products()
    {
        \App\Middleware\AuthMiddleware::requireAdmin();
        $adminInfo = $this->getAdminInfo();

        // Pagination settings
        $perPage = 5;
        $page = max(1, intval($_GET['page'] ?? 1));
        $search = $_GET['search'] ?? null;

        // Build query
        $query = Product::query();

        // Search
        if ($search) {
            $query->where('product_name', 'LIKE', '%' . $search . '%');
        }

        // Get totals
        $total = $query->count();

        $products = $query->orderBy('created_at', 'desc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $productsCurrentPage = $page;
        $productsTotalPages = (int) ceil($total / $perPage);

        include __DIR__ . '/../Views/manager/layouts/admin.php';
    }

    public function users()
    {
        \App\Middleware\AuthMiddleware::requireAdmin();
        $adminInfo = $this->getAdminInfo();

        // Pagination settings
        $perPage = 5;
        $page = max(1, intval($_GET['page'] ?? 1));
        $search = $_GET['search'] ?? null;

        $query = User::withCount('orders')
            ->withSum(
                ['orders as total_spent' => function ($query) {
                    $query->where('status', 'completed');
                }],
                'total_amount'
            );

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'LIKE', '%' . $search . '%')
                    ->orWhere('full_name', 'LIKE', '%' . $search . '%');
            });
        }

        $total = $query->count();

        $users = $query->orderBy('id', 'desc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $usersCurrentPage = $page;
        $usersTotalPages = (int) ceil($total / $perPage);

        include __DIR__ . '/../Views/manager/layouts/admin.php';
    }

    public function orders()
    {
        \App\Middleware\AuthMiddleware::requireAdmin();
        $adminInfo = $this->getAdminInfo();

        // Pagination settings
        $perPage = 5;
        $page = max(1, intval($_GET['page'] ?? 1));

        $search = $_GET['search'] ?? null;
        $status = $_GET['status'] ?? null;
        $date = $_GET['date'] ?? null;

        $query = Order::with(['user', 'items.product'])
            ->orderBy('order_date', 'desc');

        // Search orders by username or full name
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'LIKE', '%' . $search . '%')
                    ->orWhere('full_name', 'LIKE', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($status && in_array($status, ['pending', 'completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        // Filter by date
        if ($date) {
            $query->whereDate('order_date', $date);
        }

        $total = $query->count();
        $orders = $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $ordersCurrentPage = $page;
        $ordersTotalPages = (int) ceil($total / $perPage);

        include __DIR__ . '/../Views/manager/layouts/admin.php';
    }
}
