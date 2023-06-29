<?php

namespace Tests\Fixtures;

use craft\base\ElementInterface;
use Tests\Facades\Asset;

class AssetFixture extends \craft\test\fixtures\elements\AssetFixture
{
    /**
     * @inheritDoc
     * @var string
     */
    public $dataFile = __DIR__ . '/data/assets.php';

    /**
     * @inheritdoc
     * @var class-string[]
     */
    public $depends = [FolderFixture::class];

    /**
     * @inheritdoc
     */
    protected function createElement(): ElementInterface
    {
        return new Asset();
    }
}
