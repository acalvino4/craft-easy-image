<?php

namespace Tests\Fixtures;

use craft\records\Volume;
use craft\test\ActiveFixture;

class VolumeFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     * @var class-string
     */
    public $modelClass = Volume::class;

    /**
     * @inheritdoc
     * @var string
     */
    public $dataFile = __DIR__ . '/data/volumes.php';

    /**
     * @inheritDoc
     * @var class-string[]
     */
    public $depends = [FsFixture::class];
}
