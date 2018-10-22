<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\Util;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Template;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class AmpUtil implements FrameworkAwareInterface, ContainerAwareInterface
{
    use FrameworkAwareTrait;
    use ContainerAwareTrait;

    /**
     * Checks whether an AMP-equivalent is available for a given content element's template.
     *
     * @param string $template
     *
     * @return bool
     */
    public function isSupportedContentElement(string $template)
    {
        $supportedElements = $this->container->getParameter('huh.amp');

        return isset($supportedElements['amp']['elements']) && \in_array($template, array_map(function ($data) {
            return $data['name'];
        }, $supportedElements['amp']['elements']));
    }
}
