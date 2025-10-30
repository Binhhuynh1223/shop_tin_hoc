<?php

use App\Controllers\CartController;
use App\Controllers\ProfileController;
use App\Models\Cart;

$router->get('/', function () {
    require_once __DIR__ . '/../../app/Views/home.php';
});

// Product

$router->get('/products', function () {
    require_once __DIR__ . '/../../app/Views/products.php';
});

$router->get('/product/{id}', function ($id) {
    $_GET['id'] = $id;
    require_once __DIR__ . '/../../app/Views/product_detail.php';
});

$router->get('/products/{category}/{brand}', function ($category, $brand) {
    $_GET['category'] = $category;
    $_GET['brand'] = $brand;
    require_once __DIR__ . '/../../app/Views/products.php';
});

$router->get('/products/{category}', function ($category) {
    $_GET['category'] = $category;
    require_once __DIR__ . '/../../app/Views/products.php';
});


$router->get('/products/{brand}', function ($brand) {
    $_GET['brand'] = $brand;
    require_once __DIR__ . '/../../app/Views/products.php';
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
    require_once __DIR__ . '/../../app/Views/profile.php';
});

$router->post('/profile/update', function () {
    $controller = new ProfileController();
    $controller->update();
});
