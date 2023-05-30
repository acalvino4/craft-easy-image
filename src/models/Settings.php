<?php

namespace acalvino4\easyimage\models;

/**
 * Easy Image settings
 *
 * @phpstan-type FormatOption 'jpg'|'png'|'gif'|'webp'|'avif'
 */
class Settings extends TransformSet
{
    /** @var array<TransformSet> */
    public array $transformSets = [];

    /**
     * @inheritdoc
     *
     * @param mixed $config
     */
    public function __construct(...$config)
    {
        if (array_key_exists('transforms', $config)) {
            throw new \InvalidArgumentException("Cannot specify 'transforms' on Easy Image settings. Did you mean 'transformSet'?");
        }

        if (!array_key_exists('format', $config)) {
            $config['format'] = 'avif';
        }
        if (!array_key_exists('falbackFormat', $config)) {
            $config['fallbackFormat'] = 'webp';
        }

        parent::__construct(...$config);
    }



    /**
     * Image transforms are often used in sets via the picture tag for responsive image loading.
     * This paramater takes an array where each element's key is the name of the transform set (e.g. 'hero', 'product-thumbnail')
     * The value is a list of the Image Transforms to be generated for the set.
     * Typically you'll only need to set width and height, but all settings (https://craftcms.com/docs/4.x/image-transforms.html#defining-transforms-from-the-control-panel) are supported, other than format, which is determined by top level settings.
     *
     * @param array<TransformSet> $transformSets The array of TransformSet.
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
     * @param FormatOption $format
     * @return self
     */
    public function format(string $format): self
    {
        $this->format = $format;
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
