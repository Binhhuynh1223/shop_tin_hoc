<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    protected $model;

    public function __construct()
    {
        $this->model = new User();
    }

    /**
     * Handle user registration
     * @return void
     */
    public function register()
    {
        try {
            if (!isset($_POST['username']) || !isset($_POST['password'])) {
                throw new \Exception('Vui lòng nhập đầy đủ thông tin');
            }

            $data = [
                'username' => trim($_POST['username']),
                'password' => $_POST['password']
            ];

            // Validate username
            if (strlen($data['username']) < 4) {
                throw new \Exception('Tên đăng nhập phải có ít nhất 4 ký tự');
            }

            // Check if username exists
            if ($this->model->findByUsername($data['username'])) {
                throw new \Exception('Tên đăng nhập đã tồn tại, vui lòng chọn tên khác!');
            }

            // Validate password
            if (strlen($data['password']) < 6) {
                throw new \Exception('Mật khẩu phải có ít nhất 6 ký tự');
            }

            // Hash password and create user
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            $this->model->create($data);

            $_SESSION['register_success'] = 'Đăng ký thành công, bạn có thể đăng nhập!';
            header('Location: /login');
            exit;
        } catch (\Exception $e) {
            $_SESSION['register_error'] = $e->getMessage();
            header('Location: /register');
            exit;
        }
    }

    /**
     * Handle user login
     * @return void
     */
    public function login()
    {
        try {
            if (!isset($_POST['username']) || !isset($_POST['password'])) {
                throw new \Exception('Vui lòng nhập đầy đủ thông tin');
            }

            $data = [
                'username' => trim($_POST['username']),
                'password' => $_POST['password']
            ];

            $user = $this->model->findByUsername($data['username']);

            if (!$user) {
                throw new \Exception('Tên đăng nhập hoặc mật khẩu không chính xác');
            }

            if (!password_verify($data['password'], $user->password)) {
                throw new \Exception('Tên đăng nhập hoặc mật khẩu không chính xác');
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['user'] = [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role ?? 'customer',
                'avatar' => $user->avatar ?? 'https://static.vecteezy.com/system/resources/previews/006/487/912/non_2x/hacker-avatar-ilustration-free-vector.jpg'
            ];

            $_SESSION['last_activity'] = time();

            if ($user->role === 'admin') {
                header('Location: /manager');
                exit;
            } else {
                header('Location: /');
                exit;
            }
        } catch (\Exception $e) {
            $_SESSION['login_error'] = $e->getMessage();
            header('Location: /login');
            exit;
        }
    }

    /**
     * Handle user logout
     * @return void
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
        header('Location: /');
        exit;
    }
}
