<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use Illuminate\Support\Str;

Category::all()->each(function ($c) {
    if (!$c->slug) {
        $c->slug = Str::slug($c->name) . '-' . $c->id;
        $c->save();
        echo "Fixed category $c->id: $c->slug\n";
    }
});
