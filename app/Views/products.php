<?php

use App\Models\Product;

// Pagination settings
$perPage = 12;
$page = max(1, intval($_GET['page'] ?? 1));

$productsModel = new Product();
$query = Product::query();

// Filter values
$selectedCategory = $_GET['category'] ?? 'default';
$min_price = $_GET['min_price'] ?? null;
$max_price = $_GET['max_price'] ?? null;

// Filter category
if (isset($_GET['category']) && $_GET['category'] !== 'default') {
    $query->where('category', $_GET['category']);
}

// Filter brand
if (isset($_GET['brand'])) {
    $query->where('brand', $_GET['brand']);
}

// Filter price
if (!empty($min_price) && is_numeric($min_price)) {
    $query->where('price', '>=', $min_price);
}
if (!empty($max_price) && is_numeric($max_price)) {
    $query->where('price', '<=', $max_price);
}

// Get total products and paginated results
$totalProducts = $query->count();
$products_home = $query->orderBy('created_at', 'desc')
    ->offset(($page - 1) * $perPage)
    ->limit($perPage)
    ->get();

$totalPages = (int) ceil($totalProducts / $perPage);

// Get categories for dropdown
$categories = array_unique(array_column((array) $productsModel->all()->toArray(), 'category'));

// Build query string for pagination
$queryParams = [];
if ($selectedCategory !== 'default') $queryParams['category'] = $selectedCategory;
if (!empty($min_price)) $queryParams['min_price'] = $min_price;
if (!empty($max_price)) $queryParams['max_price'] = $max_price;
if (isset($_GET['brand'])) $queryParams['brand'] = $_GET['brand'];
$queryString = http_build_query($queryParams);

?>

<?php include __DIR__ . '/partials/header.php'; ?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <section id="products">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Danh sách sản phẩm</h1>

            <form id="filter-form" method="GET" action="/products" class="flex gap-3">
                <select id="category" name="category" class="px-3 py-2 border rounded-md text-sm">
                    <option value="default">Tất cả danh mục</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $selectedCategory === $category ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="number" id="min_price" name="min_price" placeholder="Giá từ"
                    class="px-3 py-2 border rounded-md text-sm w-32"
                    value="<?= htmlspecialchars($min_price ?? '') ?>">

                <input type="number" id="max_price" name="max_price" placeholder="Đến"
                    class="px-3 py-2 border rounded-md text-sm w-32"
                    value="<?= htmlspecialchars($max_price ?? '') ?>">

                <button type="submit" class="hidden"></button>
            </form>
        </div>


        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php if ($products_home->isEmpty()): ?>
                <p class="col-span-full text-center text-gray-600">Không tìm thấy sản phẩm nào phù hợp.</p>
            <?php else: ?>
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
                            onclick="addToCart(<?php echo $product->product_id; ?>)" title="Thêm vào giỏ hàng">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </button>
                        <button class="p-2 rounded-md border text-md focus-ring hover:bg-green-600 hover:text-white hover:border-green-600 transition-colors text-center"
                            onclick="buyNow(<?php echo $product->product_id; ?>)" title="Mua ngay">
                            Mua ngay
                        </button>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-6 flex items-center justify-center space-x-2">
                <?php if ($page > 1): ?>
                    <a href="/products?page=<?= $page - 1 ?><?= $queryString ? '&' . $queryString : '' ?>" class="px-3 py-1 bg-gray-200 rounded-md text-sm hover:bg-gray-300">&laquo; Trước</a>
                <?php else: ?>
                    <div class="px-3 py-1 bg-gray-200 rounded-md text-sm invisible">&laquo; Trước</div>
                <?php endif; ?>

                <div class="px-3 py-1 text-sm text-gray-600">Trang <?= $page ?> / <?= $totalPages ?></div>

                <?php if ($page < $totalPages): ?>
                    <a href="/products?page=<?= $page + 1 ?><?= $queryString ? '&' . $queryString : '' ?>" class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Tiếp &raquo;</a>
                <?php else: ?>
                    <div class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm invisible">Tiếp &raquo;</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    // Filter
    document.getElementById('filter-form').addEventListener('change', function(e) {
        const searchParams = new URLSearchParams(window.location.search);
        searchParams.delete('page');

        searchParams.set(e.target.name, e.target.value);

        if (e.target.name === 'category' && e.target.value === 'default') {
            searchParams.delete('category');
        }
        if (e.target.name === 'min_price' && e.target.value === '') {
            searchParams.delete('min_price');
        }
        if (e.target.name === 'max_price' && e.target.value === '') {
            searchParams.delete('max_price');
        }

        window.location.href = '/products' + (searchParams.toString() ? '?' + searchParams.toString() : '');
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

    function buyNow(productId) {
        fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1 // Mặc định là 1 khi ở trang danh sách
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