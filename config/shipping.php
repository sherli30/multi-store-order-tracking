<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shipping Rates Configuration (Versi Ekonomis)
    |--------------------------------------------------------------------------
    |
    | Tarif pengiriman per kilogram (Rp) yang sudah dimurahkan
    |
    */

    'rates' => [
        'reguler' => 8000, // Turun dari 10.000
        'cargo'   => 3000, // Turun dari 5.000
    ],

    /*
    | Ambang batas berat untuk beralih dari Reguler ke Cargo (kg)
    */
    'cargo_threshold' => 10,
];
