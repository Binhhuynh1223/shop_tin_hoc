<?php

use App\Models\Product;
use App\Models\Review;
use App\Models\Order;

// Lấy sản phẩm
$product = (new Product())->find($_GET['id'] ?? 0);

// Lấy đánh giá
$reviews = Review::where('product_id', $product->product_id)
    ->with('user') // Lấy thông tin user kèm theo
    ->orderBy('created_at', 'desc')
    ->get();

// Tính rating trung bình
$averageRating = $reviews->avg('rating');
$totalReviews = $reviews->count();

// Kiểm tra xem user có thể review không
$userHasPurchased = false;
$userHasReviewed = false;
$canReview = false;
$currentUserId = $_SESSION['user']['id'] ?? null;

if ($currentUserId) {
    // Kiểm tra đã mua hàng (đơn hàng 'completed')
    $userHasPurchased = Order::where('user_id', $currentUserId)
        ->where('status', 'completed')
        ->whereHas('items', fn($q) => $q->where('product_id', $product->product_id))
        ->exists();

    // Kiểm tra đã đánh giá sản phẩm này chưa
    $userHasReviewed = Review::where('user_id', $currentUserId)
        ->where('product_id', $product->product_id)
        ->exists();

    $canReview = $userHasPurchased && !$userHasReviewed;
}

include __DIR__ . '/partials/header.php';
?>

<style>
    /* CSS cho form (tạo mới và chỉnh sửa) */
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }

    .star-rating input[type="radio"] {
        display: none;
    }

    .star-rating label {
        font-size: 2rem;
        color: #ddd;
        cursor: pointer;
    }

    .star-rating input[type="radio"]:checked~label,
    .star-rating label:hover,
    .star-rating label:hover~label {
        color: #f59e0b;
    }

    /* CSS cho hiển thị sao (readonly) */
    .review-stars {
        display: flex;
        color: #f59e0b;
    }

    .review-stars-empty {
        display: flex;
        color: #e5e7eb;
    }

    /* Sao rỗng */
    .review-stars svg,
    .review-stars-empty svg {
        width: 1rem;
        height: 1rem;
    }

    /* CSS cho modal */
    #editReviewModal {
        transition: opacity 0.3s ease;
    }
</style>

