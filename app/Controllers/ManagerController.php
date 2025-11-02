<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Order;

class ManagerController
{

    private function checkAdminSession()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /error');
            exit;
        }
    }

    private function getAdminInfo()
    {
        return [
            'name' => $_SESSION['user']['name'] ?? 'Admin',
            'avatar' => $_SESSION['user']['avatar'] ?? 'https://via.placeholder.com/40'
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

        // Dữ liệu biểu đồ
        $chartLabels = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'];
        $chartData = [5000000, 7000000, 12000000, 9000000, 15000000, 20000000];

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
        $this->checkAdminSession();
        $adminInfo = $this->getAdminInfo();
        $products = new Product();
        $products = $products->all();
        $dashboardData = $this->getDashboardData();
        include __DIR__ . '/../Views/manager/layouts/admin.php';
    }

    public function products()
    {
        $this->checkAdminSession();
        $adminInfo = $this->getAdminInfo();

        // Pagination settings
        $perPage = 5;
        $page = max(1, intval($_GET['page'] ?? 1));
        $search = $_GET['search'] ?? null;

        // Build query and get totals
        $query = Product::query();
        $total = $query->count();

        // Search
        if ($search) {
            $query->where('product_name', 'LIKE', '%' . $search . '%');
        }
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
        $this->checkAdminSession();
        $adminInfo = $this->getAdminInfo();

        // Pagination settings
        $perPage = 5;
        $page = max(1, intval($_GET['page'] ?? 1));
        $search = $_GET['search'] ?? null;

        $query = User::withCount('orders')
            ->withSum('orders as total_spent', 'total_amount');

        $total = $query->count();

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'LIKE', '%' . $search . '%')
                    ->orWhere('full_name', 'LIKE', '%' . $search . '%');
            });
        }

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
        $this->checkAdminSession();
        $adminInfo = $this->getAdminInfo();

        // Pagination settings
        $perPage = 5;
        $page = max(1, intval($_GET['page'] ?? 1));
        $search = $_GET['search'] ?? null;

        $query = Order::with(['user', 'items.product'])
            ->orderBy('order_date', 'desc');

        // Search
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'LIKE', '%' . $search . '%')
                    ->orWhere('full_name', 'LIKE', '%' . $search . '%');
            });
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
