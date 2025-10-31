<!-- Modal sửa người dùng -->
<div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Sửa thông tin người dùng</h3>
            <form id="editUserForm" class="mt-4 space-y-3">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tên đăng nhập</label>
                    <input type="text" id="edit_username" name="username"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Mật khẩu mới</label>
                    <input type="password" id="edit_new_password" name="password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        placeholder="Bỏ trống nếu không muốn thay đổi">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Họ và tên</label>
                    <input type="text" id="edit_full_name" name="full_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="edit_email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                    <input type="text" id="edit_phone" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Địa chỉ</label>
                    <textarea id="edit_address" name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Vai trò</label>
                    <select id="edit_role" name="role"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Avatar hiện tại</label>
                    <img id="edit_avatar_preview" src="" alt="Current Avatar" class="w-20 h-20 object-cover rounded-full mx-auto">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Thay đổi avatar</label>
                    <input type="file" id="edit_new_avatar" name="avatar" accept="image/*"
                        class="mt-1 block w-full">
                </div>

                <div class="flex items-center justify-between pt-4">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Cập nhật
                    </button>
                    <button type="button" onclick="closeEditUserModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('editUserForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            // Dùng POST cho update vì FormData (và file uploads)
            const response = await fetch(`/manager/users/edit/${currentUserId}`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.success) {
                alert('Cập nhật thành công!');
                window.location.reload();
            } else {
                alert(result.message || 'Có lỗi xảy ra khi cập nhật');
            }
        } catch (error) {
            alert('Có lỗi xảy ra khi cập nhật');
            console.error('Error:', error);
        }
    });
</script>