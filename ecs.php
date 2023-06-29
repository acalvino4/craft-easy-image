<?php

declare(strict_types=1);

use craft\ecs\SetList;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function(ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __FILE__,
    ]);

    $ecsConfig->skip([
        __DIR__ . '/tests/_craft/storage',
        __DIR__ . '/tests/Support/_generated',
    ]);

    $ecsConfig->sets([
        SetList::CRAFT_CMS_4,
    ]);
};
