<?php

namespace Tests\Fixtures;

use craft\records\Volume;
use craft\test\ActiveFixture;

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
