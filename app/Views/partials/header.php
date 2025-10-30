<?php

use App\Models\Product;
use App\Models\Service;

$products = new Product();
$desktopproducts = $products->getByCategory('Desktop') ?? [];
$laptopproducts = $products->getByCategory('Laptop') ?? [];
$serverproducts = $products->getByCategory('Server') ?? [];
$routerproducts = $products->getByCategory('Router') ?? [];


$brand = $products->all()->toArray() ?? [];

$services = new Service();
$services = $services->getAllServices() ?? [];
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tin Học AC</title>
    <meta name="description" content="Cửa hàng tin học, laptop, linh kiện, phụ kiện. Giao diện responsive, rõ ràng, dễ sử dụng." />
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Optional: Tailwind forms + typography -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0f172a',
                        accent: '#0ea5a4'
                    }
                }
            }
        }
    </script>
    <style>
        /* Small custom styles for focus outlines and smooth image fit */
        .product-img {
            object-fit: cover;
            height: 220px;
        }

        .focus-ring:focus {
            outline: 3px solid rgba(14, 165, 164, 0.25);
            outline-offset: 2px;
        }
    </style>
</head>

<!-- NAVBAR -->

<body class="bg-gray-50 text-slate-800">
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-4">
                    <a href="/" class="inline-flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-accent to-green-600 flex items-center justify-center text-white font-semibold">AC</div>
                        <span class="text-lg font-semibold">Tin Học AC</span>
                    </a>
                </div>

                <nav class="hidden md:flex items-center gap-6 text-sm" aria-label="Main">
                    <?php
                    // Get unique brands
                    $uniqueBrands = array_unique(array_column($brand, 'brand') ?? []);
                    ?>
                    <!-- Desktop -->
                    <div class="relative group inline-block">
                        <a href="/products/desktop" class="hover:text-accent focus-ring inline-flex items-center" title="Xem tất cả sản phẩm">
                            Desktop
                        </a>
                        <div class="absolute left-0 hidden group-hover:block top-[100%] w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <?php if (count($desktopproducts) > 0): ?>
                                <?php $desktopBrands = array_unique(array_column($desktopproducts, 'brand') ?? []); ?>
                                <?php foreach ($desktopBrands as $desktopBrand): ?>
                                    <a href="/products/desktop/<?= htmlspecialchars($desktopBrand ?? '') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= htmlspecialchars($desktopBrand ?? '') ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Laptop -->
                    <div class="relative group inline-block">
                        <a href="/products/laptop" class="hover:text-accent focus-ring inline-flex items-center" title="Xem tất cả sản phẩm">
                            Laptop
                        </a>
                        <div class="absolute left-0 hidden group-hover:block top-[100%] w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <?php if (count($laptopproducts) > 0): ?>
                                <?php $laptopBrands = array_unique(array_column($laptopproducts, 'brand') ?? []); ?>
                                <?php foreach ($laptopBrands as $laptopBrand): ?>
                                    <a href="/products/laptop/<?= htmlspecialchars($laptopBrand ?? '') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= htmlspecialchars($laptopBrand ?? '') ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Server -->
                    <div class="relative group inline-block">
                        <a href="/products/server" class="hover:text-accent focus-ring inline-flex items-center" title="Xem tất cả sản phẩm">
                            Server
                        </a>
                        <div class="absolute left-0 hidden group-hover:block top-[100%] w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <?php if (count($serverproducts) > 0): ?>
                                <?php $serverBrands = array_unique(array_column($serverproducts, 'brand') ?? []); ?>
                                <?php foreach ($serverBrands as $serverBrand): ?>
                                    <a href="/products/server/<?= htmlspecialchars($serverBrand ?? '') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= htmlspecialchars($serverBrand ?? '') ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Router -->
                    <div class="relative group inline-block">
                        <a href="/products/router" class="hover:text-accent focus-ring inline-flex items-center" title="Xem tất cả sản phẩm">
                            Router
                        </a>
                        <div class="absolute left-0 hidden group-hover:block top-[100%] w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <?php if (count($routerproducts) > 0): ?>
                                <?php $routerBrands = array_unique(array_column($routerproducts, 'brand') ?? []); ?>
                                <?php foreach ($routerBrands as $routerBrand): ?>
                                    <a href="/products/router/<?= htmlspecialchars($routerBrand ?? '') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= htmlspecialchars($routerBrand ?? '') ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Phần mềm -->
                    <a href="#" class="hover:text-accent focus-ring inline-flex items-center">Phần mềm</a>

                    <!-- Dịch vụ -->
                    <a href="#" class="hover:text-accent focus-ring inline-flex items-center">Dịch vụ</a>
                </nav>

                <!-- Tìm kiếm -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center bg-gray-100 rounded-md px-2 py-1 gap-2">
                        <input aria-label="Tìm sản phẩm" type="search" placeholder="Tìm kiếm..." class="bg-transparent outline-none text-sm px-2" />
                        <button class="text-sm text-slate-600 px-2 py-1 focus-ring">Tìm</button>
                    </div>
                    <button id="menuBtn" class="md:hidden p-2 rounded-md focus-ring" aria-label="Mở menu">☰</button>
                    <a href="/cart" class="text-sm px-3 py-2 rounded-md bg-accent text-white hover:opacity-95 focus-ring">Giỏ hàng</a>
                </div>


                <!-- User -->
                <div class="hidden md:flex items-center gap-4">
                    <?php if (isset($_SESSION['user'])): ?>
                        <a class="flex items-center gap-2" href='/profile'>
                            <img src="<?= htmlspecialchars($_SESSION['user']['avatar'] ?? '') ?>"
                                alt="Avatar"
                                class="w-10 h-10 rounded-full border border-gray-200">
                            <div>
                                <p class="text-sm font-medium"><?= htmlspecialchars($_SESSION['user']['username'] ?? '') ?></p>
                                <p class="text-xs text-gray-500"><?= htmlspecialchars($_SESSION['user']['role'] ?? '') ?></p>
                            </div>
                            <a href="/logout"
                                class="ml-4 px-3 py-1 text-sm rounded-md bg-red-500 text-white hover:bg-red-600 transition-colors">
                                Đăng xuất
                            </a>
                        </a>
                    <?php else: ?>
                        <a href="/login" class="text-sm px-3 py-2 rounded-md bg-primary text-white hover:opacity-95 focus-ring">
                            Đăng nhập
                        </a>
                        <a href="/register" class="text-sm px-3 py-2 rounded-md border border-primary text-primary hover:bg-primary hover:text-white focus-ring">
                            Đăng ký
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
</body>

</html>