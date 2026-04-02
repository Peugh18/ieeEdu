<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$courses = \App\Models\Course::select('id', 'title', 'type', 'status', 'price', 'sale_price', 'discount')->get();
$output = "";
foreach($courses as $c) {
    $output .= sprintf(
        "ID:%d | %s | type:%s | status:%s | price:%s | sale_price:%s | discount:%s\n",
        $c->id, $c->title, $c->type, $c->status,
        $c->price ?? 'NULL', $c->sale_price ?? 'NULL', $c->discount ?? 'NULL'
    );
}
file_put_contents(__DIR__.'/debug_courses.log', $output);
echo "DONE\n";
