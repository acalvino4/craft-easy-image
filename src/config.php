<?php

use acalvino4\easyimage\models\Settings;
use craft\models\ImageTransform;

// In Craft 5, fluent config will be fully supported and `get_object_vars` will be unnecessary.
return get_object_vars(Settings::create()
  ->transformSets([
      'hero' => [
          new ImageTransform([
              'width' => 2560,
              'height' => 1280,
          ]),
          new ImageTransform([
              'width' => 1280,
              'height' => 640,
              // all supported transform parameters can be listed here, but you usually won't need them
          ]),
          // more transforms for this set can be listed here
      ],
      // ... more transform sets, for example 'document-flow', 'product-thumbnail' can be listed here
  ])
  // Comment out either line below to change defaults - not recommended, since the defaults have very good support and better performance and quality
  // ->primaryFormat('webp')
  // ->fallbackFormat('jpg')
);

// Config map syntax also works, but you don't get validation:
// return [
//     'transformSets' => [
//         'hero' => [
//             //...
//         ]
//     ]
// ];
