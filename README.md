# PAWNMASTER - HỆ THỐNG QUẢN LÝ CẦM ĐỒ THÔNG MINH 🚀
**Tác giả:** Phùng Thái Sơn 
**Phiên bản:** 1.0.0

## 🛠 Công nghệ sử dụng
* **Frontend:** Vue.js 3, Tailwind CSS, Vue Router, Pinia.
* **Backend:** Laravel (PHP), MySQL.
* **Tích hợp:** SweetAlert2 (Thông báo), VietQR (Thanh toán).

## ⚙️ Hướng dẫn cài đặt & Chạy dự án

### Bước 1: Khởi động Két sắt (Backend - Laravel)
1. Mở Terminal tại thư mục `backend`.
2. Chạy lệnh cài đặt thư viện: `composer install`
3. Cấu hình file `.env` và kết nối Database.
4. Chạy lệnh tạo bảng: `php artisan migrate`
5. **Khởi động server:** Dự án được cấu hình chạy qua Domain ảo nội bộ (Virtual Host). Hãy bật phần mềm Laravel Herd của bạn lên. Backend sẽ tự động chạy tại: `http://backend.test`

### Bước 2: Khởi động Giao diện (Frontend - Vue)
1. Mở Terminal tại thư mục \`frontend\`.
2. Chạy lệnh cài đặt: \`npm install\`
3. Bật giao diện: \`npm run dev\`
4. Truy cập vào đường dẫn Localhost hiển thị trên Terminal.

## 📚 Tài liệu chi tiết
Vui lòng xem trong thư mục \`/docs\` để biết chi tiết về Database, API và Logic hệ thống.
