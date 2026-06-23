<?php
$ch = curl_init('http://127.0.0.1:8000/admin/cms/sliders/11/images/75');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    '_method' => 'PUT',
    '_token' => 'fake',
    'edit_en' => 'SHOULD NOT WORK DUE TO CSRF',
]);
$res = curl_exec($ch);
echo "HTTP " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
