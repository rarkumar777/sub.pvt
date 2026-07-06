<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = App\Models\Service::whereIn('category', [527, 749, 750])->with('venderUser')->get(['id','description','category','vender']);
foreach ($rows as $r) {
    $vname = $r->venderUser ? ($r->venderUser->company ?: trim($r->venderUser->first_name.' '.$r->venderUser->last_name)) : 'NONE';
    echo "id={$r->id} cat={$r->category} vender={$r->vender} ({$vname}) desc={$r->description}\n";
}
