<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'book_id',
        'subscription_type',
        'status',
        'comprobante',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function bookOrder()
    {
        return $this->hasOne(BookOrder::class);
    }
}
