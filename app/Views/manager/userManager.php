<!-- Quản lý user -->
<section class="p-6 lg:p-8 border-t border-gray-200">
    <header class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Quản lý người dùng</h2>
        <button onclick="openAddUserModal()" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-md shadow-sm hover:bg-indigo-700 transition duration-200">
            + Thêm người dùng
        </button>
    </header>
    <!-- Modal thêm người dùng -->
    <?php include __DIR__ . '/components/add_user.php'; ?>
    <!-- Modal sửa người dùng -->
    <?php include __DIR__ . '/components/edit_user.php'; ?>
    <!-- Danh sách người dùng -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mật khẩu</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avatar</th>
                        <th class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-4 text-sm text-gray-900"><?= $user->id ?></td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($user->username) ?></td>
                            <td class="py-4 px-4 text-sm text-gray-500"><?= htmlspecialchars($user->password) ?></td>
                            <td class="py-4 px-4 text-sm text-gray-500"><?= htmlspecialchars($user->role) ?></td>
                            <td class="py-4 px-4">
                                <img src="<?= $user->avatar ?>" alt="Avatar" class="w-12 h-12 object-cover rounded-md border border-gray-200">
                            </td>
                            <td class="py-4 px-4 text-center space-x-2">
                                <button
                                    onclick="openEditUserModal(<?= $user->id ?>)"
                                    class="px-3 py-1 bg-yellow-500 text-white text-sm font-medium rounded-md hover:bg-yellow-600 transition duration-200">
                                    Sửa
                                </button>
                                <button onclick="deleteUser(<?= $user->id ?>)"
                                    class="px-3 py-1 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-600 transition duration-200">
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
</main>
</div>

<script>
    let currentUserId; // Declare global

    // Mở modal sửa người dùng và preload data
    async function openEditUserModal(id) {
        currentUserId = id;
        try {
            const response = await fetch(`/manager/users/edit/${id}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                } // Detect AJAX
            });
            const result = await response.json();

            if (result.success) {
                const data = result.data;
                document.getElementById('edit_username').value = data.username;
                document.getElementById('edit_password').value = data.password;
                document.getElementById('edit_role').value = data.role;
                document.getElementById('edit_avatar').src = data.avatar;

                document.getElementById('editUserModal').classList.remove('hidden');
            } else {
                alert(result.message || 'Không thể tải dữ liệu người dùng');
            }
        } catch (error) {
            alert('Có lỗi xảy ra khi tải dữ liệu người dùng');
            console.error('Error:', error);
        }
    }

    // Đóng modal sửa người dùng
    function closeEditUserModal() {
        document.getElementById('editUserModal').classList.add('hidden');
    }


    // Mở modal thêm người dùng
    function openAddUserModal() {
        document.getElementById('addUserModal').classList.remove('hidden');
    }

    // Đóng modal thêm người dùng
    function closeAddUserModal() {
        document.getElementById('addUserModal').classList.add('hidden');
    }

    // Xóa người dùng
    function deleteUser(id) {
        if (confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
            window.location.href = '/manager/users/delete/' + id;
        }
    }
</script>

</body>

</html>