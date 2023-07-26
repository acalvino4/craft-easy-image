# Easy Image

Maximally optimized images with minimal code.

![License](https://img.shields.io/github/license/acalvino4/craft-easy-image)
![Build Status](https://img.shields.io/github/actions/workflow/status/acalvino4/craft-easy-image/qa.yml)

![Test Coverage](https://img.shields.io/badge/Codeception-passing-brightgreen.svg)
![Phpstan Level](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)
![Easy Coding Standard](https://img.shields.io/badge/Easy%20Coding%20Standard-%20enabled-brightgreen.svg)

## Intro

The markup necessary for maximally optimized images is _complex_. But what if you could have it all, just by passing your asset, a transform set name, and whatever html attributes you want applied?

Most image optimization plugins require too much configuration or don't use the latest best practices. If your transform service supports avif, you shouldn't need to specify that for every transform. Usually you want 'cover' mode. The main thing that changes between transform sets is the resize dimensions, so that's all we'll make you configure (everything else is optional but still possible).

This plugin brings optimized (but configurable) transform defaults, and a twig function to output all the correct `picture` markup without you having to think about it.

We provide no-stress generation of markup that

- defaults to next-gen image format (`avif`)
- loads appropriate fallback format for older browsers (`webp` - yes, webp works even on "old" browser versions)
- sets height and width to avoid CLS
- loads scaled image based on viewport & resolution
- handles art direction
- handles lazy loading

all with

- modern, sensible defaults
- flexible, organized, and cascading configuration

## Usage

In `config/easy-image.php` include something like the following (see annotated `config.php` in plugin's `src` directory):

```php
<?php

use acalvino4\easyimage\models\Settings;
use craft\models\TransformSet as TS;

return get_object_vars(new Settings(
  transformSets: [
    'hero' => new TS(
      widths: [2560, 1280],
      aspectRatio: 2 / 1,
    ),
    'hero-mobile' => new TS(
      widths: [640, 320],
      aspectRatio: 1 / 2,
    ),
    // ...
  ],
));
```

Then, wherever you need a hero image, just use this in your twig markup:

```twig
{{ picture([entry.myImageField.one(), 'hero'], "mx-auto mb-10 lg:mb-20") }}
```

which will output something like

```html
<picture>
  <source
    type="image/avif"
    srcset="
      https://easyimage.local/transforms/_2560x1280_crop_center-center_none/example.avif 2560w,
      https://easyimage.local/transforms/_1280x640_crop_center-center_none/example.avif 1280w,
    "
    height="144"
    width="288"
  />
  <img
    class="mb-10 mx-auto lg:-mb-10"
    src="https://easyimage.local/transforms/_2560x1280_crop_center-center_none/example.webp"
    srcset="
      https://easyimage.local/transforms/_2560x1280_crop_center-center_none/example.webp 2560w,
      https://easyimage.local/transforms/_1280x640_crop_center-center_none/example.webp 1280w,
    "
    height="144"
    width="288"
    alt="example"
    loading="lazy"
  />
</picture>
```

A few things to note

- Image format is [`avif` (84% support)](https://caniuse.com/?search=avif), with fallback to [`webp` (98% support)](https://caniuse.com/?search=webp).
- Lazy loading is assumed (but can be turned off via parameter explained below).
- Class list is passed through to `img` element, which applies regardless of which source is used. (Other attributes can also be passed as explained below.)
- The outputted height and width don't correspond to the instrinsic size, because when we are using a `srcset`, there is not just _one_ intrinsic size. However, the numbers _do_ correspond to the correct aspect ratio, which is the important thing for avoiding layout shifts (CLS).

## API

### Config

Config is set through `config/easy-image.php`. Follow example above, or check out `vendor/acalvino4/easy-image/src/config.php` for a more thoroughly annotated example.

### Picture Twig Function

We'll show the function signature, give some explanation, then some examples.

#### Signature

```php
/**
 * Picture
 *
 * @phpstan-type ImageData array{Asset, string, 2?: int}
 * @param ImageData|ImageData[] $images
 * @param mixed[]|string $attributes
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

## Comparison to existing image optimization/transformation solutions

### Craft built-in transforms

Craft makes it extremely easy to define and reuse transforms, both from templates and the control panel. They even provide the [getSrcSet](https://docs.craftcms.com/api/v4/craft-elements-asset.html#method-getsrcset) function to generate the links for the `srcset` attribute. However, there are still several drawbacks.

- You're on your own for handling multiple formats for browser compatibility
- You're on your own for handling art direction scenarios
- When defining transforms for a next-gen format and fallback transform within the same picture tag, most settings will be the same, so you have to repeat configuration
- You can't define transforms using an aspect ratio
- You have to manually handle height, width, and lazy loading

### ImageOptimize

nystudio107's image optimization plugin does a lot, including pregenerating transforms, generating placeholders, providing a control panel UI for defining transformsSets. Again though, there are some drawbacks and things it misses.

- Doesn't support generating avif (better compression than webp, and no 'color banding')
- You're on your own for generating complex `picture` markup mentioned before
- Configuration must be repeated
- On the subject of pregenerating transforms, this really is a tradeoff, not pure benefit, for a couple reasons:
  - Content authors need to be concious of uploading images to the appropriate volume based on what transforms need to be done on the image, rather than organizing assets based on a logical content hierarchy. This breaks the abstration of content authors not needing to worry about implementation details.
  - Some generated transforms might never be requested, wasting storage space and compute effort.
  - The main benefit is to prevent needing to wait for an asset to generate to show it. But this only applies to the first request, so the negative impact, while unfortunate, is usually proportionally small.

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

- Per-image transform overrides
- Filepath for assets
- Readme refresh
- Comparison to other plugins
- handle sizes attribute
