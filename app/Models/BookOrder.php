<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookOrder extends Model
{
    public const STATUS_AWAITING_ADDRESS = 'awaiting_address';

    public const STATUS_PREPARING = 'preparing';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'payment_id',
        'book_id',
        'user_id',
        'shipping_status',
        'shipping_address',
        'district',
        'province',
        'department',
        'shipping_phone',
        'delivery_mode',
        'pickup_location',
        'carrier',
        'tracking_url',
        'student_note',
        'admin_notes',
        'shipped_at',
        'delivered_at',
        'updated_by',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_AWAITING_ADDRESS => 'Confirmando envío',
            self::STATUS_PREPARING => 'Preparando pedido',
            self::STATUS_SHIPPED => 'En camino',
            self::STATUS_DELIVERED => 'Entregado',
            self::STATUS_CANCELLED => 'Cancelado',
        ];
    }

    public function label(): string
    {
        return self::statusLabels()[$this->shipping_status] ?? $this->shipping_status;
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
