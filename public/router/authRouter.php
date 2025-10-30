<?php

use App\Controllers\AuthController;

$router->get('/', function () {
    require_once __DIR__ . '/../../app/Views/home.php';
});

$router->get('/login', function () {
    require_once __DIR__ . '/../../app/Views/auth/login.php';
});

$router->post('/login', function () {
    $authController = new AuthController();
    $authController->login();
});

$router->get('/register', function () {
    require_once __DIR__ . '/../../app/Views/auth/register.php';
});

$router->post('/register', function () {
    $authController = new AuthController();
    $authController->register();
});

$router->get('/logout', function () {
    $authController = new AuthController();
    $authController->logout();
});

?>