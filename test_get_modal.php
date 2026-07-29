<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
\Auth::login($user);

$req = Illuminate\Http\Request::create('/admin/cms/sliders/11/images', 'GET');
$resp = $kernel->handle($req);

$html = $resp->getContent();
preg_match('/<div class="modal" id="edit_img_75">.*?<\/form>/s', $html, $matches);
if (!empty($matches)) {
    echo $matches[0];
} else {
    echo "Modal not found.";
}
