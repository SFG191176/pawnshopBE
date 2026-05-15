# DANH SÁCH API ENDPOINTS
Base URL: `http://backend.test/api/`

## 1. Xác thực (Authentication)
* `POST /login`: Xử lý đăng nhập, trả về Token.
* `POST /logout`: Hủy Token, thoát tài khoản.

## 2. Quản lý Hợp đồng (Dành cho Admin)
* `GET /contracts`: Lấy danh sách toàn bộ hợp đồng kèm ảnh.
* `POST /contracts`: Tạo hợp đồng mới (Sử dụng `FormData` để upload nhiều ảnh).
* `PUT /contracts/{id}`: Cập nhật trạng thái hợp đồng (Chuộc đồ, Thanh lý).
* `PUT /contracts/{id}/renew`: Gia hạn hợp đồng, tính lại hạn đóng lãi và lưu lịch sử.
* `POST /contracts/{id}/sale`: Cập nhật giá bán thanh lý và thêm ảnh bổ sung.
* `PUT /contracts/{id}/close-sale`: Chốt bán tài sản thanh lý.

## 3. Chức năng Khách hàng (Dành cho Customer)
Yêu cầu Header: `Authorization: Bearer {token}`
* `GET /my-contracts`: Lấy danh sách hợp đồng của riêng khách hàng đang đăng nhập.
* `GET /my-payment-history`: Lấy danh sách sao kê đóng lãi/chuộc đồ của khách.
* `GET /my-notifications`: Lấy danh sách thông báo cá nhân.

## 4. Tiện ích khác
* `GET /statistics`: Lấy các con số thống kê tổng quan cho màn hình Dashboard Admin.
* `DELETE /images/{id}`: Xóa một ảnh cụ thể khỏi thư viện ảnh của hợp đồng.
