<?php

namespace acalvino4\easyimage\models;

use craft\models\ImageTransform;

/**
 * Image Transform extension
 *
 * Adds an aspect ratio property to image transforms which you can specify instead of a height or width.
 * If height and width are both specified, aspect ratio is ignored.
 * If neither are specified, image will be cropped to fit
 */
class Transform extends ImageTransform
{
    /** @var float an aspect ratio from which to calculate width or height if exactly one is missing */
    public ?float $aspectRatio = null;

    /**
     * @inheritDoc
     *
     * @param mixed ...$config
     * @phpstan-assert float $config['aspectRatio']
     */
    public function __construct(...$config)
    {
        if (get_class($this) === self::class && $config['format']) {
            throw new \InvalidArgumentException("Cannot specify 'format' on individual Easy Image transforms. Try specifying on a transform set or globally.");
        }

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    // @phpstan-ignore-next-line
    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['aspectRatio'], 'number'];

        return $rules;
    }
}
