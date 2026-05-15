<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo Admin thật
        User::create([
            'name' => 'Chủ Tiệm Mạnh',
            'email' => 'admin@pawnpro.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Tạo Khách mẫu
        User::create([
            'name' => 'Nguyễn Văn Khách',
            'email' => 'khach@pawnpro.com',
            'password' => Hash::make('khach123'),
            'role' => 'customer',
        ]);
    }
}
