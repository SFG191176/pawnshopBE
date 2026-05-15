<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // --- 1. HÀM ĐĂNG NHẬP (CẤP TOKEN) ---
    public function login(Request $request)
    {
        // Bước 1: Kiểm tra dữ liệu gửi lên (Dùng phone thay vì email)
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // Bước 2: Tìm người dùng trong Database theo số điện thoại
        $user = User::where('phone', $request->phone)->first();

        // Bước 3: Nếu không tìm thấy user HOẶC sai mật khẩu -> Từ chối
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Số điện thoại hoặc mật khẩu không đúng rồi Mạnh ơi!'
            ], 401);
        }

        // Bước 4: Đăng nhập thành công -> Nhờ Sanctum in ra một cái "Thẻ VIP" (Token)
        $token = $user->createToken($user->role . '-token')->plainTextToken;

        // Bước 5: Trả về Token và thông tin cho Vue.js cất vào LocalStorage
        return response()->json([
            'status' => 'success',
            'message' => 'Chào mừng ' . $user->name . ' trở lại hệ thống!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role, // Trả về vai trò (admin/customer) để Vue phân luồng menu
            ],
            'access_token' => $token
        ]);
    }

    // --- 2. HÀM ĐĂNG XUẤT (THU HỒI TOKEN) ---
    public function logout(Request $request)
    {
        // Thu hồi và xóa cái thẻ Token hiện tại của người dùng đó trong CSDL
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã đăng xuất và thu hồi thẻ an toàn!'
        ]);
    }
}
