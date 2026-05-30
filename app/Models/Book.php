<?php

namespace App\Models;

use App\Traits\Slugifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory, Slugifiable;

    protected $fillable = [
        'category',
        'title',
        'price',
        'stock',
        'author',
        'description',
        'cover_image',
        'file_path',
        'download_url',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_available' => 'boolean',
    ];

    public function isPaid(): bool
    {
        return (float) $this->price > 0;
    }

    /** Stock ilimitado (libros gratuitos o sin límite configurado). */
    public function hasUnlimitedStock(): bool
    {
        return ! $this->isPaid() || $this->stock === null;
    }

    public function hasStockAvailable(): bool
    {
        if (! $this->is_available) {
            return false;
        }

        if ($this->hasUnlimitedStock()) {
            return true;
        }

        return $this->stock > 0;
    }

    public function reservedUnits(): int
    {
        return $this->payments()
            ->whereIn('status', ['pendiente', 'en_revision'])
            ->count();
    }

    public function canAcceptNewPurchase(): bool
    {
        if (! $this->hasStockAvailable()) {
            return false;
        }

        if ($this->hasUnlimitedStock()) {
            return true;
        }

        return $this->stock > $this->reservedUnits();
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(BookDownload::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(BookOrder::class);
    }
}
