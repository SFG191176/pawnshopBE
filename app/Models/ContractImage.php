<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractImage extends Model
{
    use HasFactory;

    // Khai báo các cột được phép lưu dữ liệu vào DB
    protected $fillable = [
        'contract_id',
        'image_url',
        'is_main'
    ];
}
