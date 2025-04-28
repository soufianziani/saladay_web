<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            if (!$orderItem->price) {
                $orderItem->price = $orderItem->product->price;
            }
            $orderItem->price = $orderItem->price * $orderItem->quantity;
        });

        static::created(function ($orderItem) {
            // Update order total
            $order = $orderItem->order;
            if ($order) {
                $total = $order->orderItems()->sum('price');
                $order->total = $total - ($order->discount ?? 0);
                $order->mnt_rendu = max(0, $order->mnt_recu - $order->total);
                $order->save();
            }
        });

        static::updated(function ($orderItem) {
            // Update order total after changes
            $order = $orderItem->order;
            if ($order) {
                $total = $order->orderItems()->sum('price');
                $order->total = $total - ($order->discount ?? 0);
                $order->mnt_rendu = max(0, $order->mnt_recu - $order->total);
                $order->save();
            }
        });

        static::deleted(function ($orderItem) {
            // Update order total after deletion
            $order = $orderItem->order;
            if ($order) {
                $total = $order->orderItems()->sum('price');
                $order->total = $total - ($order->discount ?? 0);
                $order->mnt_rendu = max(0, $order->mnt_recu - $order->total);
                $order->save();
            }
        });
    }
}
