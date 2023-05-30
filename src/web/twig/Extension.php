<?php

namespace acalvino4\easyimage\web\twig;

use acalvino4\easyimage\models\Settings;
use acalvino4\easyimage\Plugin;
use Craft;
use craft\elements\Asset;
use craft\helpers\Html;
use craft\helpers\ImageTransforms;
use craft\models\ImageTransform;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
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

    public function getFilters()
    {
        return [
            new TwigFilter('prepareTransform', [$this, 'prepareTransform']),
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
        // TODO: have this handle multiple images, of which any might be svg
        if ($images[0][0]->extension === '.svg') {
            return Html::svg($images[0][0]);
        }

        // sort the array by min-width attribute (ImageData[2]) - this is essential bc browsers pick a source based on the first match
        usort($images, function($a, $b) {
            return ($b[2] ?? 0) - ($a[2] ?? 0);
        });

        // if string passed to $attributes, assume it is a class list
        $attributes = is_string($attributes) ? ['class' => $attributes] : $attributes;

        $settings = Plugin::getInstance()->getSettings();

        foreach ($images as $image) {
            if (!array_key_exists($image[1], $settings->transformSets)) {
                throw new \OutOfBoundsException("Key '$image[1]' does not exist on the transformSets array in your Easy Image config.");
            }
        }

        return Craft::$app->view->renderTemplate('easy-image/picture', [
            'images' => $images,
            'attributes' => $attributes,
            'eager' => $eager,
            'settings' => static::array_filter_recursive($settings->toArray()),
        ]);
    }

    /**
     * Converts our enhanced transform config (a Settings object) to one that Craft can actually use to transform.
     * Specifically, this function calculates height/width if missing from aspectRatio, and then strips the properties such as aspectRatio and fallbackFormat.
     *
     * @param mixed[] $transformConfig
     */
    public function prepareTransform(array $transformConfig): ?ImageTransform
    {
        if (array_key_exists('aspectRatio', $transformConfig)) {
            $width = $transformConfig['width'] ?? 0;
            $height = $transformConfig['height'] ?? 0;

            if ($width && !$height) {
                $transformConfig['height'] = (int) round($width / $transformConfig['aspectRatio']);
            }
            if ($height && !$width) {
                $transformConfig['width'] = (int) round($height * $transformConfig['aspectRatio']);
            }
        }

        $filteredConfig = array_intersect_key($transformConfig, array_flip([
            'id',
            'name',
            'transformer',
            'handle',
            'width',
            'height',
            'format',
            'parameterChangeTime',
            'mode',
            'position',
            'fill',
            'upscale',
            'quality',
            'interlace',
        ]));
        return ImageTransforms::normalizeTransform($filteredConfig);
    }

    /**
     * Removes empty values from an array
     *
     * @param mixed[] $arr
     * @return mixed[]
     */
    protected static function array_filter_recursive(array $arr): array
    {
        foreach ($arr as &$value) {
            if (is_array($value)) {
                $value = static::array_filter_recursive($value);
            }
        }

        return array_filter($arr);
    }
}
