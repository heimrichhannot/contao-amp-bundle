<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Template;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class HookListener implements FrameworkAwareInterface, ContainerAwareInterface
{
    use FrameworkAwareTrait;
    use ContainerAwareTrait;

    public function parseTemplate(Template $template)
    {
        return;
        $template->setName('foo.html5');
        $supportedElements = $this->container->getParameter('huh.amp');

//        if (!isset($supportedElements['amp']['elements']) || !in_array($template, $supportedElements['amp']['elements']))
//        {
//            return $buffer;
//        }
//
//        $element = new $strClass($row, $strColumn);
//        return $element->generate();
    }
}
