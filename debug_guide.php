<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$root = App\Models\ServiceCategory::find(527);
echo "527: {$root->name} parent_id={$root->parent_id}\n";

$children = App\Models\ServiceCategory::where('parent_id', 527)->get(['id','name','country_id']);
foreach ($children as $c) {
    echo "child: id={$c->id} name={$c->name} country_id={$c->country_id}\n";
}

// Find the guide shown in screenshot: "Finan / Petra / 42 km 52 min"
$svc = App\Models\Service::where('description', 'like', '%Finan / Petra%')->first();
if ($svc) {
    echo "\nGuide record: id={$svc->id} category={$svc->category} vender={$svc->vender}\n";
    $cat = App\Models\ServiceCategory::find($svc->category);
    echo "its category: id={$cat->id} name={$cat->name} parent_id={$cat->parent_id}\n";

    // vendors sharing this exact category
    $sameCategory = App\Models\Service::where('category', $svc->category)->get(['id','description','vender']);
    echo "Services sharing same category ({$svc->category}):\n";
    foreach ($sameCategory as $s) {
        echo "  id={$s->id} desc={$s->description} vender={$s->vender}\n";
    }
}
