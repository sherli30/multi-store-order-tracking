<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ShippingRate;
use App\Models\ShippingService;
use App\Models\City;
use App\Models\Courier;
use Illuminate\Support\Facades\DB;

function formatMoney($val) {
    return 'Rp ' . number_format($val, 0, ',', '.');
}

// 1. Same Route, Different Couriers (Regular Service)
echo "--- 1. JNE ≠ J&T ≠ POS ≠ SICEPAT (Same Route, Regular) ---\n";
// Let's find origin and dest
$originId = 178; // Kediri (assuming store is here)
$destId = 178; // Kediri (Same City)

$rates = ShippingRate::with(['service.courier'])
    ->where('origin_city_id', $originId)
    ->where('destination_city_id', $destId)
    ->get();

foreach ($rates as $r) {
    echo sprintf("%-15s %-15s : %s / kg (ETD: %d-%d days)\n", 
        $r->service->courier->name, 
        $r->service->service_name, 
        formatMoney($r->cost_per_kg),
        $r->etd_min, $r->etd_max
    );
}
echo "\n";

// 2. Same Route & Courier, Different Services (JNE - Express, Regular, Cargo)
echo "--- 2. Express > Regular > Economy > Cargo (Same Route, JNE) ---\n";
foreach ($rates as $r) {
    if (stripos($r->service->courier->name, 'JNE') !== false) {
        echo sprintf("%-15s %-15s : %s / kg (Min: %d kg) (ETD: %d-%d days)\n", 
            $r->service->courier->name, 
            $r->service->service_name, 
            formatMoney($r->cost_per_kg),
            $r->service->min_weight,
            $r->etd_min, $r->etd_max
        );
    }
}
echo "\n";

// 3. Same Courier & Service, Different Zones
echo "--- 3. Same City < Same Prov < Same Zone < Cross Zone < Remote ---\n";
// JNE Reguler
$jneReg = ShippingService::whereHas('courier', function($q) {
    $q->where('name', 'like', '%JNE%');
})->where('service_name', 'like', '%REG%')->first();

if ($jneReg) {
    $tests = [
        ['name' => 'Same City (Kediri -> Kediri)', 'dest' => 178], // Kediri
        ['name' => 'Same Prov (Kediri -> Surabaya)', 'dest' => 444], // Surabaya
        ['name' => 'Same Zone (Kediri -> Jakarta Pusat)', 'dest' => 152], // Jakarta Pusat
        ['name' => 'Remote Zone (Kediri -> Jayapura)', 'dest' => 169], // Jayapura
    ];

    foreach ($tests as $t) {
        $r = ShippingRate::where('shipping_service_id', $jneReg->id)
            ->where('origin_city_id', $originId)
            ->where('destination_city_id', $t['dest'])
            ->first();
        if ($r) {
            echo sprintf("%-40s : %s / kg\n", $t['name'], formatMoney($r->cost_per_kg));
        }
    }
}
echo "\n";