<div class="container mx-auto px-4 py-8">

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
            <?= $_SESSION['success_message'] ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
            <?= $_SESSION['error_message'] ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Product Image -->
        <div class="w-full md:w-1/2">
            <img src="<?= htmlspecialchars($product->image_url) ?>" alt="<?= htmlspecialchars($product->product_name) ?>" class="w-full h-auto object-cover rounded-lg shadow-md">
        </div>

        <div class="w-full md:w-1/2">
            <h2 class="text-3xl font-bold mb-2 text-gray-900"><?= htmlspecialchars($product->product_name) ?></h2>
            <p class="text-gray-500 mb-4">ID sản phẩm: <?= htmlspecialchars($product->product_id) ?></p>
            <div class="mb-4">
                <span class="text-2xl font-bold text-indigo-600">Giá: <?= number_format($product->price) ?>₫</span>
            </div>

            <div class="flex items-center mb-4">
                <?php if ($totalReviews > 0): ?>
                    <div class="flex text-yellow-400">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="<?= ($i < round($averageRating)) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" class="w-5 h-5 <?= ($i < round($averageRating)) ? '' : 'text-gray-300' ?>">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.337 1.003l-4.118 3.991a.563.563 0 00-.162.53l1.04 5.493c.083.44-.423.77-1.003.518l-4.723-2.685a.563.563 0 00-.527 0l-4.723 2.685c-.58.252-1.086-.078-1.003-.518l1.04-5.493a.563.563 0 00-.162-.53L2.18 10.399c-.364-.34-.162-.963.337-1.003l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                            </svg>
                        <?php endfor; ?>
                    </div>
                <?php else: ?>
                    <div class="flex text-gray-300">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.337 1.003l-4.118 3.991a.563.563 0 00-.162.53l1.04 5.493c.083.44-.423.77-1.003.518l-4.723-2.685a.563.563 0 00-.527 0l-4.723 2.685c-.58.252-1.086-.078-1.003-.518l1.04-5.493a.563.563 0 00-.162-.53L2.18 10.399c-.364-.34-.162-.963.337-1.003l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                            </svg>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
                <span class="ml-2 text-gray-600">(<?= $totalReviews ?> đánh giá)</span>
            </div>

            <p class="text-gray-700 mb-6"><?= htmlspecialchars($product->description); ?></p>
            <div class="mb-6">
                <h3 class="text-lg mb-2">Hãng: <?= htmlspecialchars($product->brand); ?></h3>
            </div>

            <div class="mb-6">
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Số lượng:</label>
                <input type="number" id="quantity" name="quantity" min="1" max="<?= $product->stock ?>" value="1" class="w-20 text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-500"> (Còn <?= $product->stock ?> sản phẩm)</span>
            </div>

            <div class="flex space-x-4 mb-6">
                <button class="bg-indigo-600 flex gap-2 items-center text-white px-6 py-3 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    onclick="addToCart(<?= $product->product_id ?>, document.getElementById('quantity').value)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                    Thêm vào giỏ hàng
                </button>
                <button class="bg-accent flex gap-2 items-center text-white px-6 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    onclick="buyNow(<?= $product->product_id ?>, document.getElementById('quantity').value)">
                    Mua ngay
                </button>
            </div>
        </div>
    </div>

    <div class="mt-12">
        <h3 class="text-2xl font-bold mb-6 border-b pb-3">Đánh giá sản phẩm</h3>

        <?php if ($canReview): ?>
            <div class="mb-8 p-6 bg-white rounded-lg shadow-sm border">
                <h4 class="text-lg font-semibold mb-4">Viết đánh giá của bạn</h4>
                <form action="/product/<?= $product->product_id ?>/review" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Đánh giá (chọn sao):</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" required /><label for="star5" title="5 sao">&#9733;</label>
                            <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 sao">&#9733;</label>
                            <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 sao">&#9733;</label>
                            <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 sao">&#9733;</label>
                            <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 sao">&#9733;</label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="comment" class="block text-sm font-medium text-gray-700">Bình luận:</label>
                        <textarea id="comment" name="comment" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="image" class="block text-sm font-medium text-gray-700">Đăng kèm ảnh (không bắt buộc):</label>
                        <input type="file" id="image" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700 focus-ring">Gửi đánh giá</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="space-y-6">
            <?php if ($totalReviews == 0): ?>
                <div class="p-6 bg-white rounded-lg shadow-sm border text-center">
                    <p class="text-gray-500">Hiện chưa có đánh giá nào cho sản phẩm này.</p>
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="flex gap-4 p-5 bg-white rounded-lg shadow-sm border">
                        <img src="<?= htmlspecialchars($review->user->avatar ?? 'https://via.placeholder.com/48') ?>" alt="Avatar" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                        <div class="flex-1">
                            <div class="flex justify-between items-center">
                                <h5 class="font-semibold"><?= htmlspecialchars($review->user->full_name ?? $review->user->username) ?></h5>
                                <span class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($review->created_at)) ?></span>
                            </div>
                            <div class="review-stars mt-1">
                                <?php for ($i = 0; $i < $review->rating; $i++): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                <?php endfor; ?>
                                <?php for ($i = $review->rating; $i < 5; $i++): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="text-gray-300">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                <?php endfor; ?>
                            </div>
                            <p class="text-gray-700 mt-2"><?= nl2br(htmlspecialchars($review->comment)) ?></p>

                            <?php if ($review->image_url): ?>
                                <a href="<?= htmlspecialchars($review->image_url) ?>" target="_blank" title="Xem ảnh đầy đủ">
                                    <img src="<?= htmlspecialchars($review->image_url) ?>" alt="Ảnh đánh giá" class="mt-3 w-32 h-32 object-cover rounded-md border cursor-pointer hover:opacity-80 transition-opacity">
                                </a>
                            <?php endif; ?>

                            <?php if ($currentUserId && $review->user_id == $currentUserId): ?>
                                <div class="mt-3 flex gap-3">
                                    <button onclick="openEditModal(
                                        <?= $review->review_id ?>, 
                                        <?= $review->rating ?>, 
                                        '<?= htmlspecialchars(addslashes($review->comment)) ?>', 
                                        '<?= htmlspecialchars($review->image_url ?? '') ?>'
                                    )" class="text-xs text-blue-600 font-medium hover:underline">Sửa</button>

                                    <button onclick="deleteReview(<?= $review->review_id ?>)" class="text-xs text-red-600 font-medium hover:underline">Xóa</button>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="editReviewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden z-50" onclick="closeEditModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" onclick="event.stopPropagation()">
        <div class="p-6 border-b flex justify-between items-center">
            <h4 class="text-lg font-semibold">Chỉnh sửa đánh giá của bạn</h4>
            <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <form id="editReviewForm" method="POST" enctype="multipart/form-data" class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Đánh giá (chọn sao):</label>
                <div class="star-rating" id="edit-star-rating">
                    <input type="radio" id="edit-star5" name="rating" value="5" required /><label for="edit-star5" title="5 sao">&#9733;</label>
                    <input type="radio" id="edit-star4" name="rating" value="4" /><label for="edit-star4" title="4 sao">&#9733;</label>
                    <input type="radio" id="edit-star3" name="rating" value="3" /><label for="edit-star3" title="3 sao">&#9733;</label>
                    <input type="radio" id="edit-star2" name="rating" value="2" /><label for="edit-star2" title="2 sao">&#9733;</label>
                    <input type="radio" id="edit-star1" name="rating" value="1" /><label for="edit-star1" title="1 sao">&#9733;</label>
                </div>
            </div>
            <div class="mb-4">
                <label for="edit_comment" class="block text-sm font-medium text-gray-700">Bình luận:</label>
                <textarea id="edit_comment" name="comment" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Ảnh hiện tại:</label>
                <img id="edit_image_preview" src="" alt="Ảnh đánh giá" class="mt-1 w-24 h-24 object-cover rounded-md border hidden">
                <p id="edit_no_image" class="text-sm text-gray-500">Không có ảnh.</p>
            </div>
            <div class="mb-4">
                <label for="edit_image" class="block text-sm font-medium text-gray-700">Thay đổi ảnh (không bắt buộc):</label>
                <input type="file" id="edit_image" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-medium hover:bg-gray-300">Hủy</button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700 focus-ring">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteReviewForm" method="POST" class="hidden">
