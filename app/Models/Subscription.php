<?php

namespace App\Models;

use App\Observers\SubscriptionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([SubscriptionObserver::class])]
class Subscription extends Model
{
    public const STATUS_ACTIVE = 'activa';

    public const STATUS_CANCELLED = 'cancelada';

    public const STATUS_EXPIRED = 'expirada';

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
