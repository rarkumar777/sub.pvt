<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
\Auth::login($user);

$req = Illuminate\Http\Request::create(
    '/admin/cms/sliders/11/images/75', 'POST', 
    [
        '_method' => 'PUT',
        '_token' => csrf_token(),
        'edit_en' => 'THIS WILL WORK 1',
        'edit_2en' => 'THIS WILL WORK 2',
    ],
    [], [], [
        'HTTP_X-Requested-With' => 'XMLHttpRequest'
    ]
);

$resp = $kernel->handle($req);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Redirect: " . $response->headers->get('Location') . "\n";
echo "Contents:\n";
$saved = \DB::table('en33_slider_contents')->where('image_id', 75)->where('lang', 'en')->first();
print_r($saved);

