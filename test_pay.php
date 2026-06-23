<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/en/invoice/7984/pay', 'POST', [
    '_token' => csrf_token()
]);
$response = $kernel->handle($request);
if ($response->isRedirection()) {
    echo "Redirect Location: " . $response->headers->get('Location') . "\n";
} else {
    echo "Status: " . $response->getStatusCode() . "\n";
    echo $response->getContent();
}
