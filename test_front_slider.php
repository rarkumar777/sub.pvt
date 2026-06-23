<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$layoutFile = storage_path('app/layouts/Homepage layout.php');
$sliderId = 0;
if (file_exists($layoutFile)) {
    if (!defined('gogies')) define('gogies', true);
    include $layoutFile;
    $sliderId = $GOGIES['slider'] ?? 0;
}
if (!$sliderId) {
    $ds = DB::table('en33_slider')->where('name', 'default')->first();
    $sliderId = $ds->id ?? 0;
}
echo "Slider ID frontend uses: " . $sliderId . "\n";
