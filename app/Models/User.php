<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 1. KAI BÁO SỬ DỤNG BỘ CÔNG CỤ CỦA SANCTUM Ở ĐÂY
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // 2. GẮN CÔNG CỤ ĐÓ VÀO BÊN TRONG CLASS
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone', // Đã đổi email thành phone
        'password',
        'role',  // Đã thêm phân quyền
        'zalo_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
