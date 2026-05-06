<?php
use App\Models\Transaction;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$transactions = Transaction::all();
print_r($transactions->toArray());
