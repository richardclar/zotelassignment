<?php

return [
    'last_minute' => [
        'enabled' => true,
        'min_days_before_checkin' => 0,
        'max_days_before_checkin' => 3,
        'discount_type' => 'percentage',
        'discount_value' => 10,
        'stackable' => true,
    ],

    'long_stay' => [
        'enabled' => true,
        'min_nights' => 3,
        'discount_type' => 'percentage',
        'discount_value' => 15,
        'stackable' => true,
    ],

    'early_bird' => [
        'enabled' => false,
        'min_days_before_checkin' => 14,
        'discount_type' => 'percentage',
        'discount_value' => 20,
        'stackable' => true,
    ],
];
