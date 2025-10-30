<!-- Modal sửa sản phẩm -->
<div id="editProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sửa sản phẩm</h3>
            <form id="editProductForm" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="current_image_url" id="current_image_url">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
                    <input type="text" name="product_name" id="edit_product_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Loại sản phẩm</label>
                    <input type="text" name="category" id="edit_category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hãng</label>
                    <input type="text" name="brand" id="edit_brand" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                    <textarea name="description" id="edit_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Giá</label>
                    <input type="number" name="price" id="edit_price" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Số lượng</label>
                    <input type="number" name="stock" id="edit_stock" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hình ảnh hiện tại</label>
                    <img id="edit_image_preview" src="" alt="Preview" class="w-24 h-24 object-cover rounded-md border border-gray-200 mb-2">
                    <label class="block text-sm font-medium text-gray-700">Chọn hình ảnh mới (nếu muốn thay)</label>
                    <input type="file" name="image" accept="image/*" class="mt-1 block w-full">
                </div>
                <div class="flex justify-end space-x-3 mt-5">
                    <button type="button" onclick="closeEditProductModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Cập nhật sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Xử lý submit form sửa sản phẩm
    document.getElementById('editProductForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('edit_product_name').dataset.id || ''; // Có thể lưu id nếu cần, nhưng dùng từ URL

        // Lấy id từ context (ví dụ: lưu global hoặc từ modal attr), nhưng để đơn giản, giả sử lấy từ current URL hoặc lưu khi open
        // Ở đây, tôi giả sử bạn lưu id khi open: thêm let currentEditId; in openEditProductModal: currentEditId = id;

        try {
            const formData = new FormData(this);
            const response = await fetch(`/manager/products/edit/${currentEditId}`, { // Sử dụng currentEditId
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert('Cập nhật sản phẩm thành công');
                window.location.reload();
            } else {
                alert(result.message || 'Có lỗi xảy ra khi cập nhật sản phẩm');
            }
        } catch (error) {
            alert('Có lỗi xảy ra khi cập nhật sản phẩm');
            console.error('Error:', error);
        }
    });
</script>