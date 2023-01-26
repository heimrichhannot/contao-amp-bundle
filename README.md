# Contao AMP Bundle

This bundle offers functionality concerning [Accelerated Mobile Pages (AMP)](https://www.ampproject.org) for the Contao CMS.


## Features

- offer an alternative AMP version for an ordinary Contao page (GET-Parameter `amp=1` must be set)
- offer AMP templates for the supported content elements and modules
- custom inline CSS can be added via...
    - static file
    - webpack/encore integration via [heimrichhannot/contao-encore-bundle](https://github.com/heimrichhannot/contao-encore-bundle)
    - manually in fe_page_amp.html5

## Setup

### Installation

1. Install via composer: `composer require heimrichhannot/contao-amp-bundle` 
1. Update your database.
1. Prepare your page template for [Contao Head Bundle](https://github.com/heimrichhannot/contao-head-bundle) (needed for the amphtml link element).

### First steps

1. Create an ordinary layout and assign `fe_page_amp.html5` as template. Click `Add AMP support`.
1. Navigation to the root page where you want to add AMP support, set AMP support to active and choose the layout created in step 1.
1. In order to show your website in AMP mode simply append the GET parameter `amp=1` to your URL, i.e. `https://www.example.org/article` → `https://www.example.org/article?amp=1`


## Usage

### Menu/Navigation

This bundle comes with an custom frontend module for navigation. It renders the menu as sidebar and add the option to render sub pages as accordions. We recommend to use it for the navigation on your amp page.   
Since amp-sidebar must sit directly within the body element, put the navigation module into the header section of your template (we removed container elements for header section in our template).

### Support custom templates

1. To create an amp version for any template, create a new template file with the same name and add `_amp` as suffix, e.g. `ce_my_content_element_amp.html5` or `ce_my_content_element_amp.html.twig`.

1. Register the template in your project config

    ```yaml
    # /config/config.yaml
    huh_amp:
      templates:
        ce_my_content_element: ~
    ```
   
1. If your template/element needs amp components to work, update your configuration accordingly:

    ```yaml
    # /config/config.yaml
    huh_amp:
      templates:
        ce_my_content_element:
          components: ['accordion','youtube']
    ```

If you need more control about template context or components, use the [`PrepareAmpTemplateEvent`](#events). 
If the element template is amp compatible without modifications or your element will be only used in amp context, you can set `huh_amp.template.[template].amp_template` to true, see [configuration](#configuration) section.

### Encore Bundle

If you use encore bundle, just create an amp encore entry and add it to your amp layout. Only css assets from the layout will be added.

## Templates

This section will give some hints for creating amp valid templates.

### Images

AMP Bundle ship with an image template, that can be included. If you already use the utils bundle image template, you just need to replace the template name:

```twig
{# Before #}
{{ include('@HeimrichHannotContaoUtils/image.html.twig', images.singleSRC) }}
{# After #}
{{ include('@HeimrichHannotAmp/image/image_amp.html.twig', images.singleSRC) }}

```

If you don't want the contao image container around, you can also include just the image element `{{ include('@HeimrichHannotAmp/picture/picture_amp.html.twig') }}`.

### Convert html code to amp-html

> Since bundle version 0.3

If you use the `convert_html` option for a registered template, the resulting html code after parsing the templates will be converted to amp-html code. This may come handy for example ce_html or mod_html templates. To use this feature, you must install [lullabot/amp](https://github.com/Lullabot/amp-library) library, otherwise a warning in thrown when set this option to true. This bundle extends the library functionality with additional svg-support for img tags. Keep in mind that an automatic conversation maybe not complete or good as a manual conversation. 

## Developers

### Events

| Class                   | Description                                                            |
|-------------------------|------------------------------------------------------------------------|
| PrepareAmpTemplateEvent | Prepare template, add/change amp components, change the template name. |

### Supported content elements

| Contao content element  | Contao template            | AMP component  | Notes                                                                                                                   |
|-------------------------|----------------------------|----------------|-------------------------------------------------------------------------------------------------------------------------|
| `ContentAccordion`      | `ce_accordionSingle.html5` | accordion      | single element accordions                                                                                               |
| `ContentAccordionStart` | `ce_accordionStart.html5`  | accordion      |                                                                                                                         |
| `ContentAccordionStop`  | `ce_accordionStop.html5`   | accordion      |                                                                                                                         |
| `ContentImage`          | `ce_image.html5`           | image          |                                                                                                                         |
| `ContentMedia`          | `ce_player.html5`          | audio or video | aka "Audio/Video"; if `isVideo` is set in the template, the amp component "video" is used                               |
| `ContentYouTube`        | `ce_youtube.html5`         | youtube        | core content element or [heimrichhannot/contao-youtube-bundle](https://github.com/heimrichhannot/contao-youtube-bundle) |
| `ContentSlick`          | `ce_slick.html5`           | carousel       | [heimrichhannot/contao-slick-bundle](https://github.com/heimrichhannot/contao-slick-bundle)                             |

### Supported modules

| Contao module      | Contao template        | AMP component       |
|--------------------|------------------------|---------------------|
| `ModuleNavigation` | `mod_navigation.html5` | sidebar + accordion |

### AMP Validation

You can validate your AMP page by appending `#development=1` to your url.

Things to consider:

- If you do that in dev mode, you'll get validation errors concerning the position of custom CSS tag and that custom JS is not allowed. Both of the errors are due to the symfony debug toolbar and should disappear in production mode.
- When developing a website you might do that in localhost or some kind of custom domain. So you can ignore the error "The attribute 'href' in tag 'base' is set to the invalid value [...]" becuase in production mode it will disappear.
- Take care of your generated CSS: it shouldn't contain any source map files, because these will significantly increase the size of the CSS

### Meta-Tag handling in fe_page

The meta tags are handled using [heimrichhannot/contao-head-bundle](https://github.com/heimrichhannot/contao-head-bundle) and rendered as follows:

```
<?php $this->block('meta'); ?>
    <?= $this->meta; ?>
<?php $this->endblock(); ?>
```

*Hint: If you use `fe_page_amp.html5` in your AMP layout, you won't have to take care of this.*

### Override templates

#### HTML5 templates

These can be overridden as usual by putting a file with the same name into your project's `templates` directory or in the `templates` directory of one of your modules.

#### Twig templates

These can be overridden by putting a file with the same name into your project's `app/Resources/views` directory or in the `src/Resources/views` directory of one of
your bundles (these bundles must load after the `contao-amp-bundle`; you can specify this in your bundle's `Plugin.php`).

### Responsive images: Contao vs. AMP

Keep in mind: If you didn't specify image sizes in Contao, you can skip this chapter.

In Contao responsive images are built with "archives" (`tl_image_size` and `tl_image_size_item`). In tl_image_size you can define a default
image size and in `tl_image_size_item` instances more sizes depending on a given media query.

In AMP on the other hand there's no such thing as a default case, so you need to create it **explicitly**. Also you have to add media-queries for
each and every `tl_image_size_item` so that you don't have duplicates.

So you have 2 options:

1. Assign an image size with **no** child elements to your image, i.e. only `tl_image_size`.
2. Create `tl_image_size_item` instances so that you have one for every situation that can happen. Example:

![alt text](docs/image-sizes.png)

### SVG images

If you use svg-images ensure that they have assigned `width` and `height` attributes on the `<svg>` element. Otherwise they wont have dimensions in their amp-version and the lazy loading component requires width and height for aspect ratio padding.

For non-amp version simply add the following css rules and attach `.img-fluid` css class to make svg responsive again:

```
.img-fluid {
    max-width: 100%;
    height: auto;
}
```

## Documentation

[Configuration](docs/configuration.md) - The complete configuration and examples

## Known limitations

- currently AMP pages without a non-AMP layout are not supported