<?php

namespace App\Controllers;
use App\Models\Product;
use App\Models\User;

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
        $products = $productModel->all();

        // Example data
        return [
            'totalProducts' => count($products),
            'totalOrders' => 150,
            'totalUsers' => 200,
            'totalRevenue' => 50000000,
            'chartLabels' => ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
            'chartData' => [5000000, 7000000, 12000000, 9000000, 15000000, 20000000]
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
        $products = new Product();
        $products = $products->all();
        include __DIR__ . '/../Views/manager/layouts/admin.php';
    }

    public function users()
    {
        $this->checkAdminSession();
        $adminInfo = $this->getAdminInfo();
        $users = new User();
        $users = $users->all();
        include __DIR__ . '/../Views/manager/layouts/admin.php';
    }
}