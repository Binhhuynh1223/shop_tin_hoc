<!-- Modal thêm sản phẩm -->
<div id="addProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Thêm sản phẩm mới</h3>
            <form id="addProductForm" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
                    <input type="text" name="product_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Loại sản phẩm</label>
                    <input type="text" name="category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hãng</label>
                    <input type="text" name="brand" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Giá</label>
                    <input type="number" name="price" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Số lượng</label>
                    <input type="number" name="stock" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hình ảnh</label>
                    <input type="file" name="image" accept="image/*" class="mt-1 block w-full">
                </div>
                <div class="flex justify-end space-x-3 mt-5">
                    <button type="button" onclick="closeAddProductModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Thêm sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Xử lý submit form thêm sản phẩm
    document.getElementById('addProductForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        try {
            const formData = new FormData(this);
            const response = await fetch('/manager/products/create', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert('Thêm sản phẩm thành công');
                window.location.reload();
            } else {
                alert(result.message || 'Có lỗi xảy ra khi thêm sản phẩm');
            }
        } catch (error) {
            alert('Có lỗi xảy ra khi thêm sản phẩm');
            console.error('Error:', error);
        }
    });
</script>