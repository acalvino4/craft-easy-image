<?php

namespace Tests\Unit;

use acalvino4\easyimage\Plugin;
use acalvino4\easyimage\services\Picture;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;
use Tests\Facades\Asset;
use Tests\Fixtures\AssetFixture;
use Tests\Support\Helper\Unit as UnitHelper;
use Tests\Support\UnitTester;

/**
 * @phpstan-import-type ImageData from Picture
 * @phpstan-type ImageDataString array{string, string, 2?: int}
 */
class PictureTest extends Unit
{
    /** @var UnitTester */
    protected $tester;

    /**
     * @inheritDoc
     *
     * @return mixed[]
     */
    public function _fixtures(): array
    {
        return [
            'assets' => [
                'class' => AssetFixture::class,
                // 'dataFile' => codecept_data_dir() . 'assets.php',
            ],
        ];
    }

    /**
     * Undocumented function
     *
     * @param string $outputFile
     * @param ImageDataString|ImageDataString[] $images
     * @param mixed $attributes
     * @param boolean $eager
     * @param mixed[] $settingsOverrides
     * @param ?class-string<\Throwable> $error
     * @return void
     */
    #[DataProvider('pictureData')]
    public function testGetPictureHtml(string $outputFile, array $images, mixed $attributes, bool $eager = false, array $settingsOverrides = [], ?string $error = null): void
    {
        #Override blurhash function from plugin for testing
        UnitHelper::overrideBlurhashFunction();

        # Fetch images from data string
        if (is_array($images[0])) {
            /** @var ImageData[] $images */
            foreach ($images as &$image) {
                $image[0] = Asset::find()->filename($image[0])->one();
            }
        } else {
            $images[0] = Asset::find()->filename($images[0])->one();
        }
        /** @var ImageData|ImageData[] $images */

        if ($error) {
            $this->expectException($error);
        }

        # Apply setting overrides
        $settings = Plugin::getInstance()->getSettings();
        foreach ($settingsOverrides as $key => $value) {
            $settings->$key = $value;
        }

        $pictureService = Plugin::getInstance()->picture;
        $pictureHtml = $pictureService->getPictureHtml($images, $attributes, $eager, $settings);

        UnitHelper::assertStringEqualsHtmlFile($pictureHtml, __DIR__ . "/Html/$outputFile.html");
    }


    /**
     * @return mixed[]
     */
    public static function pictureData(): array
    {
        return [
            // Basic usage with jpg source
            ['basic', ['example.jpg', 'hero'], 'mb-10 mx-auto lg:-mb-10'],
            // Basic usage with png source
            ['basic', ['example.png', 'hero'], 'mb-10 mx-auto lg:-mb-10'],
            // Basic usage with webp source
            ['basic', ['example.webp', 'hero'], 'mb-10 mx-auto lg:-mb-10'],
            // Basic usage with eager loading selected
            ['basic_eager', ['example.jpg', 'hero'], 'mb-10 mx-auto lg:-mb-10', true],
            // Art direction
            ['art_direction', [
                ['example.jpg', 'hero', 768],
                ['example2.jpg', 'hero-mobile'],
            ], 'mb-10 mx-auto lg:-mb-10'],
            // Rending extra attributes
            ['extra_attributes', ['example.jpg', 'hero'], [
                'class' => 'mb-10 mx-auto lg:-mb-10',
                'data-custom' => 'my custom data',
            ]],
            // Overriding the default image formats globally
            ['format_override', ['example.jpg', 'hero'], 'mb-10 mx-auto lg:-mb-10', false, [
                'format' => 'webp',
                'fallbackFormat' => 'jpg',
            ]],
            // No fallback format
            ['fallback_equals_format', ['example.jpg', 'hero'], 'mb-10 mx-auto lg:-mb-10', false, [
                'format' => 'webp',
            ]],
            // Invalid setting
            ['NA', ['example.jpg', 'hero'], 'mb-10 mx-auto lg:-mb-10', false, ['width' => 500], \yii\base\UnknownPropertyException::class],
            // Non-existant Image
            ['NA', ['example354.jpg', 'hero'], 'mb-10 mx-auto lg:-mb-10', false, [], \ValueError::class],
            // No aspect ratio
            ['no_aspect_ratio', ['example.jpg', 'no-ratio'], 'mb-10 mx-auto lg:-mb-10', false, []],
            // Non-existant TransformSet key
            ['basic', ['example.jpg', 'non-existant'], 'mb-10 mx-auto lg:-mb-10', false, [], \yii\base\ErrorException::class],
            // Reordered widths (shouldn't change)
            ['basic', ['example.jpg', 'hero-reordered'], 'mb-10 mx-auto lg:-mb-10'],
            // TransformSet-level overrides
            ['transform_set_overrides', ['example.jpg', 'overrides'], 'mb-10 mx-auto lg:-mb-10'],
            // Special handling of sizes attribute
            ['sizes_attribute', ['example.jpg', 'hero'], [
                'class' => 'w-full sm:w-[640px]',
                'sizes' => '(min-width: 640px) 640px, 100vw',
            ]],
        ];
    }
}
