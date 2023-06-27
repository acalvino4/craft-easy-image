<?php

namespace Tests\Fixtures;

use Craft;
use Tests\Facades\Asset;
use craft\base\ElementInterface;

class AssetFixture extends \craft\test\fixtures\elements\AssetFixture
{
    // /**
    //  * Internal variable used to track id of current element
    //  */
    // private ?int $currentId;

    /**
     * @inheritDoc
     */
    public $dataFile = __DIR__ . '/data/assets.php';

    /**
     * @inheritdoc
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
