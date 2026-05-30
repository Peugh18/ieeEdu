<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookDownload extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'book_id',
        'user_id',
        'source',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
