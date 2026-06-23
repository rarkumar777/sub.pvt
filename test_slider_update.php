<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create(
    '/admin/cms/sliders/11/images/75',
    'POST',
    [
        '_method' => 'PUT',
        'edit_en' => 'TESTING ENGLISH CAPTION',
        'edit_2en' => 'TESTING ENGLISH CAPTION 2',
        'link_en' => 'https://example.com'
    ]
);

$user = \App\Models\User::first();
\Auth::login($user);

$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Redirect: " . $response->headers->get('Location') . "\n";

$saved = \DB::table('en33_slider_contents')->where('image_id', 75)->where('lang', 'en')->first();
print_r($saved);

