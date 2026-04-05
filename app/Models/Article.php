<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Slugifiable;

class Article extends Model
{
    use HasFactory, Slugifiable;

    protected $fillable = [
        'title',
        'media',
        'published_at',
        'thumbnail',
        'download_url',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];
}
