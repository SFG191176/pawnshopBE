<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa và có phải là Chủ tiệm (admin) không
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request); // Đúng là Admin -> Mở cửa cho đi tiếp
        }

        // Nếu là Khách hàng hoặc kẻ gian -> Đuổi về ngay lập tức
        return response()->json([
            'status' => 'error',
            'message' => 'Lỗi 403: Bạn không có quyền truy cập vào khu vực này!'
        ], 403);
    }
}
