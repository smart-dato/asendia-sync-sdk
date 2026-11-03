<?php

return [
    // the same login as the Asendia Sync web portal
    'username' => env('ASENDIA_SYNC_USERNAME'),
    'password' => env('ASENDIA_SYNC_PASSWORD'),
    // used by the tracking and listing endpoints, GET /api/customers lists the ones the login may use
    'customer_id' => env('ASENDIA_SYNC_CUSTOMER_ID'),
    'base_url' => env('ASENDIA_SYNC_BASE_URL', 'https://www.asendia-sync.com'),
    // the store that keeps the 24h JWT between requests, null means the default store
    'cache_store' => env('ASENDIA_SYNC_CACHE_STORE'),
];
