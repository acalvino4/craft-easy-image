<?php

namespace Tests\Facades;

use craft\elements\Asset as BaseAsset;
use craft\helpers\ImageTransforms;
use craft\imagetransforms\ImageTransformer;
use craft\models\ImageTransform;

class Asset extends BaseAsset
{
    /**
     * @inheritDoc
     *
     * @param mixed $transform
     */
    public function getUrl(mixed $transform = null, ?bool $immediately = null): ?string
    {
        assert($transform instanceof ImageTransform);

        $rootUrl = $this->getVolume()->getTransformFs()->getRootUrl();
        $basePath = $this->getVolume()->transformSubpath;
        $transformString = ImageTransforms::getTransformString($transform);
        $transformer = $transform->getImageTransformer();
        if ($transformer instanceof ImageTransformer) {
            $filename = $transformer->getTransformIndex($this, $transform)->filename;
        } else {
            die;
        }
        return $rootUrl . $basePath . $transformString . '/' . $filename;
    }
}
