<?php

namespace acalvino4\easyimage\models;

use craft\config\BaseConfig;
use craft\models\ImageTransform;

/**
 * Easy Image settings
 *
 * @phpstan-type FormatOption 'jpg'|'png'|'gif'|'webp'|'avif'
 */
class Settings extends BaseConfig
{
    /** @var array<string, array<ImageTransform>> */
    public array $transformSets = [];

    /** @var FormatOption */
    public string $primaryFormat = 'avif';

    /** @var FormatOption */
    public string $fallbackFormat = 'webp';

    /**
     * Image transforms are often used in sets via the picture tag for responsive image loading.
     * This paramater takes an array where each element's key is the name of the transform set (e.g. 'hero', 'product-thumbnail')
     * The value is a list of the Image Transforms to be generated for the set.
     * Typically you'll only need to set width and height, but all settings (https://craftcms.com/docs/4.x/image-transforms.html#defining-transforms-from-the-control-panel) are supported, other than format, which is determined by top level settings.
     *
     * @param array<string, array<ImageTransform>> $transformSets The array of ImageTransform sets.
     * @return self
     */
    public function transformSets(array $transformSets): self
    {
        $this->transformSets = $transformSets;
        return $this;
    }

    /**
     * Sets the primary format to which images should be transformed. Defaults to avif.
     *
     * @param FormatOption $primaryFormat
     * @return self
     */
    public function primaryFormat(string $primaryFormat): self
    {
        $this->primaryFormat = $primaryFormat;
        return $this;
    }

    /**
     * Sets the fallback format to which images should be transformed. Defaults to avif.
     *
     * @param FormatOption $fallbackFormat
     * @return self
     */
    public function fallbackFormat(string $fallbackFormat): self
    {
        $this->fallbackFormat = $fallbackFormat;
        return $this;
    }
}
