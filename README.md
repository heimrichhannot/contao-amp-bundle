# Contao AMP Bundle

This bundle offers functionality concerning \"Accelerated Mobile Pages\" (AMP) for the Contao CMS.

## Features

- offer an alternative AMP version for an ordinary Contao page (GET-Parameter `amp=1` must be set)
- offer AMP templates for the supported content elements
- encore integration

## Installation

Install via composer: `composer require heimrichhannot/contao-amp-bundle` and update your database.

## Configuration

1. Create an ordinary layout and assign `fe_page_amp.html5` as template. Don't click `Add AMP support` here, we'll do that in the next step ;-)
2. Navigate to your existing layout or create a new one for the page that should support both AMP and non-AMP. Click `Add AMP support` and choose the layout we just created in step 1.
3. Assign the layout created in step 2 to your page.
4. In order to show your website in AMP mode simply append the GET parameter `amp=1` to your URL, i.e. `https://www.example.org/article` -> `https://www.example.org/article?amp=1`

## Events

Name | Arguments | Description
---- | --------- | -----------
TODO | $objTemplate, $arrItem, $objModule | Triggered just before FrontendTemplate::parse() is called

## Supported content elements

Contao content element | Contao template | AMP component | AMP template | Notes
---------------------- | --------------- | ------------- | ------------ | -----
`ContentAccordion` | `ce_accordionSingle.html5` | accordion | `ce_accordionSingle_amp.html.twig` | single element accordions
`ContentAccordionStart` | `ce_accordionStart.html5` | accordion | `ce_accordionStart_amp.html.twig` |
`ContentAccordionStop` | `ce_accordionStop.html5` | accordion | `ce_accordionStop_amp.html.twig` |
`ContentImage` | `ce_image.html5` | image | `ce_image_amp.html.twig` |
`ContentMedia` | `ce_player.html5` | audio or video | `ce_player_amp.html.twig` | aka "Audio/Video"; if `isVideo` is set in the template, the amp component "video" is used
`ContentYouTube` | `ce_youtube.html5` | youtube | `ce_youtube_amp.html.twig` or `ce_youtube_amp_huh.html.twig` | core content element or [heimrichhannot/contao-youtube-bundle](https://github.com/heimrichhannot/contao-youtube-bundle)
`ContentSlick` | `ce_slick.html5` | carousel | `ce_slick_amp.html.twig` | [heimrichhannot/contao-slick-bundle](https://github.com/heimrichhannot/contao-slick-bundle)

## Things to know

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

## Known limitations

- currently AMP pages without a non-AMP layout are not supported