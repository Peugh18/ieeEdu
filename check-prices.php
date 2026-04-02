<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$courses = \App\Models\Course::select('id','title','price','discount','sale_price')->get();
foreach($courses as $c) {
    echo $c->id.'|'.mb_substr($c->title,0,20).'|price:'.$c->price.'|disc:'.($c->discount??'null').'|sale:'.($c->sale_price??'null')."\n";
}