</form>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    function addToCart(productId, quantity) {
        fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đã thêm vào giỏ hàng!');
                } else {
                    alert(data.message);
                }
            });
    }

    function buyNow(productId, quantity) {
        fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Thêm vào giỏ hàng thành công, chuyển hướng đến trang giỏ hàng
                    window.location.href = '/cart';
                } else {
                    // Hiển thị lỗi nếu thêm thất bại
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Lỗi Mua ngay:', error);
                alert('Đã xảy ra lỗi khi thêm sản phẩm. Vui lòng thử lại.');
            });
    }

    // Chỉnh sửa đánh giá
    const editModal = document.getElementById('editReviewModal');
    const editForm = document.getElementById('editReviewForm');
    const editComment = document.getElementById('edit_comment');
    const editImagePreview = document.getElementById('edit_image_preview');
    const editNoImage = document.getElementById('edit_no_image');

    function openEditModal(reviewId, currentRating, currentComment, currentImageUrl) {
        // Set action cho form
        editForm.action = `/review/update/${reviewId}`;

        // Set comment
        editComment.value = currentComment;

        // Set rating
        const ratingInput = document.querySelector(`#edit-star-rating input[name="rating"][value="${currentRating}"]`);
        if (ratingInput) {
            ratingInput.checked = true;
        }

        // Set ảnh preview
        if (currentImageUrl) {
            editImagePreview.src = currentImageUrl;
            editImagePreview.classList.remove('hidden');
            editNoImage.classList.add('hidden');
        } else {
            editImagePreview.classList.add('hidden');
            editNoImage.classList.remove('hidden');
        }

        // Hiển thị modal
        editModal.classList.remove('hidden');
    }

    function closeEditModal() {
        editModal.classList.add('hidden');
    }

    // Xóa đánh giá
    function deleteReview(reviewId) {
        if (confirm('Bạn có chắc chắn muốn xóa đánh giá này không?')) {
            const deleteForm = document.getElementById('deleteReviewForm');
            deleteForm.action = `/review/delete/${reviewId}`;
            deleteForm.submit();
        }
    }
</script>