<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\City;

$cities = City::with('province')->get();

$getZone = function ($provinceName) {
    $name = strtolower($provinceName);
    if (str_contains($name, 'jawa') || str_contains($name, 'dki') || str_contains($name, 'banten') || str_contains($name, 'yogyakarta') || str_contains($name, 'bali') || str_contains($name, 'nusa tenggara')) {
        return 1; // Zone 1: Java, Bali & Nusa Tenggara
    } elseif (str_contains($name, 'sumatera') || str_contains($name, 'aceh') || str_contains($name, 'riau') || str_contains($name, 'jambi') || str_contains($name, 'bengkulu') || str_contains($name, 'lampung') || str_contains($name, 'bangka')) {
        return 2; // Zone 2: Sumatra
    } elseif (str_contains($name, 'kalimantan')) {
        return 3; // Zone 3: Kalimantan
    } elseif (str_contains($name, 'sulawesi') || str_contains($name, 'gorontalo')) {
        return 4; // Zone 4: Sulawesi
    } elseif (str_contains($name, 'maluku') || str_contains($name, 'papua')) {
        return 5; // Zone 5: Maluku & Papua
    }
    return 3; // Default intermediate zone
};

foreach($cities as $c) {
    echo $c->id . " - " . $c->name . " (" . $c->province->name . ") - Zone " . $getZone($c->province->name) . "\n";
}
