# TÀI LIỆU CHỨC NĂNG HỆ THỐNG PAWNMASTER (V1.0)
**Chủ sở hữu:** Lương Đức Mạnh (Sơn)
**Phiên bản:** 1.1.0 (Cập nhật hệ thống Trợ lý ảo & Cảnh báo)

## 1. HỆ THỐNG CỐT LÕI & BẢO MẬT (CORE & SECURITY)
* **Xác thực người dùng (Authentication):** Đăng nhập bằng Số điện thoại và Mật khẩu. 
* **Bảo mật Token (JWT/Sanctum):** Cấp phát "thẻ VIP" (Token) cho mỗi phiên đăng nhập, chống truy cập trái phép vào các trang nội bộ.
* **Phân quyền (Authorization):** Tự động nhận diện Role (Vai trò) để phân luồng: Admin vào Bàn làm việc, Khách hàng vào trang Theo dõi hợp đồng.
* **Đăng xuất an toàn:** Tiêu hủy Token trên Server và xóa dữ liệu trình duyệt (Local Storage) để bảo vệ tài khoản.

## 2. PHÂN HỆ QUẢN TRỊ (ADMIN - CHỦ TIỆM)
* **Bảng điều khiển (Dashboard):**
    * Thống kê Tổng vốn đang cho vay (tự động tính toán theo thời gian thực).
    * Bộ đếm: Số hợp đồng đang cầm & Số tài sản đã thanh lý.
* **Quản lý Hợp đồng Cầm đồ:**
    * **Tạo Hợp đồng:** Nhập thông tin Khách hàng (Họ tên, SĐT, CCCD, Địa chỉ) và Tài sản (Tên, Mô tả, Tình trạng, Giá trị định giá, Số tiền vay, Lãi suất, Chu kỳ).
    * **Auto-fill Thông minh:** Tự động gọi lại thông tin khách hàng cũ nếu nhập trùng SĐT hoặc CCCD.
    * **Upload Hình ảnh (Đa phương tiện):** Hỗ trợ tải lên cùng lúc nhiều ảnh, tích hợp bật Camera trực tiếp trên điện thoại/tablet, kèm lưới xem trước và nút xóa ảnh.
    * **Thuật toán Đếm ngày (Đèn giao thông):** Tự động đổi màu trạng thái (Xanh: An toàn, Vàng: Sắp tới hạn, Đỏ: Quá hạn, Đỏ nhấp nháy: Quá hạn 5 ngày - Chờ thanh lý).
    * **Đóng Lãi (Gia hạn):** Tự động tính tiền lãi theo chu kỳ mới và cập nhật hạn đóng lãi tiếp theo.
    * **Chuộc Đồ / Thanh Lý:** Nút chuyển đổi trạng thái nhanh chóng.
    * **In Hợp Đồng:** Tự động đổ dữ liệu vào Form Hóa đơn HTML chuẩn giấy A4/A5, tối ưu lề máy in, để sẵn dòng chấm cho khách ký tên.
    * **Xem Chi Tiết:** Bảng xem lại thông tin Hợp đồng và Hiển thị Lưới ảnh (Gallery) của tài sản.
* **Quản lý Cửa Hàng Thanh Lý (Liquidated Store):** Đưa các món đồ quá hạn ra mặt tiền, gắn giá bán và chốt bán.
* **Sổ Quản Lý (Ledger & Customer):** Lưu trữ hồ sơ thông tin khách hàng và lịch sử thu chi của tiệm.

## 3. PHÂN HỆ KHÁCH HÀNG (CUSTOMER PORTAL)
* **Theo dõi Hợp đồng cá nhân:** Khách đăng nhập để xem danh sách tài sản đang cầm của riêng mình. Không nhìn thấy dữ liệu của người khác.
* **Thanh toán Online (VietQR):** Nút "Thanh toán lãi" tự động bung mã QR tích hợp sẵn Ngân hàng MB Bank (STK của chủ tiệm), tự động điền sẵn chính xác Số tiền lãi và Nội dung chuyển khoản (VD: HD01 Dong lai Xe May).
* **Sao kê Giao dịch (Payment History):** Bảng lịch sử ghi nhận mọi lần đóng lãi, chuộc đồ với thời gian và số tiền minh bạch.
* **Hệ thống Thông báo (Notification):** * Nút chuông báo đỏ, hiển thị tin nhắn xác nhận khi chủ tiệm đã thu tiền lãi và gia hạn thành công.
    * Giao diện UI phân loại màu sắc thông minh dựa trên mức độ quan trọng: Xanh (Thông tin), Vàng (Nhắc nhở), Đỏ (Cảnh báo nguy hiểm).

## 4. PHÂN HỆ KHÁCH VÃNG LAI (PUBLIC STOREFRONT)
* **Mặt tiền Cửa hàng:** Nơi trưng bày danh sách các tài sản đã thanh lý để khách vãng lai (không cần tài khoản) có thể vào xem, chọn mua. Giao diện trực quan, hỗ trợ lọc và tìm kiếm mặt hàng.

## 5. HỆ THỐNG TỰ ĐỘNG HÓA & CẢNH BÁO (TRỢ LÝ ẢO)
* **Tự động quét hạn (Task Scheduling):** Hệ thống được cấu hình chạy ngầm mỗi ngày (vào lúc 00:00) để kiểm tra các hợp đồng đang cầm cố.
* **Phân loại cảnh báo thông minh:**
    * **Nhắc nhở (Reminder):** Tự động gửi thông báo trước 1 đến 3 ngày khi đến hạn đóng lãi để khách hàng chuẩn bị tài chính.
    * **Cảnh báo (Warning):** Gửi thông báo khẩn cấp ngay khi hợp đồng chuyển sang trạng thái quá hạn, cảnh báo rủi ro thanh lý tài sản.
* **Thuật toán Chống Spam:** Tự động kiểm tra lịch sử gửi thông báo, đảm bảo mỗi khách hàng chỉ nhận tối đa 1 thông báo/loại/ngày cho mỗi hợp đồng, tối ưu hóa trải nghiệm người dùng.
