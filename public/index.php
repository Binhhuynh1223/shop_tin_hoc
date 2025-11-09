<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../bootstrap.php';

define('APPNAME', 'Shop Tin Học');

session_start();

// Reset phiên sau 10p không hoạt động
define('INACTIVITY_TIMEOUT', 600); // 10 phút = 600 giây

if (isset($_SESSION['user'])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > INACTIVITY_TIMEOUT)) {
        // Hết hạn, hủy session
        session_unset();
        session_destroy();

        // Đặt thông báo lỗi và chuyển hướng
        session_start(); // Bắt đầu session mới để lưu thông báo
        $_SESSION['login_error'] = 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại.';
        header('Location: /login');
        exit;
    }
    // Cập nhật thời gian hoạt động
    $_SESSION['last_activity'] = time();
}

require_once __DIR__ . '/../vendor/autoload.php';

use Bramus\Router\Router;

$router = new Router();


require_once __DIR__ . '/router/authRouter.php';
require_once __DIR__ . '/router/managerRouter.php';
require_once __DIR__ . '/router/viewRouter.php';


$router->run();
