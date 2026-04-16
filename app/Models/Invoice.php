<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'user_id',
        'customer_name',
        'car_model',
        'car_variant',
        'vin_chassis_no',
        'price',
        'accessories',
        'tax',
        'discount',
        'net_amount',
        'qr_code',
        'pdf_path',
        'qr_path',
        'accessories_addons'
        
    ];

    protected $casts = [
        'price'      => 'float',
        'tax'        => 'float',
        'discount'   => 'float',
        'net_amount' => 'float',
    ];

    /**
     * Invoice belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
