<!-- Modal thêm người dùng mới -->
<div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-10 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Thêm người dùng mới</h3>
            <form id="addUserForm" enctype="multipart/form-data" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tên đăng nhập (Bắt buộc)</label>
                    <input type="text" name="username" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mật khẩu (Bắt buộc)</label>
                    <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Họ và tên</label>
                    <input type="text" name="full_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                    <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Địa chỉ</label>
                    <textarea name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Vai trò</label>
                    <select name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Avatar</label>
                    <input type="file" name="avatar" accept="image/*" class="mt-1 block w-full">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddUserModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Thêm người dùng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Xử lý submit form thêm người dùng
    document.getElementById('addUserForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        try {
            const formData = new FormData(this);
            const response = await fetch('/manager/users/create', { //
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert('Thêm người dùng thành công');
                window.location.reload();
            } else {
                alert(result.message || 'Có lỗi xảy ra khi thêm người dùng');
            }
        } catch (error) {
            alert('Có lỗi xảy ra khi thêm người dùng');
            console.error('Error:', error);
        }
    });
</script>