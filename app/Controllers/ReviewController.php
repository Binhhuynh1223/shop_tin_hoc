<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Models\Order;
use App\Models\Review;
use App\Models\Product;

class ReviewController extends BaseController
{

    /**
     * Kiểm tra xem user đã mua sản phẩm này chưa
     */
    private function checkPurchase($userId, $productId)
    {
        return Order::where('user_id', $userId)
            ->where('status', 'completed') // Chỉ tính đơn đã hoàn thành
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();
    }

    /**
     * Xử lý upload ảnh review
     */
    private function handleReviewImageUpload($file)
    {
        $targetDir = __DIR__ . "/../../public/images/reviews/";

        // Tạo thư mục nếu chưa có
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Validate
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new \Exception('Chỉ chấp nhận file ảnh định dạng JPG, JPEG hoặc PNG');
        }

        $filename = uniqid('review_') . '_' . basename($file['name']);
        $targetFile = $targetDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            throw new \Exception('Không thể tải lên file ảnh');
        }

        return '/images/reviews/' . $filename;
    }

    /**
     * Tạo review mới
     */
    public function create($productId)
    {
        try {
            AuthMiddleware::requireAuth();
            $userId = $_SESSION['user']['id'];

            // 1. Kiểm tra xem đã mua hàng chưa
            if (!$this->checkPurchase($userId, $productId)) {
                throw new \Exception('Bạn chỉ có thể đánh giá sản phẩm bạn đã mua.');
            }

            // 2. Kiểm tra xem đã đánh giá chưa
            $existingReview = Review::where('user_id', $userId)
                ->where('product_id', $productId)
                ->exists();

            if ($existingReview) {
                throw new \Exception('Bạn đã đánh giá sản phẩm này rồi.');
            }

            // 3. Validate input
            $rating = $_POST['rating'] ?? 0;
            $comment = $_POST['comment'] ?? '';

            if ($rating < 1 || $rating > 5) {
                throw new \Exception('Vui lòng chọn điểm đánh giá từ 1 đến 5 sao.');
            }
            if (empty($comment)) {
                throw new \Exception('Vui lòng nhập nội dung bình luận.');
            }

            $data = [
                'product_id' => $productId,
                'user_id' => $userId,
                'rating' => (int)$rating,
                'comment' => $comment,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // 4. Xử lý ảnh (nếu có)
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $data['image_url'] = $this->handleReviewImageUpload($_FILES['image']);
            }

            // 5. Lưu review
            Review::create($data);

            $_SESSION['success_message'] = 'Cảm ơn bạn đã đánh giá sản phẩm!';
            $this->redirect('/product/' . $productId);
        } catch (\Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            $this->redirect('/product/' . $productId);
        }
    }

    /**
     * Cập nhật review
     */
    public function update($reviewId)
    {
        try {
            AuthMiddleware::requireAuth();
            $userId = $_SESSION['user']['id'];

            $review = Review::find($reviewId);

            // 1. Kiểm tra xem review có tồn tại không
            if (!$review) {
                throw new \Exception('Không tìm thấy đánh giá này.');
            }

            // 2. Kiểm tra xem có đúng là chủ của review không
            if ($review->user_id != $userId) {
                throw new \Exception('Bạn không có quyền sửa đánh giá này.');
            }

            // 3. Validate input
            $rating = $_POST['rating'] ?? 0;
            $comment = $_POST['comment'] ?? '';

            if ($rating < 1 || $rating > 5) {
                throw new \Exception('Vui lòng chọn điểm đánh giá từ 1 đến 5 sao.');
            }
            if (empty($comment)) {
                throw new \Exception('Vui lòng nhập nội dung bình luận.');
            }

            $data = [
                'rating' => (int)$rating,
                'comment' => $comment,
            ];

            // 4. Xử lý ảnh (nếu có ảnh mới)
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                // Xóa ảnh cũ (nếu có)
                if ($review->image_url && file_exists(__DIR__ . '/../../public' . $review->image_url)) {
                    unlink(__DIR__ . '/../../public' . $review->image_url);
                }
                // Upload ảnh mới
                $data['image_url'] = $this->handleReviewImageUpload($_FILES['image']);
            }

            // 5. Cập nhật review
            $review->update($data);

            $_SESSION['success_message'] = 'Cập nhật đánh giá thành công!';
            $this->redirect('/product/' . $review->product_id);
        } catch (\Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            // Redirect lại, dù lỗi cũng về trang sản phẩm
            if (isset($review)) {
                $this->redirect('/product/' . $review->product_id);
            } else {
                $this->redirect('/');
            }
        }
    }

    /**
     * Xóa review
     */
    public function delete($reviewId)
    {
        try {
            AuthMiddleware::requireAuth();
            $userId = $_SESSION['user']['id'];

            $review = Review::find($reviewId);

            if (!$review) {
                throw new \Exception('Không tìm thấy đánh giá này.');
            }

            // Kiểm tra chủ sở hữu hoặc Admin
            if ($review->user_id != $userId && !AuthMiddleware::isAdmin()) {
                throw new \Exception('Bạn không có quyền xóa đánh giá này.');
            }

            $productId = $review->product_id;

            // Xóa ảnh đính kèm (nếu có)
            if ($review->image_url && file_exists(__DIR__ . '/../../public' . $review->image_url)) {
                unlink(__DIR__ . '/../../public' . $review->image_url);
            }

            // Xóa review
            $review->delete();

            $_SESSION['success_message'] = 'Đã xóa đánh giá.';
            $this->redirect('/product/' . $productId);
        } catch (\Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            if (isset($productId)) {
                $this->redirect('/product/' . $productId);
            } else {
                $this->redirect('/');
            }
        }
    }
}
