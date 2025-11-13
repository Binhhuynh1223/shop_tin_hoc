<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Models\User;

class UserController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new User();
    }

    /**
     * Tạo mới User
     */
    public function create()
    {
        try {
            AuthMiddleware::requireAdmin();
            $data = $_POST;

            // Validation
            if (empty($data['username'])) {
                throw new \Exception('Tên đăng nhập là bắt buộc');
            }
            if (empty($data['password'])) {
                throw new \Exception('Mật khẩu là bắt buộc');
            }
            if ($this->model->findByUsername($data['username'])) {
                throw new \Exception('Tên đăng nhập đã tồn tại');
            }

            // Hash mật khẩu
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

            // Xử lý avatar (nếu có)
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $data['avatar'] = $this->handleAvatarUpload($_FILES['avatar']);
            }

            $this->model->create($data);
            $this->jsonResponse(['success' => true, 'message' => 'Tạo người dùng thành công']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function delete($id)
    {
        try {
            $user = $this->model->find($id);
            if (!$user) {
                $_SESSION['error'] = 'User not found';
                header('Location: /manager/users');
                exit;
            }

            $deleted = $user->delete();
            if ($deleted) {
                $_SESSION['success'] = 'User deleted successfully';
            } else {
                $_SESSION['error'] = 'Could not delete user';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error occurred: ' . $e->getMessage();
        }

        header('Location: /manager/users');
        exit;
    }

    public function edit($id)
    {
        try {
            AuthMiddleware::requireAdmin();

            $user = $this->model->find($id); // Using find() instead of get()
            if (!$user) {
                $this->jsonResponse(['success' => false, 'message' => 'User not found'], 404);
                return;
            }

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $this->jsonResponse(['success' => true, 'data' => $user]);
                return;
            }
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * CẬP NHẬT User
     */
    public function update($id)
    {
        try {
            AuthMiddleware::requireAdmin();

            $user = $this->model->find($id);
            if (!$user) {
                $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy người dùng'], 404);
                return;
            }

            // Lấy dữ liệu từ $_POST
            $data = [
                'username' => $_POST['username'] ?? $user->username,
                'role' => $_POST['role'] ?? $user->role,
                'full_name' => $_POST['full_name'] ?? $user->full_name,
                'email' => $_POST['email'] ?? $user->email,
                'phone' => $_POST['phone'] ?? $user->phone,
                'address' => $_POST['address'] ?? $user->address,
            ];

            // Chỉ cập nhật nếu nhập mật khẩu mới
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
            }

            // Xử lý upload avatar mới (nếu có)
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $data['avatar'] = $this->handleAvatarUpload($_FILES['avatar']);
            }

            $updated = $this->model->updateUser($id, $data);

            if ($updated) {
                $this->jsonResponse(['success' => true, 'message' => 'Cập nhật người dùng thành công']);
            } else {
                throw new \Exception('Không thể cập nhật người dùng.');
            }
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Hàm xử lý upload avatar
     */
    private function handleAvatarUpload($file)
    {
        $uploadDir = __DIR__ . '/../../public/images/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validate
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new \Exception('Chỉ cho phép file JPG, PNG, GIF.');
        }

        $filename = uniqid('avatar_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return '/public/images/avatars/' . $filename;
        }

        throw new \Exception('Lỗi khi tải file avatar.');
    }
}
