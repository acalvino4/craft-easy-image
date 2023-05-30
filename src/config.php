<?php

use acalvino4\easyimage\models\Settings;
use acalvino4\easyimage\models\Transform as T;
use acalvino4\easyimage\models\TransformSet as TS;

// In Craft 5, fluent config will be fully supported and `get_object_vars` will be unnecessary.
// Any property not specified in a transform is inherited first from its parent TransformSet, then from the global config, then from defaults
return get_object_vars(new Settings(
  transformSets: [
    // In the simplest case, a transform set is just used to generate different resolutions of an image for different screen widths.
    'hero' => new TS(
      transforms: [
        new T(width: 2560), // Usually you just need the width specified for a given transform.
        new T(width: 1280),
      ],
      aspectRatio: 2 / 1, // Defining aspect ratio at the level of the transform set typically makes the most sense.
    ),
    'hero-mobile' => new TS( // sometimes you need to support different image proportions for certain screen sizes ("art direction")
      transforms: [
        new T(width: 640),
        new T(width: 320),
      ],
      aspectRatio: 1 / 2,
    ),
    'complexExample' => new TS(
      transforms: [
        new T( // all normal transform settings (except 'format') can be applied here, as well as the additional 'aspectRatio'
          width: 999,
          aspectRatio: 3 / 2,
          mode: 'fit',
          quality: 95,
        ),
        new T(
          height: 542,
          width: 999,
        ),
      ],
      // all normal transform settings, as well as 'aspectRatio' and 'fallbackFormat' can be applied here
      quality: 50,
      format: 'webp', // normally this is a site-wide decision, but you can override it for a TranformSet if necessary
      fallbackFormat: 'webp' // if 'format' is identical to 'fallbackFormat', the outputted markup will be simplified to include less sources
    ),
  ],

  // Changes the image format in generated markup.
  format: 'avif',

  // Change the fallback image format in generated markup.
  // You may think you need to manually configure this to jpg, but you probably don't, as webp has 98% browser support,
  // which is better than other features you are probably (hopefully) using already, such as ES modules and brotli compression.
  fallbackFormat: 'webp',

  // Specifies how the transform is handled: see https://craftcms.com/docs/4.x/image-transforms.html#transform-modes
  mode: 'crop',

  // Specifies a default focal point. Only valid for 'crop' and 'letterbox' mode. See https://craftcms.com/docs/4.x/image-transforms.html#crop
  position: 'center-center',

  // Set the compression ratio (from 0-100) for lossy compression.
  // To set this globally, you should probably use https://craftcms.com/docs/4.x/config/general.html#defaultimagequality instead,
  // since it changes the default for all transforms, not just those set with this plugin
  quality: '90',

  // Set the height and width for all transforms.
  // A specific height and width makes aspectRatio irrelevant, so setting these will likely override any aspectRatio from having an effect, no matter how specific it is.
  height: 0,
  width: 0,

  // Set the global aspect ratio to use if height or width are ever not specified on a transform.
  // This overrides default image proportions, so you probably don't want it set at a global level.
  aspectRatio: 0,
));


// Config map syntax also works, but validation won't be as good:
//
// return [
//     'transformSets' => [
//         'hero' => [
//             //...
//         ]
//     ]
// ];
