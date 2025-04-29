<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'complete';
    public const STATUS_CANCELLED = 'cancel';

    protected $fillable = [
        'total',
        'discount',
        'mnt_recu',
        'mnt_rendu',
        'status',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'mnt_recu' => 'decimal:2',
        'mnt_rendu' => 'decimal:2',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->discount) {
                $order->discount = 0;
            }
            if (!$order->total) {
                $order->total = 0;
            }
            
            // Calculate mnt_rendu if mnt_recu is set
            if ($order->mnt_recu) {
                $order->mnt_rendu = max(0, $order->mnt_recu - $order->total);
            }
        });

        static::created(function ($order) {
            // Update total after order items are saved
            $total = $order->orderItems()->sum('price');
            if ($total > 0) {
                $order->total = $total - ($order->discount ?? 0);
                $order->mnt_rendu = max(0, $order->mnt_recu - $order->total);
                $order->save();
            }
        });
    }
}
