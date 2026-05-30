<?php

namespace App\Models;

use App\Traits\Slugifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory, Slugifiable;

    protected $fillable = [
        'title',
        'media',
        'published_at',
        'thumbnail',
        'file_path',
        'download_url',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];
}
