# Easy Image

Maximally optimized images with minimal code.

![License](https://img.shields.io/github/license/acalvino4/craft-easy-image)
![Build Status](https://img.shields.io/github/actions/workflow/status/acalvino4/craft-easy-image/qa.yml)

![Test Coverage](https://img.shields.io/badge/Codeception-passing-brightgreen.svg)
![Phpstan Level](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)
![Easy Coding Standard](https://img.shields.io/badge/Easy%20Coding%20Standard-%20enabled-brightgreen.svg)

## Intro

The markup necessary for maximally optimized images is _complex_. You have to know a lot to get it right, and even when you do, propogating and enforcing this knowledge across a team, not to mention implementing it without stray mistakes, is a challenge. Just some of the things you need to know are

- How do I use height and width to avoid layout shift while still allowing resizing via css?
- Does the order of sources in srcset matter?
- Does the order of source tags in the picture element matter?
- How do I configure sources for art direction?
- Should I be using width descriptors or 'x' descriptors?
- How do I specify fallback image formats?
- How do I let the browser know how wide my image will display, before it or the css is loaded?
- What is the mime type for jpg's?
- Does the order of media queries in the sizes attribute matter?
- Which attributes go on the img tag, which go on source tags, and which go on both?
- How do I generate and include blurry placeholders before images load?
- How do I swap out the blurry placeholders for the real image (without js)?

But what if you didn't have to think about all that, and instead got correct markup just by specifying your asset, a transform set name, and whatever html attributes you want applied?

Most image optimization plugins require too much configuration or don't use the latest best practices. If your transform service supports avif, you shouldn't need to specify that for every transform. Usually you want 'cover' mode. The main thing that changes between transform sets is the resize dimensions, so that's all we'll make you configure (everything else is optional but still possible).

This plugin brings optimized (but configurable) transform defaults, and a twig function to output all the correct `picture` markup without you having to think about it.

We provide no-stress generation of markup that

- defaults to next-gen image format (`avif`)
- loads appropriate fallback format for older browsers (`webp` - yes, webp works even on "old" browser versions)
- sets height and width to avoid CLS
- loads scaled image based on viewport & resolution
- handles art direction
- handles lazy loading
- generates, loads, and swaps a blurry placeholder image

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
    'thumbnail' => ...
    'catalog-item' => ...
  ],
));
```

Then, wherever you need a hero image, just use this in your twig markup:

```twig
{{ picture([entry.myImageField.one(), 'hero'], "mx-auto my-10 lg:my-20") }}
```

which will output something like

```html
<picture>
  <source
    type="image/avif"
    srcset="
      https://easyimage.local/transforms/_2560x1280_crop_center-center_none/example.avif 2560w,
      https://easyimage.local/transforms/_1280x640_crop_center-center_none/example.avif  1280w
    "
    height="144"
    width="288"
  />
  <img
    class="mb-10 mx-auto lg:-mb-10"
    src="https://easyimage.local/transforms/_2560x1280_crop_center-center_none/example.webp"
    srcset="
      https://easyimage.local/transforms/_2560x1280_crop_center-center_none/example.webp 2560w,
      https://easyimage.local/transforms/_1280x640_crop_center-center_none/example.webp  1280w
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
{{ picture([entry.myImageField.one(), 'hero'], 'mx-auto my-10 lg:my-20') }}
```

Notice that when the `$attributes` argument is a string, it is interpreted as a class list.

The most complex case would look something like this:

```twig
{{ picture(
  [
    [entry.myImageField.one(), 'hero', 768],
    [entry.myImageFieldAlt.one(), 'hero-mobile'],
  ], {
    class: 'mx-auto my-10 lg:my-20',
    data-something: 'custom stuff',
  }),
  true,
}}
```

This example will load an alternate image on small screens, using the 'hero-mobile' transform set. On screens 768px and larger, it will use the same image and 'hero' transform set from before.

In both cases, the generated markup will contain the `data-something` attribute, and will _not_ lazy load.

### Sizes

If you use this plugin to generate your image markup as described above, you'll be doing great! But to really optimize, you should pass in the `sizes` attribute to the attribute list. By default, the browser will pick from your srcset based on which width descriptor matches your browser viewport width. This is necessary because while css styling may constrain the width of an image to, say, 50% of the page for a two column layout, or a flat 50px, the browser may need to request these images before the css is loaded. If your image is full-width, then you're all good, but many times an image only takes up a small fraction of the viewport - for example, a thumbnail image.

The sizes attribute describes how wide your image will display directly in the html so the browser can request the appropriate image directly. It should often mirror your css styling, as in below examples, but keep in mind that parent containers may also constrain an image's width. Keep in mind that the browser will use the first media query matched, so list the more general cases last.

#### Sizes Examples

```twig
  {{ picture([entry.testAsset.one(), 'hero'], {
    class: "w-full sm:px-16 2xl:w-[640px]",
    sizes: "(min-width: 1536px) 640px, (min-width: 640px) calc(100vw - 128px), 100vw",
  } ) }}

  {{ picture([entry.testAsset.one(), 'hero'], {
    class: "w-full sm:w-[640px]",
    sizes: "(min-width: 640px) 640px, 100vw",
  } ) }}
```

### Placeholder Images

This plugin will inline a blurry version of your image until your image loads; there is nothing you need to do. It generates this image via the [Blur Hash](https://plugins.craftcms.com/blur-hash?craft4) plugin, which is automatically included. By default, that plugin will generate placeholders with a max size of 64px in the larger direction, which comes out to a few kb in size. Because this is rather larger to inline, I recommend overriding this by including the following in `config/blur-hash.php`:

```php
<?php

return [
    'blurredMaxImageSize' => 8,
];
```

This will generally reduce the size to less than 500 bytes (around 250 in my tests), while retaining a decent amount of detail and avoiding pixelation. You can play around with this to find the right balance for you between data uri size and detail.

The other thing to note is that this dependency is licensed as "treeware", meaning you are asked to [donate to plant trees](https://ecologi.com/treeware?gift-trees&ref=a77c966621c4104ab0ab03311413fa6e) when you use it in production. This can be a one-time donation, and there is no minumim.

## Requirements

This plugin requires Craft CMS 4.4.0 or later, and PHP 8.0.2 or later.

> **Warning**
> If doing local transforms, ImageMagick 7 is required to avoid certain bugs (for example, converting a png with transparent background to avif results in a solid black background instead).

Both ddev and the default ubuntu apt package will install ImageMagick 6 by default. I am not aware of a way to upgrade the version in ddev, but for ubuntu, just run the following script as root on your server.

```bash
t=$(mktemp) && wget 'https://dist.1-2.dev/imei.sh' -qO "$t" && bash "$t" && rm "$t"
apt-get -qq install php-pear php-dev >/dev/null
pecl install imagick -q >/dev/null </dev/null
# insert your version of php below
systemctl restart php<version>-fpm.service -q
```

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

## Roadmap

- Per-image transform overrides
- Filepath or url for assets
- Comparison to other plugins
- Transform/optimize images on upload
