<?php

namespace acalvino4\easyimage\models;

/**
 * Easy Image settings
 *
 * @phpstan-type FormatOption 'jpg'|'png'|'gif'|'webp'|'avif'
 */
class Settings extends TransformSet
{
    /** @var TransformSet[] */
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
     * Normalizes settings to a format where they can easily be consumed by twig template.
     * This involves cascading settings down to transforms and transform sets,
     * and calculating height and width from aspect ratio if either was left blank.
     */
    public function normalize(): void
    {
        $settingsArr = array_filter($this->toArray());
        foreach ($this->transformSets as &$transformSet) {
            $transformSet->extend($settingsArr);
            $transformSetArr = array_filter($transformSet->toArray());

            $newTransforms = [];
            foreach ($transformSet->transforms as $transform) {
                $transform->extend($transformSetArr);

                if ($transform->aspectRatio) {
                    if (!$transform->height) {
                        $transform->height = (int) round($transform->width / $transform->aspectRatio);
                    }
                    if (!$transform->width) {
                        $transform->width = (int) round($transform->height * $transform->aspectRatio);
                    }
                }
                $newTransforms[] = $transform;
            }
            $transformSet->transforms = $newTransforms;

            // Set the set's aspect ratio if not specified based on first transform in set
            if (!$transformSet->aspectRatio && $transformSet->transforms)
                $transformSet->aspectRatio = $transformSet->transforms[0]->height / $transformSet->transforms[0]->width;
        }
    }



    /**
     * Image transforms are often used in sets via the picture tag for responsive image loading.
     * This paramater takes an array where each element's key is the name of the transform set (e.g. 'hero', 'product-thumbnail')
     * The value is a list of the Image Transforms to be generated for the set.
     * Typically you'll only need to set width and height, but all settings (https://craftcms.com/docs/4.x/image-transforms.html#defining-transforms-from-the-control-panel) are supported, other than format, which is determined by top level settings.
     *
     * @param TransformSet[] $transformSets The array of TransformSet.
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
     * Sets the fallback format to which images should be transformed. Defaults to webp.
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
