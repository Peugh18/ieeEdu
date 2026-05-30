<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookDownload;
use Illuminate\Support\Facades\Cache;

class BookDownloadService
{
    public function record(Book $book, ?int $userId, string $source): void
    {
        BookDownload::create([
            'book_id' => $book->id,
            'user_id' => $userId,
            'source' => $source,
            'created_at' => now(),
        ]);

        Cache::forget('admin_dashboard_stats');
    }
}
