<header class="bg-white shadow-md p-4 flex justify-between items-center">
    <a class="text-2xl font-bold text-indigo-900" href="/manager">Trang Quản Lý</a>
    <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-2">
            <img src="<?= $avatar ?>" alt="Avatar" class="w-10 h-10 rounded-full border-2 border-indigo-500">
            <span class="font-medium text-gray-700"><?= htmlspecialchars($name) ?></span>
        </div>
        <a href="/logout" class="text-indigo-600 hover:text-indigo-800 transition">Đăng xuất</a>
    </div>
</header>