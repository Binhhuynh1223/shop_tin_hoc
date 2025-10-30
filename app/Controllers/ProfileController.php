<?php

namespace App\Controllers;

use App\Models\User;

class ProfileController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function update()
    {
        \App\Middleware\AuthMiddleware::requireAuth();

        $id = $_SESSION['user']['id'];
        $data = array_filter([
            'full_name' => $_POST['full_name'] ?? null,
            'email' => $_POST['email'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'address' => $_POST['address'] ?? null,
        ]);

        try {
            // Handle avatar upload if file is present
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
                $file = $_FILES['avatar'];
                $allowedTypes = ['image/jpeg', 'image/png'];
                $allowedExts = ['jpg', 'jpeg', 'png'];
                $maxSize = 2 * 1024 * 1024; // 2MB

                if (!in_array($file['type'], $allowedTypes)) {
                    throw new \Exception('Chỉ hỗ trợ định dạng JPG hoặc PNG');
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                if (!in_array(strtolower($ext), $allowedExts)) {
                    throw new \Exception('Chỉ hỗ trợ định dạng JPG hoặc PNG');
                }

                if ($file['size'] > $maxSize) {
                    throw new \Exception('Kích thước file tối đa 2MB');
                }

                $uploadDir = __DIR__ . '/../../public/images/avatars/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $filename = uniqid('avatar_', true) . '.' . $ext;
                $uploadPath = $uploadDir . $filename;
                $publicPath = '/images/avatars/' . $filename;

                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $data['avatar'] = $publicPath;
                } else {
                    throw new \Exception('Lỗi khi tải file lên server');
                }
            }

            if (!empty($data)) {
                $updated = $this->model->updateUser($id, $data);
                if ($updated) {
                    // Update session if avatar changed
                    if (isset($data['avatar'])) {
                        $_SESSION['user']['avatar'] = $data['avatar'];
                    }
                    $_SESSION['success'] = 'Cập nhật thông tin thành công';
                } else {
                    if (isset($uploadPath)) {
                        unlink($uploadPath); // Clean up if update failed
                    }
                    throw new \Exception('Không thể cập nhật thông tin');
                }
            } else {
                $_SESSION['success'] = 'Không có thay đổi';
            }
        } catch (\Exception $e) {
            if (isset($uploadPath)) {
                unlink($uploadPath); // Clean up on error
            }
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('/profile');
    }
}
