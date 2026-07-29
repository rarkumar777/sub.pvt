<?php
use Illuminate\Support\Facades\Route;
Route::get('/dev-slider-test', function(\Illuminate\Http\Request $request) {
    Auth::login(\App\Models\User::first());
    $controller = app()->make(\App\Http\Controllers\Admin\CmsController::class);
    
    // Fake the POST request
    $req = \Illuminate\Http\Request::create('/admin/cms/sliders/11/images/75', 'PUT', [
        'edit_en' => 'TEST BROWSER POST CAPTION',
        'edit_2en' => 'TEST 2',
        'link_en' => 'https://example.com'
    ]);
    
    $response = $controller->updateSliderImage($req, 11, 75);
    return "Done. Redirected to: " . $response->getTargetUrl();
});
