<?php

return [
    /*
    |--------------------------------------------------------------------------
    | License Base Prices
    |--------------------------------------------------------------------------
    |
    | Define the base prices for each license duration in Indonesian Rupiah.
    | Admins can override these through the dashboard, and overrides are
    | stored in cache.
    |
    */

    'prices' => [
        1 => env('LICENSE_PRICE_1DAY', 10000),
        3 => env('LICENSE_PRICE_3DAY', 25000),
        7 => env('LICENSE_PRICE_7DAY', 40000),
        14 => env('LICENSE_PRICE_14DAY', 70000),
        30 => env('LICENSE_PRICE_30DAY', 139000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Available Durations
    |--------------------------------------------------------------------------
    */

    'durations' => [1, 3, 7, 14, 30],
];

