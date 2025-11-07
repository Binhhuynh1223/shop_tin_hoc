<aside id="admin-sidebar"
    class="w-64 bg-indigo-900 text-white flex flex-col shadow-xl h-screen fixed top-0 left-0 z-40
           transform -translate-x-full md:translate-x-0 
           transition-transform duration-300 ease-in-out md:sticky md:top-0">

    <div class="p-6 border-b border-indigo-800 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold tracking-tight">Admin Dashboard</h2>
            <p class="text-indigo-300 text-xs mt-1">Shop Tin Học</p>
        </div>
        <button id="mobile-menu-close" class="md:hidden p-1 rounded-md text-indigo-300 hover:bg-indigo-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <a href="/manager" class="block px-4 py-2 rounded-lg hover:bg-indigo-800 transition <?= $currentPage === 'manager' ? 'bg-indigo-800' : '' ?>">Tổng quan</a>
        <a href="/manager/products" class="block px-4 py-2 rounded-lg hover:bg-indigo-800 transition <?= $currentPage === 'products' ? 'bg-indigo-800' : '' ?>">Quản lý sản phẩm</a>
        <a href="/manager/orders" class="block px-4 py-2 rounded-lg hover:bg-indigo-800 transition <?= $currentPage === 'orders' ? 'bg-indigo-800' : '' ?>">Quản lý đơn hàng</a>
        <a href="/manager/users" class="block px-4 py-2 rounded-lg hover:bg-indigo-800 transition <?= $currentPage === 'users' ? 'bg-indigo-800' : '' ?>">Quản lý người dùng</a>
    </nav>
</aside>

<div id="sidebar-overlay" class="hidden md:hidden fixed inset-0 bg-black bg-opacity-50 z-30 transition-opacity duration-300 ease-in-out"></div>