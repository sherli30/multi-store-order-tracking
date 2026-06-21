<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';
$app = app();
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rates = \App\Models\ShippingRate::with('service.courier')->get();
foreach ($rates as $rate) {
    if (!$rate->service || !$rate->service->courier) continue;
    $fullName = strtolower($rate->service->courier->name . ' - ' . $rate->service->service_name);
    echo $fullName . "\n";
}
