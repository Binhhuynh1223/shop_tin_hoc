<?php
// Extract data from controller
extract($adminInfo); // $name, $avatar
if (isset($dashboardData)) extract($dashboardData);
if (isset($products)) extract(['products' => $products]);
if (isset($users)) extract(['users' => $users]);

$currentPage = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="vi">
<?php include __DIR__ . '/../components/head.php'; ?>

<body class="bg-gray-50 min-h-screen text-gray-800 antialiased font-sans">
    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../components/sidebar.php'; ?> <!-- Truyền $currentPage vào sidebar -->

        <main class="flex-1 overflow-auto">
            <?php include __DIR__ . '/../components/header.php'; ?> <!-- Sử dụng $name, $avatar -->
            <?php
            if ($currentPage === '/manager') {
                include __DIR__ . '/../manager.php';
            } elseif ($currentPage === '/manager/products') {
                include __DIR__ . '/../productManager.php';
            } elseif ($currentPage === '/manager/users') {
                include __DIR__ . '/../userManager.php';
            // elseif( $currentPage === 'orders') {
            //     include __DIR__ . '/orders.php';
        }
            ?>
        </main>
    </div>
</body>

</html>