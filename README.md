# Contao AMP Bundle

This bundle offers functionality concerning \"Accelerated Mobile Pages\" (AMP) for the Contao CMS.

## Features

-

## Installation

Install via composer: `composer require heimrichhannot/contao-amp-bundle` and update your database.

## Known limitations

### Responsive images Contao vs. AMP

In Contao responsive images are built with "archives" (`tl_image_size` and `tl_image_size_item`). In tl_image_size you can define a default
image size and in `tl_image_size_item` instances more sizes depending on a given media query.

In AMP on the other hand there's no such thing as a default case, so you need to create it **explicitly**.

<TODO images>