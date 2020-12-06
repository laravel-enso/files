<?php

return [
    'linkExpiration' => env('TEMPORARY_LINK_EXPIRATION', 60 * 60 * 24),
    'storageLimit' => 500000,
    'paginate' => (int) env('FILES_PAGINATION', 24),
    'testingFolder' => 'testing',
];
