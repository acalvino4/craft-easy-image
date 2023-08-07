<?php

namespace acalvino4\easyimage\models;

use craft\models\ImageTransform;

/**
 * Easy Image settings
 */
class Settings extends TransformSet
{
    /** @var TransformSet[] */
    public array $transformSets = [];

    /**
     * @inheritdoc
     *
     * @param TransformSet[] $transformSets
     * @param mixed $config
     */
    public function __construct($transformSets = [], ...$config)
    {
        $this->transformSets = $transformSets;

        parent::__construct(...$config);

        // Using ImageOptimize's placeholder image; deprecated in favor of blurhash
        // if (\Composer\InstalledVersions::isInstalled('nystudio107/craft-imageoptimize')) {
        //     $volumeService = \Craft::$app->volumes;
        //     $fieldMap = [];
        //     foreach ($volumeService->getAllVolumes() as $volume) {
        //         $fieldLayout = $volume->getFieldLayout();
        //         $fields = $fieldLayout?->getCustomFields();
        //         if ($fields) {
        //             $optImgField = array_filter($fields, fn($a) => ($a instanceof OptimizedImages))[0] ?? null;
        //             $fieldMap[$volume->handle] = $optImgField?->handle ?? '';
        //         }
        //     }
        //     $this->imageOptimizeFields = $fieldMap;
        // }
    }

    /**
     * Creates transforms and cascades settings for use in twig template.
     *
     * @param string[] $transformSetKeys
     */
    public function prepare(array $transformSetKeys): void
    {
        if (!$this->format) {
            $this->format = 'avif';
        }
        if (!$this->fallbackFormat) {
            $this->fallbackFormat = 'webp';
        }

        // Get cascadable settings
        $settingsArr = array_filter($this->toArray(array_merge(['aspectRatio', 'fallbackFormat'], static::TRANSFORM_PROPERTIES)), fn($a) => isset($a));

        foreach ($transformSetKeys as $key) {
            $transformSet = $this->transformSets[$key];

            if (!$transformSet->prepared) {

                // Cascade settings
                foreach ($settingsArr as $parameter => $value) {
                    if (!isset($transformSet->$parameter)) {
                        $transformSet->$parameter = $value;
                    }
                }

                // Create transforms
                arsort($transformSet->widths); // makes output predictable
                foreach ($transformSet->widths as $width) {
                    if ($transformSet->aspectRatio) {
                        $height = (int) round($width / $transformSet->aspectRatio);
                    }
                    $transformSet->transforms[] = new ImageTransform(array_merge(
                        array_filter($transformSet->toArray(static::TRANSFORM_PROPERTIES), fn($a) => isset($a)),
                        [
                            'width' => $width,
                            'height' => $height ?? 0,
                        ]
                    ));
                }

                $transformSet->prepared = true;
            }
        }
    }

    /**
     * @param TransformSet[] $transformSets The array of TransformSet.
     * @return self
     */
    public function transformSets(array $transformSets): self
    {
        $this->transformSets = $transformSets;
        return $this;
    }
}
