<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pdo = \DB::getPdo();

// Show exactly what's in the DB for all courses
$rows = $pdo->query("SELECT id, title, type, price, sale_price, discount FROM courses ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

$out = "";
foreach($rows as $r) {
    $out .= "ID:".$r['id']." title:".$r['title']." type:".$r['type']." price:".$r['price']." sale_price:".$r['sale_price']." discount:".$r['discount']."\n";
}
file_put_contents(__DIR__.'/rawdb.log', $out);

// Now fix: any course with discount>0 that has sale_price null or 0
$fixed = 0;
foreach($rows as $r) {
    if (!empty($r['discount']) && (float)$r['discount'] > 0 && ((float)($r['sale_price'] ?? 0) <= 0)) {
        $sp = round((float)$r['price'] * (1 - (float)$r['discount'] / 100), 2);
        $pdo->exec("UPDATE courses SET sale_price = {$sp} WHERE id = {$r['id']}");
        $out .= "FIXED ID:".$r['id']." -> sale_price=".$sp."\n";
        $fixed++;
    }
}

file_put_contents(__DIR__.'/rawdb.log', $out);
echo "Done. Fixed {$fixed} courses.\nSee rawdb.log for details.\n";
