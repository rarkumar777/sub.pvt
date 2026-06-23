<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/admin/users/1', 'PUT', [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@test.com',
    'country' => 1,
    'city' => 'City',
    'gender' => 0,
    '_token' => csrf_token()
]);
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
if ($response->isRedirection()) {
    echo "Redirect: " . $response->headers->get('Location') . "\n";
}
