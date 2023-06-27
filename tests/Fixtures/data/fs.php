<?php

use craft\fs\Local;
use craft\helpers\App;

return [
    'testFs' => [
        'id' => '1000',
        'name' => 'Test FS',
        'type' => Local::class,
        'url' => App::env('PRIMARY_SITE_URL'),
        'hasUrls' => true,
        'path' => dirname(__FILE__, 4) . '/testassets',
    ],
];
