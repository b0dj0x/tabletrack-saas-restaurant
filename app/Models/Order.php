<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'table_id',
        'waiter_id',
        'status', // pending, preparing, served, completed, cancelled
        'payment_status', // pending, paid, partially_paid
        'payment_method', // cash, baridimob
        'total_price',
        'type', // dine_in, takeaway, delivery
        'customer_name',
        'customer_phone',
        'notes',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentReceipts()
    {
        return $this->hasMany(PaymentReceipt::class);
    }
}
