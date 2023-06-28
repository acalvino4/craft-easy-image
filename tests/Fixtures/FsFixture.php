<?php

namespace Tests\Fixtures;

use Craft;
use craft\base\FsInterface;
use yii\test\ArrayFixture;

class FsFixture extends ArrayFixture
{
    /**
     * @inheritdoc
     */
    public $dataFile = __DIR__ . '/data/fs.php';

    /**
     * A holding list for any filesystems we need to temporarily remove due to naming conflicts.
     * @var FsInterface[]
     */
    private array $oldFilesystems = [];

    /**
     * The list of filesystems that we add and need to clean up when done.
     * @var FsInterface[]
     */
    private array $newFilesystems = [];

    /**
     * @inheritdoc
     */
    public function load(): void
    {
        parent::load();

        $fsService = Craft::$app->fs;

        foreach ($this->data as $handle => $fsConfig) {
            $oldFs = $fsService->getFilesystemByHandle($handle);
            if ($oldFs) {
                $oldFilesystems[] = $oldFs;
                $fsService->removeFilesystem($oldFs);
            }
            $fsConfig['handle'] = $fsConfig['handle'] ?? $handle;
            // @phpstan-ignore-next-line
            $newFs = $fsService->createFilesystem($fsConfig);
            $newFilesystems[] = $newFs;
            $fsService->saveFilesystem($newFs);
        }
    }

    /**
     * @inheritdoc
     */
    public function unload(): void
    {
        $fsService = Craft::$app->fs;

        foreach ($this->newFilesystems as $fs) {
            $fsService->removeFilesystem($fs);
        }
        foreach ($this->oldFilesystems as $fs) {
            $fsService->saveFilesystem($fs);
        }

        parent::unload();
    }
}
