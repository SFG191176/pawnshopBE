<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentHistoryController;

// ==========================================
// 1. CỔNG PUBLIC (Không cần đăng nhập)
// ==========================================
Route::post('/login', [AuthController::class, 'login']);

// Đã bế API lấy danh sách hợp đồng ra đây để ai cũng xem được đồ thanh lý!
Route::get('/contracts', [ContractController::class, 'index']);

// ==========================================
// 2. KHU VỰC BẢO MẬT CẤP 1 (Bắt buộc phải có Token đăng nhập)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // --- KHU VỰC CHUNG (Khách hàng & Chủ tiệm đều dùng được) ---
    Route::get('/my-contracts', [ContractController::class, 'myContracts']);
    Route::get('/my-payment-history', [PaymentHistoryController::class, 'index']);
    Route::get('/my-notifications', [NotificationController::class, 'index']);
    Route::post('/mark-notifications-read', [NotificationController::class, 'markAsRead']);

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);

    // ==========================================
    // 3. KHU VỰC BẢO MẬT CẤP 2 (Tuyệt mật - CHỈ ADMIN MỚI ĐƯỢC VÀO)
    // ==========================================
    Route::middleware('admin')->group(function () {

        // Các thao tác quản lý hợp đồng
        Route::post('/contracts', [ContractController::class, 'store']);
        Route::put('/contracts/{id}', [ContractController::class, 'update']);
        Route::put('/contracts/{id}/renew', [ContractController::class, 'renew']);
        Route::post('/contracts/{id}/sell', [ContractController::class, 'updateForSale']);

        // API Chốt Bán Thực Tế
        Route::post('/contracts/{id}/close-sale', [ContractController::class, 'closeSale']);

        // API Xóa Ảnh Cũ trong Bộ sưu tập
        Route::delete('/contract-images/{id}', [ContractController::class, 'deleteImage']);

        // Thống kê doanh thu, tài sản
        Route::get('/statistics', [ContractController::class, 'statistics']);

    });

});
