<header class="bg-white shadow-md p-4 flex justify-between items-center sticky top-0 z-30">
    <button id="mobile-menu-toggle" class="md:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    
    <a class="text-xl md:text-2xl font-bold text-indigo-900 hidden md:block" href="/manager">Trang Quản Lý</a>

    <div class="flex items-center space-x-4">
        <div class="hidden md:flex items-center space-x-2">
            <img src="<?= $avatar ?>" alt="Avatar" class="w-10 h-10 rounded-full border-2 border-indigo-500">
            <span class="font-medium text-gray-700"><?= htmlspecialchars($name) ?></span>
        </div>
        <a href="/logout" class="text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="hidden md:inline">Đăng xuất</span>
        </a>
    </div>
</header>