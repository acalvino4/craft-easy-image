<?php

namespace acalvino4\easyimage\web\twig;

use acalvino4\easyimage\Plugin;
use craft\elements\Asset;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension
 *
 * @phpstan-type ImageData array{Asset, string, 2?: int}
 */
class Extension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('picture', [$this, 'picture'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Picture
     *
     * @param ImageData|ImageData[] $images
     * @param mixed[]|string $attributes
     * @param boolean $eager
     * @return string
     */
    public function picture(array $images, $attributes = [], $eager = false): string
    {
        $plugin = Plugin::getInstance();
        return $plugin->picture->getPictureHTML($images, $attributes, $eager, $plugin->getSettings());
    }
}
