<?php

use acalvino4\easyimage\models\Settings;
use acalvino4\easyimage\models\Transform as T;
use acalvino4\easyimage\models\TransformSet as TS;

return get_object_vars(new Settings(
    transformSets: [
      'hero' => new TS(
        transforms: [
          new T(width: 2560),
          new T(width: 1280),
        ],
        aspectRatio: 2 / 1,
      ),
      'hero-mobile' => new TS(
        transforms: [
          new T(width: 640),
          new T(width: 320),
        ],
        aspectRatio: 1 / 2,
      ),


      // 'heroTest' => new TS(
      //   widths: [2560, 1280],
      //   aspectRatio: 2 / 1,
      // ),
    ]
));
