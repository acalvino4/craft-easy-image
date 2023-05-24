# Easy Image

Maximally optimized images with minimal code.

## Intro

Most image optimization plugins require too much configuration or don't use the latest best practices. If your transform service supports avif, you shouldn't need to specify that for every transform. Usually you want 'cover' mode. The main thing that changes between transform sets is the resize dimensions, so that's all we'll make you configure (everything else is optional but still possible).

This plugin brings optimized (but configurable) transform defaults, and a twig function to output all the correct `picture` markup without you having to think about it.

We provide no-stress generation of markup that

- defaults to next-gen image format (`avif`)
- loads appropriate fallback format for older browsers (`webp` - yes, webp works even on "old" browser versions)
- loads scaled image based on viewport & resolution
- handles art direction
- handles lazy loading

all while

- requiring config only for things unique to your project
- not requiring duplication of config in multiple places
- allowing customizations to base plugin transform options (if you _really_ need `jpg`)
- allowing overrides in particular cases

## Usage

In `config/easy-image.php` include something like the following:

```php
<?php

use acalvino4\easyimage\models\Settings;
use craft\models\ImageTransform;

// In Craft 5, fluent config will be fully supported and `get_object_vars` will be unnecessary.
return get_object_vars(Settings::create()
  ->transformSets([
      'hero' => [
          new ImageTransform([
              'width' => 2560,
              'height' => 1280,
          ]),
          new ImageTransform([
              'width' => 1280,
              'height' => 640,
              // all supported transform parameters can be listed here, but you usually won't need them
          ]),
          // more transforms for this set can be listed here
      ],
      // ... more transform sets, for example 'document-flow', 'product-thumbnail' can be listed here
  ])
);
```

Then, wherever you need a hero image, just use this in your twig markup:

```twig
{{ picture([entry.myImageField.one(), 'hero'], "mx-auto mb-10 lg:mb-20") }}
```

which will output something like

```html
<picture>
  <source type="image/avif" srcset="
    [filesystem_url]/[asset_volume]/_2560x1280_crop_center-center_none/7/myImage.avif 640w,
    [filesystem_url]/[asset_volume]/_1280x640_crop_center-center_none/7/myImage.avif 480w,
  " media="(min-width: 0px)">
  <img class="mx-auto mb-10 lg:-mb-10" src="[filesystem_url]/[asset_volume]/_640xAUTO_crop_center-center_none/7/myImage.webp" srcset="
    [filesystem_url]/[asset_volume]/_2560x1280_crop_center-center_none/7/myImage.webp 640w,
    [filesystem_url]/[asset_volume]/_1280x640_crop_center-center_none/7/myImage.webp 480w,
  " alt="..." loading="lazy">
</picture>
```

A few things to note

- Image format is  [`avif` (84% support)](https://caniuse.com/?search=avif), with fallback to [`webp` (98% support)](https://caniuse.com/?search=webp).
- Lazy loading is assumed (but can be turned off via parameter explained below).
- Class list is passed through to `img` element, which applies regardless of which source is used. (Other attributes can also be passed as explained below.)

## API

### Config

Config is set through `config/easy-image.php`. Follow example above, or copy from `vendor/acalvino4/easy-image/src/config.php` to get started.

### Picture Twig Function

We'll show the function signature, give some explanation, then some examples.

#### Signature

```php
/**
 * Picture
 *
 * @phpstan-type ImageData array{Asset, string, 2?: int}
 * @param ImageData|ImageData[] $images
 * @param array<mixed>|string $attributes
 * @param boolean $eager
 * @return string
 */
public function picture(array $images, $attributes = [], $eager = false): string {}
```

#### Explanation

- `$images` - an `ImageData` value, or an array of these. Each `ImageData` value is just an ordered list of
  - an Asset,
  - the name of the transform set to use (defined in the config),
  - the min width this image should be used at (for use with art direction). Defaults to 0; irrelevant if you have only one `ImageData`.
- `$attributes` - an hash of html attributes to apply to this picture element. Supports the same attribute definitions as `\craft\helpers\Html::renderTagAttributes()`. A string of classnames can be provides instead and will be applied as such.
- `$eager` - whether this image should be loaded eagerly (normally). Defaults to false, meaning this image will lazy load.

#### Examples

The basic use case was demonstrated above:

```twig
{{ picture([entry.myImageField.one(), 'hero'], 'mx-auto mb-10 lg:mb-20') }}
```

Notice that when the `$attributes` argument is a string, it is interpreted as a class list.

The most complex case would look something like this:

```twig
{{ picture(
  [
    [entry.myImageField.one(), 'hero', 768],
    [entry.myImageFieldAlt.one(), 'hero-mobile'],
  ], {
    class: 'mx-auto mb-10 lg:mb-20',
    data-something: 'custom stuff',
  }),
  true,
}}
```

This example will load an alternate image on small screens, using the 'hero-mobile' transform set. On screens 768px and larger, it will use the same image and 'hero' transform set from before.

In both cases, the generated markup will contain the `data-something` attribute, and will _not_ lazy load.


## Comparison to existing image optimization plugins

TODO

## Requirements

This plugin requires Craft CMS 4.4.0 or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Easy Image”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require acalvino4/craft-easy-image

# tell Craft to install the plugin
./craft plugin/install easy-image
```

## TODO

Implement:

- Per-image transform overrides
- Aspect Ratio transform setting

Test:

- Regular
- Extra attributes
- Eager loading
- Art direction
- Svg
- Svgs
- Svg and Image
- Different default formats
- Use of non-existant transformSet
