<?php

namespace acalvino4\easyimage;

use acalvino4\easyimage\models\Settings;
use acalvino4\easyimage\services\Picture;
use acalvino4\easyimage\web\twig\Extension;
use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\TemplateEvent;
use craft\web\View;
use yii\base\Event;

/**
 * Easy Image plugin
 *
 * @method static Plugin getInstance()
 * @method Settings getSettings()
 * @property-read Picture $picture
 * @author Augustine Calvino
 * @copyright Augustine Calvino
 * @license MIT
 */
class Plugin extends BasePlugin
{
    public string $schemaVersion = '1.0.0';

    /**
     * @return mixed[]
     */
    public static function config(): array
    {
        return [
            'components' => [
                'picture' => Picture::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
        });

        Craft::$app->view->registerTwigExtension(new Extension());
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    // protected function settingsHtml(): ?string
    // {
    //     return Craft::$app->view->renderTemplate('easy-image/_settings.twig', [
    //         'plugin' => $this,
    //         'settings' => $this->getSettings(),
    //     ]);
    // }

    private function attachEventHandlers(): void
    {
        Event::on(View::class, View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
            $e->roots['easy-image'] = __DIR__ . '/templates';
        });

        Event::on(View::class, View::EVENT_BEFORE_RENDER_TEMPLATE, function(TemplateEvent $event) {
            Craft::$app->getView()->registerCss("img, video {object-fit: cover;}");
        });
    }
}
