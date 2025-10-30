<!-- Modal sửa người dùng -->
<div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Sửa thông tin người dùng</h3>
            <form id="editUserForm" class="mt-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_username">
                        Tên người dùng
                    </label>
                    <input type="text" id="edit_username" name="username"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_password">
                        Mật khẩu
                    </label>
                    <input type="password" id="edit_password" name="password"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_role">
                        Vai trò
                    </label>
                    <select id="edit_role" name="role"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Avatar hiện tại
                    </label>
                    <img id="edit_avatar" src="" alt="Current Avatar" class="w-20 h-20 object-cover rounded-full mx-auto">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_new_avatar">
                        Thay đổi avatar
                    </label>
                    <input type="file" id="edit_new_avatar" name="avatar" accept="image/*"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="flex items-center justify-between mt-6">
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
            const response = await fetch(`/manager/users/update/${currentUserId}`, {
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