<aside class="w-64 bg-indigo-900 text-white flex flex-col shadow-xl">
    <div class="p-6 border-b border-indigo-800">
        <h2 class="text-xl font-semibold tracking-tight">Admin Dashboard</h2>
        <p class="text-indigo-300 text-xs mt-1">Shop Tin Học</p>
    </div>
    <nav class="flex-1 p-4 space-y-2">
        <a href="/manager" class="block px-4 py-2 rounded-lg hover:bg-indigo-800 transition <?= $currentPage === 'manager' ? 'bg-indigo-800' : '' ?>">Tổng quan</a>
        <a href="/manager/products" class="block px-4 py-2 rounded-lg hover:bg-indigo-800 transition <?= $currentPage === 'products' ? 'bg-indigo-800' : '' ?>">Quản lý sản phẩm</a>
        <a href="/manager/orders" class="block px-4 py-2 rounded-lg hover:bg-indigo-800 transition">Quản lý đơn hàng</a>
        <a href="/manager/users" class="block px-4 py-2 rounded-lg hover:bg-indigo-800 transition">Quản lý người dùng</a>
        <a href="/manager/reports" class="block px-4 py-2 rounded-lg hover:bg-indigo-800 transition">Báo cáo</a>
    </nav>
</aside>