<?php

use App\Controllers\CartController;
use App\Controllers\ProfileController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\ReviewController;

$router->get('/', function () {
    require_once __DIR__ . '/../../app/Views/home.php';
});

// Product
$router->get('/products', function () {
    $controller = new ProductController();
    $controller->index();
});

$router->get('/products/search', function () {
    $controller = new ProductController();
    $controller->search();
});

$router->get('/product/{id}', function ($id) {
    $controller = new ProductController();
    $controller->show($id);
});

$router->get('/products/{category}/{brand}', function ($category, $brand) {
    $controller = new ProductController();
    $controller->filter($category, $brand);
});

$router->get('/products/{category}', function ($category) {
    $controller = new ProductController();
    $controller->filter($category);
});


$router->get('/products/{brand}', function ($brand) {
    $controller = new ProductController();
    $controller->filter(null, $brand);
});

$router->post('/product/(\d+)/review', function ($id) {
    $controller = new ReviewController();
    $controller->create($id);
});


// Cart
$router->get('/cart', function () {
    if (!isset($_SESSION['user']['id'])) {
        header('Location: /login');
        exit();
    }
    $userId = $_SESSION['user']['id'];
    $controller = new CartController();
    $controller->index($userId);
});

$router->post('/cart/add', function () {
    if (!isset($_SESSION['user']['id'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.']);
        exit();
    }
    $userId = $_SESSION['user']['id'];
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
        exit();
    }
    $controller = new CartController();
    try {
        $controller->addToCart($userId, $input);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true]);
    } catch (\Exception $e) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
});

$router->post('/cart/update/{cart_item_id}', function ($cart_item_id) {
    $controller = new CartController();
    $controller->updateQuantity($cart_item_id);
});

$router->post('/cart/remove/{cart_item_id}', function ($cart_item_id) {
    $controller = new CartController();
    $controller->remove($cart_item_id);
});


// Profile
$router->get('/profile', function () {
    $controller = new ProfileController();
    $controller->index();
});

$router->post('/profile/update', function () {
    $controller = new ProfileController();
    $controller->update();
});


// Checkout và Order
$router->get('/checkout', function () {
    $controller = new OrderController();
    $controller->checkout();
});
$router->post('/order/create', function () {
    $controller = new OrderController();
    $controller->create();
});


// Trang kết quả Order
$router->get('/order/success/(\d+)', function ($id) {
    $controller = new OrderController();
    $controller->success($id);
});
$router->get('/order/failure/(\d+)', function ($id) {
    $controller = new OrderController();
    $controller->failure($id);
});
$router->post('/order/cancel/(\d+)', function ($id) {
    $controller = new OrderController();
    $controller->cancelOrder($id);
});

// VNPay
$router->get('/order/vnpay_return', function () {
    $controller = new OrderController();
    $controller->vnpayReturn();
});
$router->get('/order/vnpay_ipn', function () {
    $controller = new OrderController();
    $controller->vnpayIpn();
});


// Review
$router->post('/review/update/(\d+)', function ($id) {
    $controller = new ReviewController();
    $controller->update($id);
});

$router->post('/review/delete/(\d+)', function ($id) {
    $controller = new ReviewController();
    $controller->delete($id);
});
