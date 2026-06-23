<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/admin/customers/ledger', 'GET');
$user = \App\Models\User::where('user_group', 'admin')->first() ?? \App\Models\User::first();
auth()->login($user);
$response = $kernel->handle($request);
echo "STATUS: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() >= 400) { echo "ERROR: " . substr(strip_tags($response->getContent()), 0, 500); }
