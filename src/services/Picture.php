<?php

namespace acalvino4\easyimage\services;

use acalvino4\easyimage\models\Settings;
use Craft;
use craft\elements\Asset;
use craft\helpers\Html;
use yii\base\Component;

/**
 * Easy Image service
 *
 * @phpstan-type ImageData array{?Asset, string, 2?: int}
 */
class Picture extends Component
{
    /**
     * Picture
     *
     * @param ImageData|ImageData[] $images
     * @param mixed[]|string $attributes
     * @param boolean $eager
     * @param Settings $settings
     * @return string
     */
    public function getPictureHtml(array $images, $attributes, $eager, Settings $settings): string
    {
        // echo 'format: ' . $settings->transformSets['hero']->transforms[0]->format . ";\n";
        // die;
        // if single image details passed, rather than array, make it an array of one
        /** @var ImageData[] */
        $images = is_array($images[0]) ? $images : [$images];

        // svg files shouldn't be transformed, as they are almost always more optimized than raster formats
        // TODO: have this handle multiple images, of which any might be svg
        if ($images[0][0]?->extension === '.svg') {
            return Html::svg($images[0][0]);
        }

        // sort the array by min-width attribute (ImageData[2]) - this is essential bc browsers pick a source based on the first match
        usort($images, function($a, $b) {
            return ($b[2] ?? 0) - ($a[2] ?? 0);
        });

        // if string passed to $attributes, assume it is a class list
        $attributes = is_string($attributes) ? ['class' => $attributes] : $attributes;

        //
        foreach ($images as $image) {
            if (!$image[0]) {
                throw new \ValueError('Asset cannot be null.');
            }
        }

        $transformSetKeys = array_map(fn($image) => $image[1], $images);
        $settings->prepare($transformSetKeys);



        return Craft::$app->view->renderTemplate('easy-image/picture', [
            'images' => $images,
            'attributes' => $attributes,
            'eager' => $eager,
            'settings' => $settings,
        ]);
    }
}
