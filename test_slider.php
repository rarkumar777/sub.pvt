<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create(
    '/admin/cms/sliders/11/images/75',
    'PUT',
    [
        '_token' => csrf_token(),
        'edit_en' => 'TESTING ENGLISH CAPTION',
        'edit_2en' => 'TESTING ENGLISH CAPTION 2',
        'link_en' => 'https://example.com'
    ]
);

$controller = app()->make(\App\Http\Controllers\Admin\CmsController::class);
$response = $controller->updateSliderImage($request, 11, 75);

$saved = \DB::table('en33_slider_contents')->where('image_id', 75)->where('lang', 'en')->first();
print_r($saved);

