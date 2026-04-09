<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLead extends Model
{
    protected $fillable = ['user_id', 'courses', 'total'];

    protected $casts = [
        'courses' => 'array',
        'total' => 'float',
    ];
}
