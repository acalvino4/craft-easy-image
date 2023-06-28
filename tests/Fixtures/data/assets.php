<?php

$assets = [
    [
        'title' => 'example',
        'filename' => 'example.jpg',
    ],
    [
        'title' => 'example',
        'filename' => 'example.png',

    ],
    [
        'title' => 'example',
        'filename' => 'example.webp',

    ],
    [
        'title' => 'example2',
        'filename' => 'example2.jpg',

    ],
];

return array_map(function($asset) {
    return array_merge($asset, [
        'tempFilePath' => dirname(__FILE__, 3) . "/_craft/storage/runtime/temp/" . $asset['filename'],
        'volumeId' => 1000,
        'folderId' => 1000,
    ]);
}, $assets);
