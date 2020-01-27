<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\AmpBundle\AmpLibrary;


use Lullabot\AMP\Pass\ImgTagTransformPass;

class SvgImgTagTransformPass extends ImgTagTransformPass
{
    protected function isSvg(\DOMElement $el)
    {
        if (!$el->hasAttribute('src')) {
            return true;
        }

        $src = trim($el->getAttribute('src'));
        if (preg_match('/.*\.svg$/', $src) && $el->hasAttribute("width") && $el->hasAttribute("height")) {
            return false;
        }

        return true;
    }

    protected function convertAmpImg($el, $lineno, $context_string)
    {
        /** @var \DOMElement $element */
        $element =  parent::convertAmpImg($el, $lineno, $context_string);
        $element->setAttribute('layout', 'intrinsic');
        return $element;
    }


}