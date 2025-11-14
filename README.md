# Giới thiệu:

Đây là một dự án website cửa hàng tin học được xây dựng bằng PHP thuần, theo mô hình MVC (Model-View-Controller) và sử dụng một số thư viện hiện đại để tối ưu hóa chức năng.

## Mô tả

Dự án là một cửa hàng trực tuyến chuyên bán các sản phẩm thiết bị tin học như Laptop, Desktop, Server, Phần mềm, và thiết bị mạng. Website bao gồm đầy đủ các chức năng cho cả người dùng và quản trị viên.

---

## Công nghệ sử dụng

* **Ngôn ngữ chính:** PHP
* **Cơ sở dữ liệu:** MySQL
* **Database ORM:** `illuminate/database` (Eloquent ORM)
* **Routing:** `bramus/router`
* **Quản lý biến môi trường:** `vlucas/phpdotenv`
* **Frontend:**
    * Tailwind CSS
    * JavaScript (Vanilla)
    * Chart.js (Vẽ biểu đồ)
* **Xác thực:**
    * Đăng nhập với Google (`league/oauth2-google`)
* **Thanh toán:**
    * Thanh toán khi nhận hàng
    * Tích hợp cổng thanh toán VNPAY

---

## Tính năng nổi bật

### 1. Khách hàng:

* **Xác thực:** Đăng ký, Đăng nhập, Đăng xuất.
* **Sản phẩm:**
    * Xem danh sách sản phẩm.
    * Lọc sản phẩm theo Danh mục, Hãng, và khoảng giá.
    * Tìm kiếm sản phẩm.
    * Xem chi tiết sản phẩm.
* **Đánh giá:**
    * Khách hàng chỉ được đánh giá khi đã mua hàng.
    * Thêm đánh giá (rating sao, bình luận kèm ảnh).
* **Giỏ hàng:**
    * Thêm/sửa/xóa sản phẩm.
* **Thanh toán:**
    * Hỗ trợ 2 phương thức: COD và VNPAY.
    * Trang thông báo đặt hàng thành công / thất bại.
* **Quản lý người dùng:**
    * Cập nhật thông tin cá nhân (Họ tên, email, SĐT, địa chỉ, ảnh đại diện).
    * Xem lịch sử đơn hàng.
    * Hủy các đơn hàng đang ở trạng thái "Chờ thanh toán".

### 2. Quản trị viên:

* **Bảo mật:** Cần quyền Admin để truy cập.
* **Dashboard:**
    * Thống kê tổng quan: Doanh thu, tổng đơn hàng, sản phẩm, người dùng.
    * Biểu đồ doanh thu 12 tháng gần nhất.
    * Danh sách Top 5 sản phẩm bán chạy, Top 5 khách hàng chi tiêu nhiều nhất, và sản phẩm sắp hết hàng.
* **Quản lý Sản phẩm (CRUD):**
    * Tìm tên sản phẩm.
    * Thêm sản phẩm mới.
    * Sửa thông tin sản phẩm.
    * Xóa sản phẩm.
* **Quản lý Người dùng (CRUD):**
    * Tìm tên người dùng
    * Thêm người dùng mới.
    * Sửa thông tin người dùng.
    * Xóa người dùng.
* **Quản lý Đơn hàng:**
    * Xem danh sách đơn hàng.
    * Lọc đơn hàng theo khách hàng, trạng thái đặt hàng, ngày đặt.
    * Xác nhận "Hoàn thành" cho các đơn thanh toán khi nhận hàng.
    * Tạo và in hóa đơn (HTML/CSS) cho từng đơn hàng.

---

## Cài đặt

### Yêu cầu

* PHP
* MySQL
* Composer
* Máy chủ web (Apache hoặc Nginx)

### Các bước cài đặt

1.  **Clone repository:**
    ```bash
    git clone [URL_REPOSITORY]
    cd [TEN_THU_MUC_DU_AN]
    ```

2.  **Cài đặt các thư viện:**
    ```bash
    composer install
    ```

3.  **Cấu hình biến môi trường:**
    * Tạo một tệp `.env` ở thư mục gốc.
    * Sao chép nội dung từ `.env.example` rồi sửa thông tin cấu hình.

5.  **Cấu hình Web Server:**
    * Trỏ "Document Root" của máy chủ vào thư mục gốc.
    * Đảm bảo `mod_rewrite` (đối với Apache) đã được bật để tệp `.htaccess` có thể hoạt động và xử lý URL routing.
    * Tệp `.htaccess` sẽ chuyển hướng tất cả các yêu cầu đến `index.php`.

6.  **Phân quyền thư mục:**
    * Đảm bảo máy chủ có quyền ghi vào các thư mục `public/images/avatars`, `public/images/products`, và `public/images/reviews` để có thể tải ảnh lên.
    ```bash
    chmod -R 775 public/images/avatars
    chmod -R 775 public/images/products
    chmod -R 775 public/images/reviews
    ```

---

## Cấu trúc Database

Dự án sử dụng 7 bảng chính:

* `users`: Lưu thông tin người dùng (khách hàng và admin).
* `products`: Lưu thông tin sản phẩm.
* `cart`: Lưu thông tin giỏ hàng (liên kết với `user_id`).
* `cart_items`: Lưu các sản phẩm trong giỏ hàng (liên kết với `cart_id` và `product_id`).
* `orders`: Lưu thông tin đơn hàng (trạng thái, địa chỉ, tổng tiền...).
* `order_items`: Lưu các sản phẩm trong một đơn hàng (snapshot giá tại thời điểm mua).
* `reviews`: Lưu các đánh giá sản phẩm (liên kết với `user_id` và `product_id`).
