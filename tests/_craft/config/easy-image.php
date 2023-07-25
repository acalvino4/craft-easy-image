<?php

use acalvino4\easyimage\models\Settings;
use acalvino4\easyimage\models\TransformSet as TS;

return get_object_vars(new Settings(
  transformSets: [
    'hero' => new TS(
      widths: [2560, 1280],
      aspectRatio: 2 / 1,
    ),
    'hero-mobile' => new TS(
      widths: [640, 320],
      aspectRatio: 1 / 2,
    ),
    'no-ratio' => new TS(
      widths: [2560, 1280],
    ),
    'overrides' => new TS(
      widths: [640, 320],
      aspectRatio: 4 / 3,
      quality: 50,
      format: 'webp',
      fallbackFormat: 'jpg',
    ),
    'hero-reordered' => new TS(
      widths: [1280, 2560],
      aspectRatio: 2 / 1,
    ),
  ],
));
