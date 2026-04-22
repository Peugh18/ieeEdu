<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'section',
        'order',
        'image_path',
        'heading',
        'subheading',
        'button_text',
        'button_link',
        'position',
        'is_active',
        'show_text',
        'whatsapp_number',
        'contact_email',
        'contact_address',
    ];

    protected $casts = [
        'show_text' => 'boolean',
        'is_active' => 'boolean',
    ];
}
