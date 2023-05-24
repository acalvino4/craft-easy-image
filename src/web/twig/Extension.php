<?php

namespace acalvino4\easyimage\web\twig;

use acalvino4\easyimage\Plugin;
use Craft;
use craft\elements\Asset;
use craft\helpers\Html;
use OutOfBoundsException;
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
        // Define custom Twig functions
        // (see https://twig.symfony.com/doc/3.x/advanced.html#functions)
        return [
            new TwigFunction('picture', [$this, 'picture'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Picture
     *
     * @param ImageData|ImageData[] $images
     * @param array<mixed>|string $attributes
     * @param boolean $eager
     * @return string
     */
    public function picture(array $images, $attributes = [], $eager = false): string
    {

        // if single image details passed, rather than array, make it an array of one
        /** @var ImageData[] */
        $images = is_array($images[0]) ? $images : [$images];

        // svg files shouldn't be transformed, as they are almost always more optimized than raster formats
        if ($images[0][0]->extension === '.svg') {
            return Html::svg($images[0][0]);
        }

        // sort the array by min-width attribute (ImageData[2]) - this is essential bc browsers pick a source based on the first match
        usort($images, function($a, $b) {
            return ($a[2] ?? 0) - ($b[2] ?? 0);
        });

        // if string passed to $attributes, assume it is a class list
        $attributes = is_string($attributes) ? ['class' => $attributes] : $attributes;

        $settings = Plugin::getInstance()->getSettings();

        foreach ($images as $image) {
            if (!array_key_exists($image[1], $settings->transformSets)) {
                throw new OutOfBoundsException("Key '$image[1]' does not exist on the transformSets array in your Easy Image config.");
            }
        }

        return Craft::$app->view->renderTemplate('easy-image/picture', [
            'images' => $images,
            'attributes' => $attributes,
            'eager' => $eager,
            'settings' => $settings,
        ]);
    }
}
