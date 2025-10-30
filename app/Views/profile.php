<?php

use App\Middleware\AuthMiddleware;
use App\Models\User;

// Require authentication
AuthMiddleware::requireAuth();

$model = new User();
$user = $model->find($_SESSION['user']['id']);

include __DIR__ . '/partials/header.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <section class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold mb-6">Thông tin cá nhân</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Avatar section -->
            <div class="text-center">
                <img
                    id="avatar-preview"
                    src="<?= htmlspecialchars($user->avatar ?? 'https://tse4.mm.bing.net/th/id/OIP.ouH9rIJGbsiTDhM6JVKy8AHaHa') ?>"
                    alt="Avatar"
                    class="w-32 h-32 rounded-full mx-auto object-cover mb-4" />
                <label for="avatar-upload" class="cursor-pointer inline-block px-4 py-2 bg-accent text-white rounded-md text-sm font-medium hover:bg-accent-dark focus-ring">
                    Tải ảnh lên
                </label>
                <p class="text-sm text-red-600 mt-2">*.jpg, .png, tối đa 2MB</p>
            </div>

            <!-- Information form -->
            <div class="md:col-span-2">
                <form action="/profile/update" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="file" name="avatar" accept="image/*" class="hidden" id="avatar-upload" />

                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700">Tên đăng nhập</label>
                        <input
                            type="text"
                            id="username"
                            value="<?= htmlspecialchars($user->username) ?>"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-accent focus:ring-accent sm:text-sm bg-slate-100 cursor-not-allowed"
                            disabled />
                    </div>

                    <div>
                        <label for="full_name" class="block text-sm font-medium text-slate-700">Họ và tên</label>
                        <input
                            type="text"
                            name="full_name"
                            id="full_name"
                            value="<?= htmlspecialchars($user->full_name ?? '') ?>"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-accent focus:ring-accent sm:text-sm" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="<?= htmlspecialchars($user->email ?? '') ?>"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-accent focus:ring-accent sm:text-sm" />
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700">Số điện thoại</label>
                        <input
                            type="tel"
                            name="phone"
                            id="phone"
                            value="<?= htmlspecialchars($user->phone ?? '') ?>"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-accent focus:ring-accent sm:text-sm" />
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-slate-700">Địa chỉ (dùng cho giao hàng)</label>
                        <textarea
                            name="address"
                            id="address"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-accent focus:ring-accent sm:text-sm"><?= htmlspecialchars($user->address ?? '') ?></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="px-6 py-2 bg-accent text-white rounded-md font-medium hover:bg-accent-dark focus-ring">
                            Cập nhật thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
    // Preview avatar
    const uploadInput = document.getElementById('avatar-upload');
    const previewImg = document.getElementById('avatar-preview');

    uploadInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<?php
include __DIR__ . '/partials/footer.php';
?>