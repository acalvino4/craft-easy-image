<?php

namespace acalvino4\easyimage\models;

use craft\models\ImageTransform;

/**
 * Image Transform extension
 *
 * @phpstan-type FormatOption 'jpg'|'png'|'gif'|'webp'|'avif'
 */
class TransformSet extends ImageTransform
{
    /** @var float an aspect ratio from which to calculate width or height if exactly one is missing */
    public ?float $aspectRatio = null;

    /** @var FormatOption */
    public ?string $fallbackFormat = null;

    /** @var ImageTransform[] */
    public array $transforms = [];

    /** @var int[] */
    public array $widths = [];

    /** @var string[] */
    public const ALLOWED_CASCADES = [
        'mode',
        'format',
        'fallbackFormat',
        'position',
        'quality',
        'interlace',
        'transformer',
    ];


    /**
     * @inheritDoc
     *
     * @param mixed ...$config
     */
    public function __construct(...$config)
    {
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    // @phpstan-ignore-next-line
    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [
            ['format', 'fallbackFormat'],
            'in',
            'range' => [
                'jpg',
                'png',
                'gif',
                'webp',
                'avif',
            ],
        ];
        $rules[] = [['aspectRatio'], 'number'];

        return $rules;
    }

    /**
     * Updates the $format property of this object and all child transforms to $fallbackFormat
     *
     * @return void
     */
    public function fallback(): void
    {
        if ($this->format === $this->fallbackFormat) {
            return;
        }
        foreach ($this->transforms as &$transform) {
            $transform->format = $this->fallbackFormat;
        }
        $this->format = $this->fallbackFormat;
    }

    /**
     * @inheritDoc
     *
     * @param mixed[] $parameters
     */
    public function extend(array $parameters): void
    {
        foreach ($parameters as $parameter => $value) {
            if (in_array($parameter, static::ALLOWED_CASCADES, true) && !empty($value) && empty($this->$parameter)) {
                $this->$parameter = $value;
            }
        }
    }
}
