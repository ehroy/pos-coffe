<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'table_id',
        'customer_name',
        'source',
        'status',
        'subtotal',
        'discount',
        'tax',
        'service_charge',
        'total',
        'payment_status',
        'payment_method',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    public function hasKitchenItems(): bool
    {
        return $this->items()
            ->whereHas('product.category', fn ($q) => $q->where('type', '!=', 'drink'))
            ->exists();
    }

    public function scopeHasKitchenItems($query)
    {
        return $query->whereHas('items.product.category', fn ($q) => $q->where('type', '!=', 'drink'));
    }
}
