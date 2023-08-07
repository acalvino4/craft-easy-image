# Usage with and comparison to other image optimization/transformation solutions

There are a number of image optimization solutions availible in the Craft ecosystem. Their functionality often overlaps, but sometimes can be used together, so it can be difficult to decide which plugins or combinations of plugins to use. This section will hopefully help you make an informed choice. If any info here (particularly regarding other plugins) is found to be incorrect, please open an issue and I will be happy to fix it.

Since Easy Image only shines at creating markup and organizing your transform sets, it is not a replacement for everything the other plugins do.

## Craft built-in transforms

Craft makes it extremely easy to define and reuse transforms, both from templates and the control panel. They even provide the [getSrcSet](https://docs.craftcms.com/api/v4/craft-elements-asset.html#method-getsrcset) function to generate the links for the `srcset` attribute. However, there are still several drawbacks to using Craft transforms on their own.

- You're on your own for handling multiple formats for browser compatibility
- You're on your own for handling art direction scenarios
- When defining transforms for a next-gen format and fallback transform within the same picture tag, most settings will be the same, so you have to repeat configuration
- You can't define transforms using an aspect ratio
- You have to manually handle height, width, and lazy loading

### Joint Use

Easy Image uses the native transforms under the hood, but if using Easy Image you shouldn't use them directly.

## ImageOptimize

nystudio107's image optimization plugin does a lot, including pregenerating transforms, generating placeholders, providing a control panel UI for defining transforms sets. Again though, there are some drawbacks and things it misses.

- Doesn't support generating avif (better compression than webp, and no 'color banding')
- You're on your own for generating complex `picture` markup mentioned before
- Configuration must be repeated
- On the subject of pregenerating transforms, this really is a tradeoff, not pure benefit, for a couple reasons:
  - Content authors need to be concious of uploading images to the appropriate volume based on what transforms need to be done on the image, rather than organizing assets based on a logical content hierarchy. This breaks the abstration of content authors not needing to worry about implementation details.
  - Some generated transforms might never be requested, wasting storage space and compute effort.
  - The main benefit is to prevent needing to wait for an asset to generate to show it. But this only applies to the first request, so the negative impact, while unfortunate, is usually proportionally small.

### Joint Use

You may still want to use ImageOptimize to optimize base files, generate placeholder images, or connect to an external service like Imgix, while using Easy Image to manage transforms and the markup. This, imo, makes the best use of what each plugin is good at. In this case, follow the ImageOptimize documentation, but

- when you create an OptimizedImages field, remove all the variants (since these are managed by Easy Image)
- don't worry about installing the webp command line tool (since that conversion will be done through ImageMagick or your external transform service)

If you select an alternative transform method in the ImageOptimize settings, the transform urls generated in the EasyImage markup will all use that alternative service.

## Picture

The picture plugin is actually extremely similar in design goals to this plugin. It's main purpose is to generate optimized picture markup based on a global config and a twig function. Again though, there are a few things on which it falls short.

- Doesn't handle markup for a fallback image format, so either you can't use next-gen formats, or some users won't see the image.
- Can't change a setting for all transform sets by specifying at the top level of config
- Doesn't output `height` and `width` attributes, so you are suseptible to layout shift

### Joint Use

You would not use these plugins together, since they have the same goal. I think you'll find though that Easy Image is more flexible and thorough in use cases it handles.

## Imager X

Imager X has a bit of overlap with this plugin, including a config file for defining transform sets, and some functions to help generate markup. It has a lot more advanced options for transforms than any other Craft plugin, so if you have special use cases it very likely is your best option. For most people though, Easy Image will be the simplest way to get fully optimized images for a few reasons.

- As with Image Optimize, Imager X leaves you on your own for writing most of the complex picture markup.
- The configuration file is slightly more verbose (`[['width' => 600],
['width' => 1800],]` instead of `widths: [600, 1800]`, and similar things)
- No globally cascading config settings for transform sets
- Doesn't support all filesystems out of the box

### Joint Use

As with ImageOptimize, Imager X has some use cases that go beyond what Easy Image can do; for example, it can be used to connect to imgix as the transform service. If you want to use Easy Image where it shines (managing transform sets and outputting markup), alongside Imager X where it shines, here are my suggestions.

- Manage all transform configurations and markup outputting from easy image, and ignore any functions or config from Imager X having to do with this.
- Use Imager X for [Color Information and Analyis](https://imager-x.spacecat.ninja/usage/colors.html#color-information-and-analysis), [Optimizers](https://imager-x.spacecat.ninja/usage/optimizers.html#configuration), and [Connecting to a 3rd party transform service](https://imager-x.spacecat.ninja/configuration.html#transformer-string)

## Image Toolbox

Image Toolbox generates picture markup, handles art direction, and implements fallback image formats, but there are again a few drawbacks.

- Generates webp as the primary image format with fallback to jpg, and this can not be reconfigured to more modern options (i.e. avif)
- Does not generate responsive sizes for use in `srcset`
- Does not output height & width attributes, opening the door for layout shifts.
- Can't define transforms in a global config for reuse, only in templates.

### Joint Use

As with Picture, you would not use these plugins together, since they have the same goal. And again, I think you'll find that Easy Image is more flexible and thorough in use cases it handles.
