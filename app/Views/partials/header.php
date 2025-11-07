<?php

use App\Models\Product;
use App\Models\Service;

$products = new Product();
$desktopproducts = $products->getByCategory('Desktop') ?? [];
$laptopproducts = $products->getByCategory('Laptop') ?? [];
$serverproducts = $products->getByCategory('Server') ?? [];
$softwareproducts = $products->getByCategory('Software') ?? [];
$routerproducts = $products->getByCategory('Router') ?? [];
$printerproducts = $products->getByCategory('Printer') ?? [];


$brand = $products->all()->toArray() ?? [];
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Shop Tin Học</title>
    <meta name="description" content="Cửa hàng tin học, laptop, linh kiện, phụ kiện. Giao diện responsive, rõ ràng, dễ sử dụng." />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

        /* CSS for popup search*/
        .header-search-results {
            /* Đã đổi từ ID sang class */
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            z-index: 50;
            max-height: 400px;
            overflow-y: auto;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
            text-decoration: none;
            color: inherit;
        }

        .search-result-item:hover {
            background-color: #f8fafc;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 0.25rem;
            margin-right: 0.75rem;
        }

        .search-result-info {
            flex-grow: 1;
        }

        .search-result-info h4 {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.2;
            margin: 0;
        }

        .search-result-info p {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0;
            margin-top: 2px;
        }

        .search-result-info .price {
            font-weight: 600;
            color: #c026d3;
        }

        /* Ngăn body cuộn khi menu di động mở */
        body.mobile-menu-open {
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-gray-50 text-slate-800">
    <header class="bg-white shadow-sm sticky top-0 z-40">

        <div id="mobile-search-overlay" class="hidden md:hidden absolute top-0 left-0 right-0 h-16 bg-white z-50 p-2 shadow-sm">
            <div class="relative flex items-center bg-gray-100 rounded-md px-2 py-1 gap-2 h-full">
                <button id="mobile-search-close-btn" class="text-sm text-slate-600 px-2 py-1 focus-ring" aria-label="Đóng tìm kiếm">
                    <i class="fas fa-arrow-left text-lg"></i>
                </button>
                <input
                    aria-label="Tìm sản phẩm"
                    type="search"
                    placeholder="Tìm kiếm..."
                    id="mobile-search-input"
                    class="w-full h-full bg-transparent outline-none text-sm px-2"
                    autocomplete="off" />
                <div id="mobile-search-results" class="header-search-results"></div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-4">
                    <a href="/" class="inline-flex items-center gap-2">
                        <div class="w-11 h-11 rounded-lg bg-brand text-accent flex items-center justify-center">
                            <i class="fas fa-laptop-code text-3xl"></i>
                        </div>
                        <div>
                            <span class="text-xl font-semibold">Shop Tin Học</span>
                        </div>
                    </a>
                </div>

                <nav class="hidden md:flex items-center gap-6 text-sm" aria-label="Main">
                    <?php
                    // Get unique brands
                    $uniqueBrands = array_unique(array_column($brand, 'brand') ?? []);
                    ?>
                    <div class="relative group inline-block">
                        <a href="/products?category=Desktop" class="hover:text-accent focus-ring inline-flex flex-col items-center" title="Xem tất cả sản phẩm">
                            <i class="fas fa-desktop"></i>
                            <span>Desktop</span>
                        </a>
                        <div class="absolute left-0 hidden group-hover:block top-[100%] w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <?php if (count($desktopproducts) > 0): ?>
                                <?php $desktopBrands = array_unique(array_column($desktopproducts, 'brand') ?? []); ?>
                                <?php foreach ($desktopBrands as $desktopBrand): ?>
                                    <a href="/products?category=Desktop&brand=<?= htmlspecialchars($desktopBrand ?? '') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= htmlspecialchars($desktopBrand ?? '') ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="relative group inline-block">
                        <a href="/products?category=Laptop" class="hover:text-accent focus-ring inline-flex flex-col items-center" title="Xem tất cả sản phẩm">
                            <i class="fas fa-laptop"></i>
                            <span>Laptop</span>
                        </a>
                        <div class="absolute left-0 hidden group-hover:block top-[100%] w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <?php if (count($laptopproducts) > 0): ?>
                                <?php $laptopBrands = array_unique(array_column($laptopproducts, 'brand') ?? []); ?>
                                <?php foreach ($laptopBrands as $laptopBrand): ?>
                                    <a href="/products?category=Laptop&brand=<?= htmlspecialchars($laptopBrand ?? '') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= htmlspecialchars($laptopBrand ?? '') ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="relative group inline-block">
                        <a href="/products?category=Server" class="hover:text-accent focus-ring inline-flex flex-col items-center" title="Xem tất cả sản phẩm">
                            <i class="fas fa-server"></i>
                            <span>Server</span>
                        </a>
                        <div class="absolute left-0 hidden group-hover:block top-[100%] w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <?php if (count($serverproducts) > 0): ?>
                                <?php $serverBrands = array_unique(array_column($serverproducts, 'brand') ?? []); ?>
                                <?php foreach ($serverBrands as $serverBrand): ?>
                                    <a href="/products?category=Server&brand=<?= htmlspecialchars($serverBrand ?? '') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= htmlspecialchars($serverBrand ?? '') ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="relative group inline-block">
                        <a href="/products?category=Router" class="hover:text-accent focus-ring inline-flex flex-col items-center" title="Xem tất cả sản phẩm">
                            <i class="fas fa-network-wired"></i>
                            <span>Router</span>
                        </a>
                        <div class="absolute left-0 hidden group-hover:block top-[100%] w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <?php if (count($routerproducts) > 0): ?>
                                <?php $routerBrands = array_unique(array_column($routerproducts, 'brand') ?? []); ?>
                                <?php foreach ($routerBrands as $routerBrand): ?>
                                    <a href="/products?category=Router&brand=<?= htmlspecialchars($routerBrand ?? '') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= htmlspecialchars($routerBrand ?? '') ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="relative group inline-block">
                        <a href="/products?category=Printer" class="hover:text-accent focus-ring inline-flex flex-col items-center" title="Xem tất cả sản phẩm">
                            <i class="fas fa-print"></i>
                            <span>Máy in</span>
                        </a>
                        <div class="absolute left-0 hidden group-hover:block top-[100%] w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <?php if (count($printerproducts) > 0): ?>
                                <?php $printerBrands = array_unique(array_column($printerproducts, 'brand') ?? []); ?>
                                <?php foreach ($printerBrands as $printerBrand): ?>
                                    <a href="/products?category=Printer&brand=<?= htmlspecialchars($printerBrand ?? '') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= htmlspecialchars($printerBrand ?? '') ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="relative group inline-block">
                        <a href="/products?category=Software" class="hover:text-accent focus-ring inline-flex flex-col items-center" title="Xem tất cả sản phẩm">
                            <i class="fas fa-code"></i>
                            <span>Phần mềm</span>
                        </a>
                        <div class="absolute left-0 hidden group-hover:block top-[100%] w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <?php if (count($softwareproducts) > 0): ?>
                                <?php $softwareBrands = array_unique(array_column($softwareproducts, 'brand') ?? []); ?>
                                <?php foreach ($softwareBrands as $softwareBrand): ?>
                                    <a href="/products?category=Software&brand=<?= htmlspecialchars($softwareBrand ?? '') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= htmlspecialchars($softwareBrand ?? '') ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </nav>

                <div class="flex items-center gap-3">

                    <div class="hidden md:flex items-center bg-gray-100 rounded-md px-2 py-1 gap-2 relative">
                        <input
                            aria-label="Tìm sản phẩm"
                            type="search"
                            placeholder="Tìm kiếm..."
                            id="header-search-input"
                            class="bg-transparent outline-none text-sm px-2"
                            autocomplete="off" />
                        <button class="text-sm text-slate-600 px-2 py-1 focus-ring">
                            <i class="fas fa-search"></i>
                        </button>
                        <div id="header-search-results" class="header-search-results"></div>
                    </div>

                    <button id="mobile-search-toggle-btn" class="md:hidden text-sm px-2 py-2 rounded-md hover:text-accent focus-ring" aria-label="Tìm kiếm">
                        <i class="fas fa-search text-xl"></i>
                    </button>
                    <a href="/cart" class="text-sm px-3 py-2 rounded-md hover:text-accent focus-ring inline-flex flex-col items-center">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="hidden md:inline">Giỏ hàng</span>
                    </a>

                    <a href="#" class="menu-toggle-btn md:hidden text-sm px-2 py-2 rounded-md hover:text-accent focus-ring" aria-label="Mở menu người dùng">
                        <?php if (isset($_SESSION['user'])): ?>
                            <img src="<?= htmlspecialchars($_SESSION['user']['avatar'] ?? 'https)://via.placeholder.com/24') ?>" alt="Avatar" class="w-6 h-6 rounded-full object-cover">
                        <?php else: ?>
                            <i class="fas fa-user text-xl"></i>
                        <?php endif; ?>
                    </a>
                    <div class="hidden md:block">
                        <?php if (isset($_SESSION['user'])): ?>
                            <div class="relative group inline-block">
                                <a href="/profile" class="flex items-center gap-2 cursor-pointer">
                                    <img src="<?= htmlspecialchars($_SESSION['user']['avatar'] ?? '') ?>"
                                        alt="Avatar"
                                        class="w-10 h-10 rounded-full border border-gray-200 object-cover">
                                    <div>
                                        <p class="text-sm font-medium"><?= htmlspecialchars($_SESSION['user']['username'] ?? '') ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($_SESSION['user']['role'] ?? '') ?></p>
                                    </div>
                                </a>
                                <div class="absolute center hidden group-hover:block top-[100%] w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                                    <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:text-accent forcus-ring">
                                        <i class="fas fa-user"></i> Xem hồ sơ
                                    </a>
                                    <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:text-accent forcus-ring">
                                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center gap-4">
                                <a href="/login" class="text-sm px-3 py-2 rounded-md bg-primary text-white hover:opacity-95 focus-ring">
                                    Đăng nhập
                                </a>
                                <a href="/register" class="text-sm px-3 py-2 rounded-md border border-primary text-primary hover:bg-primary hover:text-white focus-ring">
                                    Đăng ký
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button class="menu-toggle-btn md:hidden p-2 rounded-md focus-ring" aria-label="Mở menu">
                        <i id="menuIcon" class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu"
            class="hidden md:hidden fixed top-16 left-0 right-0 h-[calc(100vh-4rem)] bg-white z-50 flex flex-col transform -translate-x-full transition-transform duration-300 ease-in-out">

            <div class="p-4 border-b bg-gray-50">
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="flex items-center justify-between">
                        <a href="/profile" class="flex items-center gap-3">
                            <img src="<?= htmlspecialchars($_SESSION['user']['avatar'] ?? '') ?>"
                                alt="Avatar"
                                class="w-12 h-12 rounded-full border border-gray-200 object-cover">
                            <div>
                                <p class="text-lg font-medium"><?= htmlspecialchars($_SESSION['user']['username'] ?? '') ?></p>
                                <p class="text-sm text-gray-500">Xem hồ sơ của bạn</p>
                            </div>
                        </a>
                        <a href="/logout" class="hover:text-accent p-2" aria-label="Đăng xuất">
                            <i class="fas fa-sign-out-alt text-2xl"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="flex items-center gap-4">
                        <a href="/login" class="flex-1 text-center text-sm px-3 py-3 rounded-md bg-primary text-white hover:opacity-95 focus-ring">
                            Đăng nhập
                        </a>
                        <a href="/register" class="flex-1 text-center text-sm px-3 py-3 rounded-md border border-primary text-primary hover:bg-primary hover:text-white focus-ring">
                            Đăng ký
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <nav class="p-4 space-y-3 flex-1 overflow-y-auto">
                <a href="/profile/#order-history" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
                    <i class="fas fa-history w-5 text-center"></i>
                    <span>Lịch sử đơn hàng</span>
                </a>
                <a href="/products?category=Desktop" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
                    <i class="fas fa-desktop w-5 text-center"></i>
                    <span>Desktop</span>
                </a>
                <a href="/products?category=Laptop" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
                    <i class="fas fa-laptop w-5 text-center"></i>
                    <span>Laptop</span>
                </a>
                <a href="/products?category=Server" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
                    <i class="fas fa-server w-5 text-center"></i>
                    <span>Server</span>
                </a>
                <a href="/products?category=Router" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
                    <i class="fas fa-network-wired w-5 text-center"></i>
                    <span>Router</span>
                </a>
                <a href="/products?category=Printer" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
                    <i class="fas fa-print w-5 text-center"></i>
                    <span>Máy in</span>
                </a>
                <a href="/products?category=Software" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
                    <i class="fas fa-code w-5 text-center"></i>
                    <span>Phần mềm</span>
                </a>
            </nav>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // For mobile menu toggle
            const menuToggleBtns = document.querySelectorAll('.menu-toggle-btn');
            const menuIcon = document.getElementById('menuIcon');
            const mobileMenu = document.getElementById('mobile-menu');

            if (menuToggleBtns.length > 0 && mobileMenu) {
                menuToggleBtns.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const isOpen = !mobileMenu.classList.contains('hidden');

                        if (isOpen) {
                            // Đóng menu
                            mobileMenu.classList.add('-translate-x-full');
                            setTimeout(() => {
                                mobileMenu.classList.add('hidden');
                            }, 300); // Đợi animation
                            if (menuIcon) {
                                menuIcon.classList.remove('fa-times');
                                menuIcon.classList.add('fa-bars');
                            }
                            document.body.classList.remove('mobile-menu-open');
                        } else {
                            // Mở menu
                            mobileMenu.classList.remove('hidden');
                            setTimeout(() => {
                                mobileMenu.classList.remove('-translate-x-full');
                            }, 10); // Đợi render
                            if (menuIcon) {
                                menuIcon.classList.remove('fa-bars');
                                menuIcon.classList.add('fa-times');
                            }
                            document.body.classList.add('mobile-menu-open');
                        }
                    });
                });
            }

            // Search overlay for mobile
            const searchToggleBtn = document.getElementById('mobile-search-toggle-btn');
            const searchOverlay = document.getElementById('mobile-search-overlay');
            const searchCloseBtn = document.getElementById('mobile-search-close-btn');
            const searchInputMobileEl = document.getElementById('mobile-search-input');

            if (searchToggleBtn && searchOverlay && searchCloseBtn && searchInputMobileEl) {
                searchToggleBtn.addEventListener('click', function() {
                    searchOverlay.classList.remove('hidden');
                    searchInputMobileEl.focus(); // Tự động focus vào ô input
                });

                searchCloseBtn.addEventListener('click', function() {
                    searchOverlay.classList.add('hidden');
                });
            }

            // Search
            async function handleSearch(term, resultsContainer) {
                if (term.length < 1) {
                    resultsContainer.innerHTML = '';
                    resultsContainer.style.display = 'none';
                    return;
                }

                try {
                    const response = await fetch(`/products/search?q=${encodeURIComponent(term)}`);
                    const data = await response.json();

                    resultsContainer.innerHTML = ''; // Xóa kết quả cũ

                    if (data.success && data.products.length > 0) {
                        data.products.forEach(product => {
                            const priceFormatted = new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(product.price);

                            const itemHtml = `
                                <a href="/product/${product.product_id}" class="search-result-item">
                                    <img src="${product.image_url}" alt="${product.product_name}">
                                    <div class="search-result-info">
                                        <h4>${product.product_name}</h4>
                                        <p>${product.brand}</p>
                                        <p class="price">${priceFormatted}</p>
                                    </div>
                                </a>
                            `;
                            resultsContainer.innerHTML += itemHtml;
                        });
                        resultsContainer.style.display = 'block';
                    } else {
                        if (data.products && data.products.length === 0) {
                            resultsContainer.innerHTML = '<p class="p-2 text-sm text-gray-500 text-center">Không tìm thấy sản phẩm.</p>';
                            resultsContainer.style.display = 'block';
                        } else {
                            resultsContainer.style.display = 'none';
                        }
                    }
                } catch (error) {
                    console.error('Lỗi tìm kiếm:', error);
                    resultsContainer.style.display = 'none';
                }
            }

            // Desktop search
            const searchInputDesktop = document.getElementById('header-search-input');
            const searchResultsDesktop = document.getElementById('header-search-results');
            if (searchInputDesktop) {
                searchInputDesktop.addEventListener('keyup', function() {
                    handleSearch(this.value.trim(), searchResultsDesktop);
                });
            }

            // Mobile search
            const searchInputMobile = document.getElementById('mobile-search-input');
            const searchResultsMobile = document.getElementById('mobile-search-results');
            if (searchInputMobile) {
                searchInputMobile.addEventListener('keyup', function() {
                    handleSearch(this.value.trim(), searchResultsMobile);
                });
            }

            // Hide popup when clicking outside
            document.addEventListener('click', function(e) {
                if (searchInputDesktop && searchResultsDesktop && !searchInputDesktop.parentElement.contains(e.target)) {
                    searchResultsDesktop.style.display = 'none';
                }
                if (searchInputMobile && searchResultsMobile && !searchInputMobile.parentElement.parentElement.contains(e.target)) {
                    searchResultsMobile.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>