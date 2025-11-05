<?php

use App\Models\Product;

$products = new Product();
$products_home = $products->find($_GET['id'] ?? 0);
?>

<?php include __DIR__ . '/partials/header.php'; ?>

<!-- Main Container -->
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Product Image -->
        <div class="w-full md:w-1/2">
            <img src="<?= htmlspecialchars($products_home->image_url) ?>" alt="<?= htmlspecialchars($products_home->product_name) ?>" class="w-full h-auto object-cover rounded-lg shadow-md">
        </div>

        <!-- Product Details -->
        <div class="w-full md:w-1/2">
            <h2 class="text-3xl font-bold mb-2 text-gray-900"><?= htmlspecialchars($products_home->product_name) ?></h2>
            <p class="text-gray-500 mb-4">ID sản phẩm: <?= htmlspecialchars($products_home->product_id) ?></p>
            <div class="mb-4">
                <span class="text-2xl font-bold text-indigo-600">Giá: <?= number_format($products_home->price) ?>₫</span>
            </div>
            <div class="flex items-center mb-4">
                <div class="flex text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                    </svg>
                    <!-- Lặp cho rating, giả sử 4.5 -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-300">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="ml-2 text-gray-600">(120 đánh giá)</span>
            </div>
            <p class="text-gray-700 mb-6"><?php echo htmlspecialchars($products_home->description); ?></p>

            <div class="mb-6">
                <h3 class="text-lg mb-2">Hãng: <?php echo htmlspecialchars($products_home->brand); ?></h3>
            </div>

            <div class="mb-6">
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Số lượng:</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1" class="w-20 text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>

            <div class="flex space-x-4 mb-6">
                <button class="bg-indigo-600 flex gap-2 items-center text-white px-6 py-3 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    onclick="addToCart(<?= $products_home->product_id ?>, document.getElementById('quantity').value)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                    Thêm vào giỏ hàng
                </button>
                <button class="bg-accent flex gap-2 items-center text-white px-6 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    onclick="buyNow(<?= $products_home->product_id ?>, document.getElementById('quantity').value)">
                    Mua ngay
                </button>
            </div>

        </div>
    </div>
</div>

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
</script>