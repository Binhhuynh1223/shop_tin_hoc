<?php
// Extract data from controller
extract($adminInfo); // $name, $avatar
if (isset($dashboardData)) extract($dashboardData);
if (isset($products)) extract(['products' => $products]);
if (isset($users)) extract(['users' => $users]);
if (isset($orders)) extract(['orders' => $orders]);
if (isset($topProducts)) extract(['topProducts' => $topProducts]);
if (isset($topCustomers)) extract(['topCustomers' => $topCustomers]);
if (isset($lowStockProducts)) extract(['lowStockProducts' => $lowStockProducts]);


// Determine current request path without querystring and set a page slug
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$currentPage = '';
if ($path === '/manager') {
    $currentPage = 'manager';
} elseif (strpos($path, '/manager/products') === 0) {
    $currentPage = 'products';
} elseif (strpos($path, '/manager/users') === 0) {
    $currentPage = 'users';
} elseif (strpos($path, '/manager/orders') === 0) {
    $currentPage = 'orders';
} else {
    $currentPage = 'manager';
}
?>

<!DOCTYPE html>
<html lang="vi">
<?php include __DIR__ . '/../components/head.php'; ?>

<body class="bg-gray-50 min-h-screen text-gray-800 antialiased font-sans">
    <div class="flex min-h-screen relative md:static">
        <?php include __DIR__ . '/../components/sidebar.php'; ?> <main class="flex-1 overflow-auto">
            <?php include __DIR__ . '/../components/header.php'; ?> <?php
            // Include correct manager view based on slug (ignore querystring)
            if ($currentPage === 'manager') {
                include __DIR__ . '/../manager.php';
            } elseif ($currentPage === 'products') {
                include __DIR__ . '/../productManager.php';
            } elseif ($currentPage === 'users') {
                include __DIR__ . '/../userManager.php';
            } elseif ($currentPage === 'orders') {
                include __DIR__ . '/../orderManager.php';
            } else {
                include __DIR__ . '/../manager.php';
            }
            ?>
        </main>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const openBtn = document.getElementById('mobile-menu-toggle');
        const closeBtn = document.getElementById('mobile-menu-close');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        if (openBtn) {
            openBtn.addEventListener('click', openSidebar);
        }
        if (closeBtn) {
            closeBtn.addEventListener('click', closeSidebar);
        }
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }
    });
</script>

</body>

</html>