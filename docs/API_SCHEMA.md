# CẤU TRÚC CƠ SỞ DỮ LIỆU (DATABASE SCHEMA)
Hệ thống sử dụng cơ sở dữ liệu quan hệ (MySQL). Dưới đây là cấu trúc các bảng cốt lõi:

## 1. Bảng `users` (Tài khoản)
* `id`: Khóa chính
* `name`: Tên người dùng / khách hàng
* `phone`: Số điện thoại (Dùng để đăng nhập)
* `password`: Mật khẩu đã mã hóa Hash
* `role`: Phân quyền (`admin` hoặc `customer`)

## 2. Bảng `contracts` (Hợp đồng cầm đồ)
Lưu trữ toàn bộ thông tin chi tiết về một giao dịch cầm cố.
* `id`: Mã hợp đồng
* `user_id`: Khóa ngoại liên kết bảng `users` (Chủ tài sản)
* `customer_name`, `customer_phone`, `customer_id_card`: Thông tin cơ bản
* `customer_id_date`, `customer_id_place`, `customer_address`: Thông tin định danh chi tiết (CCCD & Hộ khẩu)
* `asset_name`, `asset_description`, `asset_condition`: Thông tin và tình trạng tài sản
* `appraised_value`: Giá trị định giá
* `loan_amount`: Số tiền giải ngân (Gốc)
* `interest_rate`: Lãi suất (%)
* `cycle_days`: Chu kỳ đóng lãi (Ví dụ: 10, 15, 30 ngày)
* `loan_date`: Ngày tạo hợp đồng
* `next_payment_date`: Ngày đến hạn đóng lãi tiếp theo
* `status`: Trạng thái (Đang cầm, Đã chuộc, Thanh lý...)
* `image`: Đường dẫn ảnh đại diện sản phẩm

## 3. Bảng `contract_images` (Thư viện ảnh tài sản)
Lưu trữ nhiều ảnh cho một hợp đồng.
* `id`: Khóa chính
* `contract_id`: Khóa ngoại trỏ về `contracts`
* `image_url`: Đường dẫn file ảnh
* `is_main`: Cờ đánh dấu ảnh bìa (Boolean)

## 4. Bảng `payment_histories` (Lịch sử giao dịch)
* `id`, `user_id`, `contract_id`
* `amount`: Số tiền giao dịch
* `type`: Loại giao dịch (Đóng lãi, Chuộc đồ)
* `status`: Trạng thái giao dịch (Thành công)

## 5. Bảng `notifications` (Thông báo)
* `id`, `user_id`
* `title`, `message`: Nội dung thông báo
* `is_read`: Trạng thái đã xem chưa (Boolean)
