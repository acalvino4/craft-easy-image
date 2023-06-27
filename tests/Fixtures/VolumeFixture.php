<?php

namespace Tests\Fixtures;

use craft\records\Volume;
use craft\test\ActiveFixture;
use Craft;
use craft\services\Volumes;


class VolumeFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $modelClass = Volume::class;

    /**
     * @inheritdoc
     */
    public $dataFile = __DIR__ . '/data/volumes.php';

    /**
     * @inheritdoc
     */
    public $depends = [FsFixture::class];
}
