<?php

namespace Tests\Fixtures;

use Craft;
use craft\records\VolumeFolder;
use craft\services\Volumes;
use craft\test\ActiveFixture;

class FolderFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     * @var class-string
     */
    public $modelClass = VolumeFolder::class;

    /**
     * @inheritdoc
     * @var string
     */
    public $dataFile = __DIR__ . '/data/folders.php';

    /**
     * @inheritdoc
     * @var class-string[]
     */
    public $depends = [VolumeFixture::class];

    /**
     * @inheritdoc
     */
    public function load(): void
    {
        parent::load();

        Craft::$app->set('volumes', new Volumes());
    }
}
