<!-- Quản lý sản phẩm -->
<section class="p-6 lg:p-8 border-t border-gray-200">
    <header class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Quản lý sản phẩm</h2>
        <button onclick="openAddProductModal()" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-md shadow-sm hover:bg-indigo-700 transition duration-200">
            + Thêm sản phẩm
        </button>
    </header>
    <!-- Modal thêm sản phẩm -->
    <?php include __DIR__ . '/components/add_product.php'; ?>
    <!-- Modal sửa sản phẩm -->
    <?php include __DIR__ . '/components/edit_product.php'; ?>
    <!-- Danh sách sản phẩm -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hãng</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ảnh</th>
                        <th class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($products as $sp): ?>
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-4 text-sm text-gray-900"><?= $sp->product_id ?></td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($sp->product_name) ?></td>
                            <td class="py-4 px-4 text-sm text-gray-500"><?= htmlspecialchars($sp->category) ?></td>
                            <td class="py-4 px-4 text-sm text-gray-500"><?= htmlspecialchars($sp->brand) ?></td>
                            <td class="py-4 px-4 text-sm font-medium text-indigo-600"><?= number_format($sp->price) ?>₫</td>
                            <td class="py-4 px-4 text-sm text-gray-500 text-center"><?= $sp->stock ?></td>
                            <td class="py-4 px-4">
                                <img src="<?= $sp->image_url ?>" alt="Ảnh sản phẩm" class="w-12 h-12 object-cover rounded-md border border-gray-200">
                            </td>
                            <td class="py-4 px-4 text-center space-x-2">
                                <button
                                    onclick="openEditProductModal(<?= $sp->product_id ?>)"
                                    class="px-3 py-1 bg-yellow-500 text-white text-sm font-medium rounded-md hover:bg-yellow-600 transition duration-200">
                                    Sửa
                                </button>
                                <button onclick="deleteProduct(<?= $sp->product_id ?>)"
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
    let currentEditId; // Declare global

    // Mở modal sửa sản phẩm và preload data
    async function openEditProductModal(id) {
        currentEditId = id;
        try {
            const response = await fetch(`/manager/products/edit/${id}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                } // Detect AJAX
            });
            const result = await response.json();

            if (result.success) {
                const data = result.data;
                document.getElementById('edit_product_name').value = data.product_name;
                document.getElementById('edit_category').value = data.category;
                document.getElementById('edit_brand').value = data.brand;
                document.getElementById('edit_description').value = data.description;
                document.getElementById('edit_price').value = data.price;
                document.getElementById('edit_stock').value = data.stock;
                document.getElementById('current_image_url').value = data.image_url;
                document.getElementById('edit_image_preview').src = data.image_url;

                document.getElementById('editProductModal').classList.remove('hidden');
            } else {
                alert(result.message || 'Không thể tải dữ liệu sản phẩm');
            }
        } catch (error) {
            alert('Có lỗi xảy ra khi tải dữ liệu sản phẩm');
            console.error('Error:', error);
        }
    }

    // Đóng modal sửa sản phẩm
    function closeEditProductModal() {
        document.getElementById('editProductModal').classList.add('hidden');
    }


    // Mở modal thêm sản phẩm
    function openAddProductModal() {
        document.getElementById('addProductModal').classList.remove('hidden');
    }

    // Đóng modal thêm sản phẩm
    function closeAddProductModal() {
        document.getElementById('addProductModal').classList.add('hidden');
    }

    // Xóa sản phẩm
    function deleteProduct(id) {
        if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
            window.location.href = '/manager/products/delete/' + id;
        }
    }
</script>

</body>

</html>