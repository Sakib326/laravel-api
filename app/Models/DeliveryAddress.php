<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    use HasFactory;
    protected $table = 'deliveryaddress';

    protected $fillable = [
        'name',
        'address',
        'mobile',
        'email',
        'order_notes',
        'subtotal',
        'shipping_fee',
        'total',
        'payment_method',
        'product_snapshot',
        'status',
    ];

    protected $casts = [
        'product_snapshot' => 'array',
    ];
}
