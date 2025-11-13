<?php

use App\Controllers\AuthController;
use App\Controllers\GoogleAuthController;

$router->get('/login', function () {
    $authController = new AuthController();
    $authController->showLoginForm();
});

$router->post('/login', function () {
    $authController = new AuthController();
    $authController->login();
});

$router->get('/register', function () {
    $authController = new AuthController();
    $authController->showRegisterForm();
});

$router->post('/register', function () {
    $authController = new AuthController();
    $authController->register();
});

$router->get('/logout', function () {
    $authController = new AuthController();
    $authController->logout();
});

// Login email
$router->get('/auth/google', function () {
    (new GoogleAuthController())->redirectToGoogle();
});

$router->get('/auth/google/callback', function () {
    (new GoogleAuthController())->handleGoogleCallback();
});
?>