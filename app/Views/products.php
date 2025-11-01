<?php

use App\Models\Product;

// Pagination settings
$perPage = 12;
$page = max(1, intval($_GET['page'] ?? 1));

$productsModel = new Product();
$query = Product::query();

if (isset($_GET['category']) && $_GET['category'] !== 'default') {
    $query = $query->where('category', $_GET['category']);
}

if (isset($_GET['brand'])) {
    $query = $query->where('brand', $_GET['brand']);
}

$totalProducts = $query->count();
$products_home = $query->orderBy('created_at', 'desc')
    ->offset(($page - 1) * $perPage)
    ->limit($perPage)
    ->get();

$totalPages = (int) ceil($totalProducts / $perPage);
?>

<?php include __DIR__ . '/partials/header.php'; ?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <section id="products">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Danh sách sản phẩm</h1>
            <div class="flex gap-3">
                <select id="category" class="px-3 py-2 border rounded-md text-sm">
                    <option value="default">Tất cả danh mục</option>
                    <?php
                    $categories = array_unique(array_column((array) $productsModel->all()->toArray(), 'category'));
                    foreach ($categories as $category):
                    ?>
                        <option value="<?php echo htmlspecialchars($category); ?>" <?php echo isset($_GET['category']) && $_GET['category'] === $category ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="search"
                    placeholder="Search..."
                    class="px-3 py-2 border rounded-md text-sm"
                    id="searchInput">
            </div>
        </div>


        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($products_home as $product): ?>
                <article class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="cursor-pointer" onclick="viewProduct(<?php echo $product->product_id; ?>)">
                        <img src="<?php echo htmlspecialchars($product->image_url); ?>"
                            class="w-full product-img">
                        <div class="p-4">
                            <h3 class="font-semibold"><?php echo htmlspecialchars($product->product_name); ?></h3>
                            <p class="mt-1 text-sm text-slate-600"><?php echo htmlspecialchars($product->description); ?></p>
                            <div class="mt-3">
                                <div class="text-lg font-bold">
                                    <?php echo number_format($product->price, 0, ',', '.'); ?>₫
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-4 flex gap-2 justify-end">
                        <button class="p-2 rounded-md border text-sm focus-ring hover:bg-accent hover:text-white hover:border-accent transition-colors"
                            onclick="addToFavorite(<?php echo $product->product_id; ?>)" title="Yêu thích">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                        <button class="p-2 rounded-md border text-sm focus-ring hover:bg-accent hover:text-white hover:border-accent transition-colors"
                            onclick="addToCart(<?php echo $product->product_id; ?>)" title="Thêm vào giỏ hàng">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-6 flex items-center justify-center space-x-2">
                <?php if ($page > 1): ?>
                    <a href="/products?page=<?= $page - 1 ?>" class="px-3 py-1 bg-gray-200 rounded-md text-sm hover:bg-gray-300">&laquo; Trước</a>
                <?php endif; ?>

                <div class="px-3 py-1 text-sm text-gray-600">Trang <?= $page ?> / <?= $totalPages ?></div>

                <?php if ($page < $totalPages): ?>
                    <a href="/products?page=<?= $page + 1 ?>" class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Tiếp &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    // Filter products by category
    document.getElementById('category').addEventListener('change', function(e) {
        const category = e.target.value;
        const searchParams = new URLSearchParams(window.location.search);
        if (category === 'default') {
            searchParams.delete('category');
        } else {
            searchParams.set('category', category);
        }
        searchParams.delete('page'); // reset to first page on filter change
        window.location.href = '/products' + (searchParams.toString() ? '?' + searchParams.toString() : '');
    });

    // Search products
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchText = e.target.value.toLowerCase();
        const products = document.querySelectorAll('#products article');

        products.forEach(product => {
            const title = product.querySelector('h3').textContent.toLowerCase();
            const desc = product.querySelector('p').textContent.toLowerCase();

            if (title.includes(searchText) || desc.includes(searchText)) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
        });
    });

    function viewProduct(productId) {
        window.location.href = `/product/${productId}`;
    }

    function addToCart(productId) {
        fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
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

    function addToFavorite(productId) {
        fetch(`/favorites/add/${productId}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đã thêm vào yêu thích!');
                } else {
                    alert(data.message);
                }
            });
    }
</script>