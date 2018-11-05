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
     * Checks whether an AMP-equivalent is available for a given ui element's template.
     *
     * @param string $template
     *
     * @return bool
     */
    public function isSupportedUiElement(string $template)
    {
        $config = $this->container->getParameter('huh.amp');

        return isset($config['amp']['ui_elements']) && \in_array($template, array_map(function ($data) {
            return $data['template'];
        }, $config['amp']['ui_elements']));
    }

    public function getAmpNameByUiElement(string $uiElement)
    {
        $config = $this->container->getParameter('huh.amp');

        foreach ($config['amp']['ui_elements'] as $element) {
            if ($element['template'] === $uiElement) {
                return $element['ampName'];
            }
        }

        return false;
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

    public function removeTrailingAmp(string $string)
    {
        return preg_replace('@(^.*)_amp$@i', '$1', $string);
    }
}
