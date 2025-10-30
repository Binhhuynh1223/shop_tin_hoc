<?php

use App\Models\Product;

$products_home = Product::orderBy('created_at', 'desc')->take(10)->get();
?>

<?php
include __DIR__ . '/partials/header.php';
?>

<!-- HERO -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-center">
        <div class="lg:col-span-2">
            <div class="rounded-2xl bg-gradient-to-r from-white via-slate-50 to-white p-14 shadow">
                <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight">PC, Laptop, thiết bị mạng chất lượng</h1>
                <p class="mt-4 text-slate-600">Sản phẩm chính hãng. Bảo hành rõ ràng. Dịch vụ cài đặt, sửa chữa nhanh chóng.</p>
                <div class="mt-6 flex gap-3">
                    <a href="/products" class="inline-block px-5 py-3 rounded-md bg-accent text-white font-medium focus-ring">Xem sản phẩm</a>
                    <a href="#services" class="inline-block px-5 py-3 rounded-md border border-slate-200 text-slate-700 focus-ring">Dịch vụ</a>
                </div>
                <ul class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm text-slate-700">
                    <li class="flex items-center gap-3"><span class="w-3 h-3 bg-accent rounded-full"></span>Giao hàng nhanh</li>
                    <li class="flex items-center gap-3"><span class="w-3 h-3 bg-accent rounded-full"></span>Bảo hành uy tín</li>
                    <li class="flex items-center gap-3"><span class="w-3 h-3 bg-accent rounded-full"></span>Hỗ trợ kỹ thuật tận nơi</li>
                </ul>
            </div>
        </div>
        <?php include __DIR__ . '/partials/slider.php'; ?>

        <!-- FEATURES -->
        <section class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="p-4 bg-gray-100 rounded-lg shadow-sm">
                <h4 class="font-semibold">Chính hãng</h4>
                <p class="text-sm text-slate-600 mt-2">Sản phẩm có nguồn gốc rõ ràng, bảo hành theo hãng.</p>
            </div>
            <div class="p-4 bg-gray-100 rounded-lg shadow-sm">
                <h4 class="font-semibold">Tư vấn chuyên sâu</h4>
                <p class="text-sm text-slate-600 mt-2">Tư vấn lựa chọn theo nhu cầu công việc và ngân sách.</p>
            </div>
            <div class="p-4 bg-gray-100 rounded-lg shadow-sm">
                <h4 class="font-semibold">Dịch vụ sửa chữa</h4>
                <p class="text-sm text-slate-600 mt-2">Sửa chữa, thay thế linh kiện nhanh, có bảo hành.</p>
            </div>
        </section>

        <!-- PRODUCTS -->
        <section class="mt-10 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <h2 id="products" class="text-2xl font-bold">Sản phẩm nổi bật</h2>
                <a href="/products" class="text-sm text-slate-600">Xem tất cả</a>
            </div>
            <div id="product-slider" class="flex mt-6 transition-transform duration-700 ease-in-out">
                <?php foreach ($products_home as $product): ?>
                    <article class="bg-white rounded-lg shadow-sm overflow-hidden min-w-[250px] max-w-[250px] mr-4">
                        <img src="<?= htmlspecialchars($product->image_url) ?>"
                            alt="<?= htmlspecialchars($product->product_name) ?>"
                            class="w-full h-40 object-cover" />
                        <div class="p-4">
                            <h3 class="font-semibold"><?= htmlspecialchars($product->product_name) ?></h3>
                            <p class="mt-1 text-sm text-slate-600 line-clamp-2">
                                <?= htmlspecialchars($product->description) ?>
                            </p>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="text-lg font-bold">
                                    <?= number_format($product->price, 0, ',', '.') ?>₫
                                </div>
                                <button class="px-3 py-1 rounded-md border text-sm focus-ring"
                                    onclick="location.href='/product/<?= $product->product_id ?>'">
                                    Xem
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- SERVICES -->
        <div id="services" class="flex items-center justify-between mt-10">
            <h2 class="text-2xl font-bold">Dịch vụ</h2>
        </div>
        <section id="services" class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <h4 class="font-semibold">Cài đặt phần mềm</h4>
                <p class="text-sm text-slate-600 mt-2">Cài Win, Office, phần mềm chuyên dụng.</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <h4 class="font-semibold">Sửa chữa tận nơi</h4>
                <p class="text-sm text-slate-600 mt-2">Kỹ thuật viên đến tận nơi khi cần.</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <h4 class="font-semibold">Tư vấn nâng cấp</h4>
                <p class="text-sm text-slate-600 mt-2">Tối ưu hiệu năng theo ngân sách.</p>
            </div>
        </section>

        </html>

        <?php
        include __DIR__ . '/partials/footer.php';
        ?>

        <!-- Script slider mượt -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const slider = document.getElementById('product-slider');
                const cards = slider.querySelectorAll('article');

                // Nhân đôi nội dung để tạo hiệu ứng liên tục
                slider.innerHTML += slider.innerHTML;

                let position = 0;
                const speed = 0.2; // tốc độ trượt (px/frame)

                function animate() {
                    position -= speed;
                    if (Math.abs(position) >= slider.scrollWidth / 2) {
                        position = 0; // quay lại đầu khi hết nửa chuỗi
                    }
                    slider.style.transform = `translateX(${position}px)`;
                    requestAnimationFrame(animate);
                }

                // Gắn style để animation mượt
                slider.style.display = 'flex';
                slider.style.willChange = 'transform';
                slider.style.transition = 'none';

                requestAnimationFrame(animate);
            });
        </script>