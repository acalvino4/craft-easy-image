<?php

namespace Tests\Unit;

use acalvino4\easyimage\Plugin;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;
use Tests\Facades\Asset;
use Tests\Fixtures\AssetFixture;
use Tests\Support\Helper\Unit as UnitHelper;
use Tests\Support\UnitTester;

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

    #[DataProvider('pictureData')]
    public function testGetPictureHtml(string $outputFile, array $images, mixed $attributes, bool $eager = false, array $settingsOverrides = []): void
    {
        # Fetch images
        if (is_array($images[0])) {
            foreach ($images as &$image) {
                $image[0] = Asset::find()->filename($image[0])->one();
            }
        } else {
            $images[0] = Asset::find()->filename($images[0])->one();
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

    public static function pictureData(): array
    {
        return [
            ['basic', ['example.jpg', 'hero'], 'mb-10 mx-auto lg:-mb-10'],
            ['basic', ['example.png', 'hero'], 'mb-10 mx-auto lg:-mb-10'],
            ['basic', ['example.webp', 'hero'], 'mb-10 mx-auto lg:-mb-10'],
            ['basic_eager', ['example.jpg', 'hero'], 'mb-10 mx-auto lg:-mb-10', true],
            ['art_direction', [
                ['example.jpg', 'hero', 768],
                ['example2.jpg', 'hero-mobile'],
            ], 'mb-10 mx-auto lg:-mb-10'],
            ['extra_attributes', ['example.jpg', 'hero'], [
                'class' => 'mb-10 mx-auto lg:-mb-10',
                'data-custom' => 'my custom data',
            ]],
            ['format_override', ['example.jpg', 'hero'], 'mb-10 mx-auto lg:-mb-10', false, [
                'format' => 'webp',
                'fallbackFormat' => 'jpg',
            ]],
            ['fallback_equals_format', ['example.jpg', 'hero'], 'mb-10 mx-auto lg:-mb-10', false, [
                'format' => 'webp',
            ]],
        ];
    }
}
