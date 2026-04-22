<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultancyRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'area',
        'message',
        'status',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
