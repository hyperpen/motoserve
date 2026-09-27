<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'total_amount',
        'pickup_date',
        'pickup_time',
        'pickup_code',
        'status',
        'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(
            User::class,
            'customer_id'
        );
    }

    public function items()
    {
        return $this->hasMany(
            OrderItem::class
        );
    }
}