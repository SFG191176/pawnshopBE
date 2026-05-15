<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_id_card',
        'customer_id_date',  // Mới thêm
        'customer_id_place', // Mới thêm
        'customer_address',  // Mới thêm
        'asset_name',
        'asset_description',
        'asset_condition',   // Mới thêm
        'appraised_value',
        'loan_amount',
        'interest_rate',
        'cycle_days',
        'loan_date',
        'next_payment_date',
        'paid_interest',
        'sale_price',
        'actual_sold_price',
        'status',
        'status_color',
        'image'
    ];

    public function images()
    {
        return $this->hasMany(ContractImage::class);
    }
}
