<?php

namespace acalvino4\easyimage\models;

/**
 * Image Transform extension
 *
 * @phpstan-type FormatOption 'jpg'|'png'|'gif'|'webp'|'avif'
 */
class TransformSet extends Transform
{
    /** @var FormatOption */
    public ?string $fallbackFormat = null;

    /** @var Transform[] */
    public array $transforms = [];

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

        return $rules;
    }

    /**
     * @inheritDoc
     *
     * @param mixed[] $parameters
     */
    public function extend(array $parameters): void
    {
        $whiteList = [
            'width',
            'height',
            'format',
            'mode',
            'format',
            'fallbackFormat',
            'position',
            'quality',
            'interlace',
            'transformer',
            'aspectRatio',
        ];

        foreach ($parameters as $parameter => $value) {
            if (in_array($parameter, $whiteList, true) && empty($this->$parameter)) {
                $this->$parameter = $value;
            }
        }
    }
}
