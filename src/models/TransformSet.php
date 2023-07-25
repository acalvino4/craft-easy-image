<?php

namespace acalvino4\easyimage\models;

use craft\base\imagetransforms\ImageTransformerInterface;
use craft\base\Model;
use craft\models\ImageTransform;

/**
 * Image Transform extension
 *
 * @phpstan-type Format 'jpg'|'png'|'gif'|'webp'|'avif'
 * @phpstan-type Mode 'crop'|'fit'|'stretch'|'letterbox'
 * @phpstan-type Position 'top-left'|'top-center'|'top-right'|'center-left'|'center-center'|'center-right'|'bottom-left'|'bottom-center'|'bottom-right'
 * @phpstan-type Interlace 'none'|'line'|'plane'|'partition'
 * @property ?Format $format
 */
class TransformSet extends Model
{
    /** @var ?Format */
    public ?string $format = null;

    /** @var ?Format The fallback format for when the browser doesn't support $format */
    public ?string $fallbackFormat = null;

    // An aspect ratio from which to calculate transform heights
    public ?float $aspectRatio = null;

    /** @var ?Mode */
    public ?string $mode = null;

    /** @var ?Position */
    public ?string $position = null;

    /** @var ?Interlace */
    public ?string $interlace = null;

    /** Fill color */
    public ?string $fill = null;

    /** @var ?bool Allow upscaling */
    public ?bool $upscale = null;

    /** @var ?int<0, 100> Quality */
    public ?int $quality = null;

    /** @var ?class-string<ImageTransformerInterface> */
    public ?string $transformer = null;

    /** @var int[] */
    public array $widths = [];

    /** @var ImageTransform[] */
    protected array $transforms = [];

    /** @var string[] */
    protected const TRANSFORM_PROPERTIES = [
        'format',
        'mode',
        'position',
        'interlace',
        'fill',
        'upscale',
        'quality',
        'transformer',
    ];

    /**
     * @param Format $format
     * @param Format $fallbackFormat
     * @param float $aspectRatio
     * @param Mode $mode
     * @param Position $position
     * @param Interlace $interlace
     * @param string $fill
     * @param bool $upscale
     * @param int<0, 100> $quality
     * @param class-string<ImageTransformerInterface> $transformer
     * @param int[] $widths
     */
    public function __construct(
        ?string $format = null,
        ?string $fallbackFormat = null,
        ?float $aspectRatio = null,
        ?string $mode = null,
        ?string $position = null,
        ?string $interlace = null,
        ?string $fill = null,
        ?bool $upscale = null,
        ?int $quality = null,
        ?string $transformer = null,
        array $widths = [],
    ) {
        parent::__construct([
            // 'baseTransform' => $baseTransform,
            'format' => $format,
            'fallbackFormat' => $fallbackFormat,
            'aspectRatio' => $aspectRatio,
            'mode' => $mode,
            'position' => $position,
            'interlace' => $interlace,
            'fill' => $fill,
            'upscale' => $upscale,
            'quality' => $quality,
            'transformer' => $transformer,
            'widths' => $widths,
        ]);
    }

    /**
     * @return ImageTransform[]
     */
    public function getTransforms(): array
    {
        return $this->transforms;
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
        $fallback = $this->fallbackFormat;
        if (!$fallback || $this->format === $fallback) {
            return;
        }
        foreach ($this->transforms as &$transform) {
            $transform->format = $fallback;
        }
        $this->format = $fallback;
    }
}
