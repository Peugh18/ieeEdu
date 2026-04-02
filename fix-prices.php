<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$courses = \App\Models\Course::whereNotNull('discount')
    ->where('discount', '>', 0)
    ->whereNull('sale_price')
    ->get();

$output = "Cursos con descuento pero sin sale_price:\n";
foreach($courses as $c) {
    $computed = round($c->price * (1 - $c->discount / 100), 2);
    $c->sale_price = $computed;
    $c->save();
    $output .= "ID:{$c->id} | {$c->title} | price:{$c->price} | discount:{$c->discount}% | sale_price calculado: {$computed} -> GUARDADO\n";
}

if ($courses->isEmpty()) {
    $output .= "No hay cursos con ese problema.\n";
    // Show all courses anyway
    $all = \App\Models\Course::select('id','title','price','discount','sale_price')->get();
    foreach($all as $c) {
        $output .= "  ID:{$c->id} | {$c->title} | price:{$c->price} | discount:{$c->discount} | sale_price:{$c->sale_price}\n";
    }
}

file_put_contents(__DIR__.'/fix_prices.log', $output);
echo $output;
