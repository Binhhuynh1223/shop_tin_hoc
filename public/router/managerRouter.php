<?php

use App\Controllers\ManagerController;
use App\Controllers\ProductController;
use App\Controllers\UserController;
use App\Controllers\OrderController;

$router->mount('/manager', function () use ($router) {

    $router->get('/', function () {
        $controller = new ManagerController();
        $controller->index();
    });

    // Quản lý sản phẩm
    $router->get('/products', function () {
        $controller = new ManagerController();
        $controller->products();
    });

    $router->get('/products/delete/(\d+)', function ($id) {
        $controller = new ProductController();
        $controller->delete($id);
    });

    $router->get('/products/edit/(\d+)', function ($id) {
        $controller = new ProductController();
        $controller->edit($id);
    });

    $router->post('/products/edit/(\d+)', function ($id) {
        $controller = new ProductController();
        $controller->update($id);
    });

    $router->post('/products/create', function () {
        $controller = new ProductController();
        $controller->create();
    });


    // Quản lý người dùng
    $router->get('/users', function () {
        $controller = new ManagerController();
        $controller->users();
    });

    $router->get('/users/delete/(\d+)', function ($id) {
        $controller = new UserController();
        $controller->delete($id);
    });

    $router->get('/users/edit/(\d+)', function ($id) {
        $controller = new UserController();
        $controller->edit($id);
    });

    $router->post('/users/edit/(\d+)', function ($id) {
        $controller = new UserController();
        $controller->update($id);
    });

    $router->post('/users/create', function () {
        $controller = new UserController();
        $controller->create();
    });

    // Quản lý đơn hàng
    $router->get('/orders', function () {
        $controller = new ManagerController();
        $controller->orders();
    });

    // Cập nhật trạng thái đơn hàng (pending -> completed)
    $router->post('/orders/complete/(\d+)', function ($id) {
        $controller = new OrderController();
        $controller->completeOrder($id);
    });

});
