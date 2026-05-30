<?php

namespace App\Models;

use App\Traits\Slugifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, Slugifiable;

    public $slug_source = 'name';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
