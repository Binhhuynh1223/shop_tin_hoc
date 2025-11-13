<?php

namespace App\Controllers;

use App\Models\Product;
use App\Middleware\AuthMiddleware;

class ProductController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new Product();
    }

    public function index()
    {
        $this->render('products');
    }

    public function show($id)
    {
        $_GET['id'] = $id;
        $this->render('product_detail');
    }

    public function filter($category = null, $brand = null)
    {
        if ($category) {
            $_GET['category'] = $category;
        }
        if ($brand) {
            $_GET['brand'] = $brand;
        }
        $this->render('products');
    }

    /**
     * Create a new product
     * @return void
     */
    public function create()
    {
        try {
            AuthMiddleware::requireAdmin();

            $data = $_POST;
            Product::validate($data);

            // Handle image upload if present
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $data['image_url'] = $this->handleImageUpload($_FILES['image']);
            }

            $ok = $this->model->create($data);
            $this->jsonResponse(['success' => true, 'message' => 'Tạo sản phẩm thành công']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Show edit form for product (fallback for non-AJAX, nhưng khuyến nghị dùng AJAX cho modal)
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        try {
            AuthMiddleware::requireAdmin();

            $product = $this->model->find($id);
            if (!$product) {
                $this->jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
                return;
            }

            // Nếu là AJAX request, return JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $this->jsonResponse(['success' => true, 'data' => $product]);
                return;
            }
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update($id)
    {
        try {
            AuthMiddleware::requireAdmin();

            $product = $this->model->find($id);
            if (!$product) {
                throw new \Exception('Product not found');
            }

            $data = $_POST;
            Product::validate($data);

            // Handle image upload nếu có file mới
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $data['image_url'] = $this->handleImageUpload($_FILES['image']);
            } else {
                // Giữ image_url cũ nếu không upload mới
                $data['image_url'] = $data['current_image_url'] ?? $product->image_url;
            }

            $updated = $this->model->updateRecord($id, $data);
            if (!$updated) {
                throw new \Exception('Could not update product');
            }

            $this->jsonResponse(['success' => true, 'message' => 'Cập nhật sản phẩm thành công']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function delete($id)
    {
        try {
            $product = $this->model->find($id);
            if (!$product) {
                $_SESSION['error'] = 'Product not found';
                header('Location: /manager/products');
                exit;
            }

            $deleted = $product->delete();
            if ($deleted) {
                $_SESSION['success'] = 'Product deleted successfully';
            } else {
                $_SESSION['error'] = 'Could not delete product';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error occurred: ' . $e->getMessage();
        }

        header('Location: /manager/products');
        exit;
    }

    /**
     * Handle image upload
     * @param array $file
     * @return string
     * @throws \Exception
     */
    private function handleImageUpload($file)
    {
        $targetDir = __DIR__ . "/../../public/images/products/";

        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new \Exception('Chỉ chấp nhận file ảnh định dạng JPG, JPEG hoặc PNG');
        }

        // Nếu trùng tên file thì đổi tên
        $filename = date('d-m-Y') . '_' . basename($file['name']);
        $targetFile = $targetDir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            throw new \Exception('Không thể tải lên file ảnh');
        }

        return '/images/products/' . $filename;
    }

    /**
     * Search by product name
     */
    public function search()
    {
        try {
            $query = $_GET['q'] ?? '';
            if (strlen($query) < 1) {
                $this->jsonResponse(['success' => true, 'products' => []]);
                return;
            }

            $products = $this->model
                ->where('product_name', 'LIKE', '%' . $query . '%')
                ->select('product_id', 'product_name', 'image_url', 'price', 'brand')
                ->take(10) // Giới hạn 10 kết quả
                ->get();

            $this->jsonResponse(['success' => true, 'products' => $products]);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
