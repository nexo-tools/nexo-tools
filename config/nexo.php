<?php

return [

    // Powered-by attribution shown in the shared footer. Canonical ecosystem
    // contract (same name/shape across every Nexo tool): NEXO_ATTRIBUTION_*.
    'attribution' => [
        'label' => env('NEXO_ATTRIBUTION_LABEL'),
        'url' => env('NEXO_ATTRIBUTION_URL'),
    ],

    // Help center contact target (mailto is used when no support URL is set).
    'support_url' => env('NEXO_SUPPORT_URL'),
    'support_email' => env('NEXO_SUPPORT_EMAIL', 'hola@alvarocdev.com'),

];
