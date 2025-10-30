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

    public function getAll()
    {
        $data = $_GET;
        $ok = $this->model->getAll($data);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($ok);
    }

    public function getById($id)
    {
        $ok = $this->model->get($id);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($ok);
    }

    public function create()
    {
        $data = $_POST;
        $ok = $this->model->create($data);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($ok);
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

    public function update($id)
    {
        $data = $_POST;
        $ok = $this->model->update($id, $data);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($ok);
    }
}
