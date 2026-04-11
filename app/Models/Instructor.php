<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'bio',
        'image',
        'social_links',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
