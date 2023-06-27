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
     */
    public $modelClass = VolumeFolder::class;

    /**
     * @inheritdoc
     */
    public $dataFile = __DIR__ . '/data/folders.php';

    /**
     * @inheritdoc
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
