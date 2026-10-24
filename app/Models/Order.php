<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[Fillable([
    'order_number', 'user_id', 'customer_name', 'phone', 'email', 'address', 'city',
    'shipping_area', 'notes', 'subtotal', 'shipping_cost', 'discount', 'total',
    'payment_method', 'payment_status', 'transaction_id', 'sp_order_id', 'payment_channel', 'paid_at', 'status',
])]
class Order extends Model
{
    public const STATUSES = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    public const PAYMENT_METHODS = [
        'cod' => 'Cash on Delivery',
        'shurjopay' => 'Online Payment (shurjoPay)',
    ];

    public const PAYMENT_STATUSES = [
        'unpaid' => 'Unpaid',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
    ];

    public const SHIPPING_AREAS = [
        'inside_dhaka' => 'Inside Dhaka',
        'outside_dhaka' => 'Outside Dhaka',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Puts the ordered quantities back in stock. */
    public function releaseStock(): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product = $item->product_id ? Product::lockForUpdate()->find($item->product_id) : null;
                if ($product) {
                    $product->increment('stock', $item->quantity);
                    $product->decrement('sold_count', min($item->quantity, $product->sold_count));
                }
            }
        });
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'TT-'.now()->format('ymd').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? strtoupper($this->payment_method);
    }

    public function getShippingAreaLabelAttribute(): string
    {
        return self::SHIPPING_AREAS[$this->shipping_area] ?? $this->shipping_area;
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    /** Whether the customer may (re)start an online payment for this order. */
    public function canPayOnline(): bool
    {
        return $this->payment_method === 'shurjopay'
            && in_array($this->payment_status, ['unpaid', 'failed'], true)
            && ! in_array($this->status, ['cancelled', 'delivered'], true);
    }

    public function toApi(): array
    {
        return [
            'order_number' => $this->order_number,
            'customer_name' => $this->customer_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'shipping_area' => $this->shipping_area,
            'shipping_area_label' => $this->shipping_area_label,
            'notes' => $this->notes,
            'subtotal' => (float) $this->subtotal,
            'shipping_cost' => (float) $this->shipping_cost,
            'discount' => (float) $this->discount,
            'total' => (float) $this->total,
            'payment_method' => $this->payment_method,
            'payment_method_label' => $this->payment_method_label,
            'payment_status' => $this->payment_status,
            'payment_status_label' => $this->payment_status_label,
            'payment_channel' => $this->payment_channel,
            'transaction_id' => $this->transaction_id,
            'can_pay' => $this->canPayOnline(),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'created_at' => $this->created_at?->toIso8601String(),
            'items' => $this->items->map(fn (OrderItem $item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'image_url' => $item->product?->image_url,
                'price' => (float) $item->price,
                'quantity' => $item->quantity,
                'total' => (float) $item->total,
            ])->values(),
        ];
    }
}
