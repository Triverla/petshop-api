<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'payment_id',
        'user_id',
        'order_status_id',
        'products',
        'address',
        'delivery_fee',
        'amount',
    ];

    protected $casts = [
        'products' => 'json',
        'address' => 'json',
        'amount' => 'float',
        'delivery_fee' => 'float',
        'shipped_at' => 'timestamp'
    ];

    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id', 'uuid');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'uuid');
    }
}
