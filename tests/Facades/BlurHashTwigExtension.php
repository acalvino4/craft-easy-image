<?php

namespace Tests\Facades;

use craft\elements\Asset;
use dodecastudio\blurhash\twigextensions\BlurHashTwigExtension as BaseBlurHashTwigExtension;

class BlurHashTwigExtension extends BaseBlurHashTwigExtension
{
    /**
     * @inheritDoc
     *
     * @param Asset $asset
     * @return string
     */
    public function blurhash($asset): string
    {
        return 'datauri';
    }
}
