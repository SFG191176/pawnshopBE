# TÀI LIỆU KIẾN TRÚC CƠ SỞ DỮ LIỆU (DATABASE SCHEMA)
**Dự án:** Cầm Đồ Sơn Hoàng 

---

## 1. TỪ ĐIỂN MIGRATION (LỊCH SỬ TẠO BẢNG)
*Thư mục chứa: `database/migrations/`*
*Lưu ý: Không đổi tên các file này để tránh làm hỏng cấu trúc cập nhật của Laravel.*

### A. Các file Mặc định của Laravel (Hệ thống cốt lõi)
* `..._create_users_table.php`: Tạo bảng `users` (Quản lý tài khoản Admin/Chủ tiệm và Khách hàng).
* `..._create_password_reset_tokens_table.php`: Tạo bảng hỗ trợ chức năng cấp lại mật khẩu.
* `..._create_failed_jobs_table.php`: Tạo bảng lưu trữ các tiến trình chạy ngầm bị lỗi (Queue).
* `..._create_personal_access_tokens_table.php`: Tạo bảng lưu trữ Token (Thẻ VIP) cho tính năng đăng nhập API bằng Sanctum.

### B. Các file Nghiệp vụ Cầm Đồ (Tính năng chính)
* `..._create_contracts_table.php`: Tạo bảng `contracts` (Lưu thông tin cốt lõi của Hợp đồng cầm đồ).
* `..._create_contract_images_table.php`: Tạo bảng `contract_images` (Lưu đường dẫn thư viện ảnh của tài sản, hỗ trợ tải nhiều ảnh).
* `..._create_payment_histories_table.php`: Tạo bảng `payment_histories` (Lưu sao kê lịch sử đóng lãi, chuộc đồ của khách).
* `..._create_notifications_table.php`: Tạo bảng `notifications` (Lưu trữ tin nhắn, cảnh báo gửi đến khách hàng).

### C. Các file Cập nhật / Bản vá (Migrations bổ sung)
* `2026_04_01_052816_add_new_fields_to_contracts_table.php`: Bổ sung thêm các cột mới (SĐT, CCCD...) vào bảng Hợp đồng mà không làm mất dữ liệu cũ.
* `2026_04_20_071842_add_type_to_notifications_table.php`: Bổ sung cột `type` vào bảng Thông báo để phân loại mức độ cảnh báo (info, reminder, warning).

---

## 2. CẤU TRÚC CHI TIẾT CÁC BẢNG NGHIỆP VỤ (TABLES)

### 2.1. Bảng `users` (Người dùng)
* `id`: Khóa chính.
* `name`: Tên người dùng / Tên khách hàng.
* `phone`: Số điện thoại (Dùng làm tài khoản đăng nhập thay cho email).
* `password`: Mật khẩu (Đã mã hóa bcrypt).
* `role`: Phân quyền (Gồm 2 loại: `admin` (Chủ tiệm) hoặc `customer` (Khách hàng)).

### 2.2. Bảng `contracts` (Hợp đồng cầm đồ)
* `id`: Khóa chính (Mã hợp đồng).
* `user_id`: Khóa ngoại (Khách hàng sở hữu hợp đồng này).
* `asset_name`: Tên tài sản (VD: iPhone 15 Pro Max, Xe máy SH...).
* `asset_description`: Mô tả tình trạng tài sản.
* `loan_amount`: Số tiền cầm / vay gốc.
* `interest_rate`: Lãi suất (%).
* `cycle_days`: Số ngày của một chu kỳ đóng lãi.
* `loan_date`: Ngày bắt đầu cầm đồ.
* `next_payment_date`: Ngày hẹn đóng lãi tiếp theo (Dùng để chạy thuật toán Đèn giao thông cảnh báo).
* `status`: Trạng thái hợp đồng (`Đang cầm`, `Đã chuộc`, `Thanh lý`, `Đã bán thanh lý`).
* `sale_price`: Giá bán thanh lý (Chỉ có khi status là Thanh lý - dùng hiển thị ra Public Store).

### 2.3. Bảng `contract_images` (Thư viện ảnh tài sản)
* `id`: Khóa chính.
* `contract_id`: Khóa ngoại (Bức ảnh này thuộc về hợp đồng nào).
* `image_url`: Đường dẫn lưu ảnh trong thư mục `storage/app/public/uploads`.

### 2.4. Bảng `payment_histories` (Sao kê giao dịch)
* `id`: Khóa chính (Mã giao dịch).
* `contract_id`: Khóa ngoại (Giao dịch của hợp đồng nào).
* `user_id`: Khóa ngoại (Ai là người đóng tiền).
* `amount`: Số tiền giao dịch.
* `type`: Loại giao dịch (`Đóng lãi` hoặc `Chuộc đồ`).
* `date`: Thời gian thực hiện giao dịch.

### 2.5. Bảng `notifications` (Thông báo)
* `id`: Khóa chính.
* `user_id`: Khóa ngoại (Gửi cho khách hàng nào).
* `title`: Tiêu đề thông báo.
* `type`: Loại thông báo (`info`, `reminder`, `warning`) dùng để giao diện Frontend bắt màu sắc tương ứng (Xanh, Vàng, Đỏ).
* `message`: Nội dung chi tiết (VD: "Cảm ơn bạn đã đóng lãi thành công...").
* `is_read`: Trạng thái đọc (Boolean: true/false).

---

## 3. MỐI QUAN HỆ CƠ SỞ DỮ LIỆU (RELATIONSHIPS)
Hệ thống sử dụng các ràng buộc khóa ngoại (Foreign Key) chặt chẽ để đảm bảo tính toàn vẹn dữ liệu:

1. **User (1) - (N) Contract:** Một Khách hàng có thể có nhiều Hợp đồng cầm đồ.
2. **Contract (1) - (N) ContractImage:** Một Hợp đồng có thể có nhiều Ảnh chụp tài sản khác nhau. Khi Hợp đồng bị xóa, toàn bộ ảnh liên quan sẽ bị xóa theo (Cascade).
3. **Contract (1) - (N) PaymentHistory:** Một Hợp đồng sẽ sinh ra nhiều lần đóng lãi (Lịch sử giao dịch).
4. **User (1) - (N) Notification:** Một Khách hàng có thể nhận được nhiều Thông báo cảnh báo từ hệ thống.
