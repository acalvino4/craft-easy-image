<?php

use acalvino4\easyimage\models\Settings;
use acalvino4\easyimage\models\TransformSet as TS;

// In Craft 5, fluent config will be fully supported and `get_object_vars` will be unnecessary.
// Any property not specified in a transform set is inherited from the top level settings, then from craft defaults (if applicable)
return get_object_vars(new Settings(
  transformSets: [
    // A transform set is just used to generate different resolutions of an image for different screen widths / device densities; everything else stays the same between transforms in a set.
    // This set corresponds to one <source> tag in the markup. Each width corresponds to one source in the "srcset" attribute.
    'hero' => new TS(
      widths: [2560, 1280],
      aspectRatio: 2 / 1,
    ),
    // When you need to support different image proportions for certain screen sizes ("art direction"), define another transform set.
    'hero-mobile' => new TS(
      widths: [640, 320],
      aspectRatio: 1 / 2,
    ),
    'complexExample' => new TS(
      widths: [256, 512, 1024, 2048], // The order doesn't matter, and there is no limit on number of transforms in a set.
      // aspectRatio: 1 / 1, // With no aspect ratio set, height is set to AUTO, meaning the image keeps it's original proportions. You should set this if possible though, since some css layout solutions rely on this to avoid CLS.

      // All normal transform settings, as well as 'aspectRatio' and 'fallbackFormat' can be applied here too.
      quality: 50,
      format: 'webp', // Normally this is a site-wide decision, but you can override it for a transform set if necessary (for example, if you're publishing a blog article comparing different image formats and hence need to display some in a non-optimized format).
      fallbackFormat: 'webp', // if 'format' is identical to 'fallbackFormat', the outputted markup will be simplified to include less sources.
      mode: 'fit',
    ),
  ],

  // The rest of the settings cascade down to individual transform sets if not applied at that level

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

  // Set the global aspect ratio to use if height or width are ever not specified on a transform.
  // This overrides default image proportions, so you probably don't want it set at a global level.
  aspectRatio: 0,
));


// Config map syntax also works, but validation won't be as good:
//
// return [
//     'transformSets' => [
//         'hero' => new TS(
//             //...
//         ),
//     ]
// ];
