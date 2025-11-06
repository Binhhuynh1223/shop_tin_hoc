<?php include __DIR__ . '/partials/header.php'; ?>

<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 mb-12">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-red-600 p-6 text-white text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h1 class="text-3xl font-bold mt-4">Đặt hàng không thành công!</h1>
            <p class="mt-2 text-red-100">Đã có lỗi xảy ra trong quá trình xử lý đơn hàng của bạn.</p>
        </div>

        <div class="p-6 md:p-8 text-center space-y-6">
            <h2 class="text-xl font-semibold text-gray-800">Đơn hàng đã được hủy</h2>

            <p class="text-gray-600">
                Rất tiếc, quá trình thanh toán cho đơn hàng của bạn đã thất bại hoặc bị hủy.
                <br>
                Đơn hàng này đã được tự động hủy và các sản phẩm đã được hoàn trả về kho.
            </p>

            <p class="text-gray-600">
                Vui lòng kiểm tra lại thông tin thanh toán hoặc thử lại với phương thức khác.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4 pt-4 border-t">
                <a href="/cart" class="w-full sm:w-auto text-center px-6 py-3 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700 focus-ring">
                    Quay về giỏ hàng
                </a>
                <a href="/" class="w-full sm:w-auto text-center px-6 py-3 bg-gray-200 text-gray-800 rounded-md font-medium hover:bg-gray-300 focus-ring">
                    Về trang chủ
                </a>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>