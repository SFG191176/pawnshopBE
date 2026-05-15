<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Import thư viện mã hóa mật khẩu

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tạo tài khoản Chủ Tiệm (Quyền Admin)
        User::create([
            'name' => 'Sơn - Chủ Tiệm',
            'phone' => '0337989392', // Dùng SĐT thay cho Email
            'password' => Hash::make('123456'), // Mật khẩu được mã hóa an toàn
            'role' => 'admin', // Cấp quyền cao nhất
        ]);

        // 2. Tạo thử 1 tài khoản Khách hàng (Quyền Customer) để sau này test
        User::create([
            'name' => 'Cao Lê Huy',
            'phone' => '0123456789',
            'password' => Hash::make('123456'),
            'role' => 'customer', // Chỉ là khách hàng
        ]);
    }
}
