<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\Util;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Input;
use Contao\System;
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
        $config = $this->container->getParameter('huh.amp');

        return isset($config['amp']['elements']) && \in_array($template, array_map(function ($data) {
            return $data['name'];
        }, $config['amp']['elements']));
    }

    public function getAmpNameByContentElement(string $contentElement)
    {
        $config = $this->container->getParameter('huh.amp');

        foreach ($config['amp']['elements'] as $lib) {
            if ($lib['name'] === $contentElement) {
                return $lib['ampTemplate'];
            }
        }

        return false;
    }

    /**
     * Renders the twig template.
     *
     * @param Template $template
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return string
     */
    public function renderTwigTemplate(Template $template, string $templateName = '')
    {
        $buffer = $this->container->get('twig')->render(
            $this->container->get('huh.utils.template')->getTemplate($templateName ?: $template->getName()),
            $template->getData()
        );

        return $buffer;
    }

    public function getLibraryByAmpName(string $ampName)
    {
        $config = $this->container->getParameter('huh.amp');

        foreach ($config['amp']['libraries'] as $lib) {
            if ($lib['ampName'] === $ampName) {
                return $lib['url'];
            }
        }

        return false;
    }

    public function skipAnalyticsForBackend()
    {
        if (BE_USER_LOGGED_IN) {
            return true;
        }

        if (!isset($_COOKIE['BE_USER_AUTH'])) {
            return false;
        }

        return Input::cookie('BE_USER_AUTH') == System::getSessionHash('BE_USER_AUTH');
    }
}
