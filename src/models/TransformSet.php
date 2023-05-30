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
}
