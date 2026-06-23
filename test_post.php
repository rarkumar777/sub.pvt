<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/admin/invoices/7989/send', 'POST', [
    '_token' => csrf_token()
]);
$response = $kernel->handle($request);
if ($response->isRedirection()) {
    echo "Redirect: " . $response->headers->get('Location') . "\n";
} else {
    echo "Status: " . $response->getStatusCode() . "\n";
}
