<?php

use App\Models\Product;

// Pagination settings
$perPage = 12;
$page = max(1, intval($_GET['page'] ?? 1));

$productsModel = new Product();
$query = Product::query();

// Filter values
$selectedCategory = $_GET['category'] ?? 'default';
$selectedBrand = $_GET['brand'] ?? 'default';
$min_price = $_GET['min_price'] ?? null;
$max_price = $_GET['max_price'] ?? null;

// Filter category
if ($selectedCategory !== 'default') {
    $query->where('category', $selectedCategory);
}

// Filter brand (chỉ lọc brand khi đã chọn category)
if ($selectedCategory !== 'default' && $selectedBrand !== 'default') {
    $query->where('brand', $selectedBrand);
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

// Lấy danh sách brands DỰA TRÊN CATEGORY ĐÃ CHỌN
$brands = [];
if ($selectedCategory !== 'default') {
    $brands = Product::where('category', $selectedCategory)
        ->distinct()
        ->pluck('brand')
        ->sort()
        ->toArray();
}

// Build query string for pagination
$queryParams = [];
if ($selectedCategory !== 'default') $queryParams['category'] = $selectedCategory;
if ($selectedCategory !== 'default' && $selectedBrand !== 'default') $queryParams['brand'] = $selectedBrand;
if (!empty($min_price)) $queryParams['min_price'] = $min_price;
if (!empty($max_price)) $queryParams['max_price'] = $max_price;
$queryString = http_build_query($queryParams);

?>

<?php include __DIR__ . '/partials/header.php'; ?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <section id="products">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <h1 class="text-2xl font-bold flex-shrink-0">Danh sách sản phẩm</h1>

            <form id="filter-form" method="GET" action="/products" class="w-full flex flex-col md:flex-row gap-2">
                <select id="category" name="category" class="w-full md:w-auto px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="default">Tất cả danh mục</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $selectedCategory === $category ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select id="brand" name="brand" class="w-full md:w-auto px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" <?= empty($brands) ? 'disabled' : '' ?>>
                    <option value="default">Tất cả hãng</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?php echo htmlspecialchars($brand); ?>" <?php echo $selectedBrand === $brand ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($brand); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="number" id="min_price" name="min_price" placeholder="Giá từ"
                    class="w-full md:w-32 px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    value="<?= htmlspecialchars($min_price ?? '') ?>">

                <input type="number" id="max_price" name="max_price" placeholder="Đến"
                    class="w-full md:w-32 px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    value="<?= htmlspecialchars($max_price ?? '') ?>">

                <button type="submit" class="hidden"></button>
            </form>
        </div>


        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php if ($products_home->isEmpty()): ?>
                <p class="col-span-full text-center text-gray-600">Không tìm thấy sản phẩm nào phù hợp.</p>
            <?php else: ?>
                <?php foreach ($products_home as $product): ?>
                    <article class="bg-white rounded-lg shadow-sm overflow-hidden flex flex-col">
                        <div class="cursor-pointer flex-grow" onclick="viewProduct(<?php echo $product->product_id; ?>)">
                            <img src="<?php echo htmlspecialchars($product->image_url); ?>"
                                class="w-full product-img">
                            <div class="p-4">
                                <h3 class="font-semibold text-sm sm:text-base line-clamp-2"><?php echo htmlspecialchars($product->product_name); ?></h3>
                                <p class="mt-1 text-xs sm:text-sm text-slate-600 line-clamp-2"><?php echo htmlspecialchars($product->description); ?></p>
                                <div class="mt-3">
                                    <div class="text-base sm:text-lg font-bold">
                                        <?php echo number_format($product->price, 0, ',', '.'); ?>₫
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <button class="p-2 rounded-md border text-xs sm:text-sm focus-ring hover:bg-accent hover:text-white hover:border-accent transition-colors flex items-center justify-center gap-1"
                                onclick="addToCart(<?php echo $product->product_id; ?>)" title="Thêm vào giỏ hàng">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>Thêm vào giỏ</span>
                            </button>
                            <button class="p-2 rounded-md border text-xs sm:text-sm focus-ring hover:bg-green-600 hover:text-white hover:border-green-600 transition-colors text-center"
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
    const filterForm = document.getElementById('filter-form');
    const categorySelect = document.getElementById('category');
    const brandSelect = document.getElementById('brand');

    // Gắn listener cho toàn bộ form (cho select) hoặc từng input (cho gõ phím)
    categorySelect.addEventListener('change', handleFilterChange);
    brandSelect.addEventListener('change', handleFilterChange);

    // Sử dụng 'blur' (khi người dùng click ra ngoài) cho input giá để tránh reload mỗi khi gõ
    document.getElementById('min_price').addEventListener('blur', handleFilterChange);
    document.getElementById('max_price').addEventListener('blur', handleFilterChange);


    function handleFilterChange(e) {
        const searchParams = new URLSearchParams(window.location.search);
        searchParams.delete('page'); // Reset trang về 1 khi lọc

        // Lấy giá trị hiện tại
        const category = categorySelect.value;
        const brand = brandSelect.value;
        const minPrice = document.getElementById('min_price').value;
        const maxPrice = document.getElementById('max_price').value;

        // Xử lý Category
        if (category === 'default') {
            searchParams.delete('category');
            searchParams.delete('brand'); // Nếu reset category, cũng reset brand
        } else {
            searchParams.set('category', category);

            // Nếu sự kiện là thay đổi category, reset brand về 'default'
            if (e && e.target.id === 'category') {
                searchParams.delete('brand');
            }
        }

        // Xử lý Brand (chỉ khi category được chọn)
        if (category !== 'default' && brand !== 'default') {
            searchParams.set('brand', brand);
        } else {
            searchParams.delete('brand');
        }

        // Xử lý Giá
        if (minPrice) {
            searchParams.set('min_price', minPrice);
        } else {
            searchParams.delete('min_price');
        }
        if (maxPrice) {
            searchParams.set('max_price', maxPrice);
        } else {
            searchParams.delete('max_price');
        }

        window.location.href = '/products' + (searchParams.toString() ? '?' + searchParams.toString() : '');
    }


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