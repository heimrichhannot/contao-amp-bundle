<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\Util;

use Contao\Input;
use Contao\System;

class AmpUtil
{
    /**
     * @var array
     */
    private $ampBundleConfig;

    public function __construct(array $ampBundleConfig)
    {
        $this->ampBundleConfig = $ampBundleConfig;
    }


    /**
     * Checks whether an AMP-equivalent is available for a given ui element's template.
     *
     * @param string $template
     *
     * @return bool
     */
    public function isSupportedUiElement(string $template)
    {
        return isset($this->ampBundleConfig['templates'][$template]);
    }

    /**
     * Check if given template is already amp prepared
     *
     * @param string $template
     * @return bool
     */
    public function isAmpTemplate(string $template): bool
    {
        if (isset($this->ampBundleConfig['templates'][$template]['ampTemplate'])) {
            return true === $this->ampBundleConfig['templates'][$template]['ampTemplate'];
        }
        return false;
    }

    /**
     * Return the library url by library name
     *
     * @param string $ampName
     * @return string|null
     */
    public function getLibraryUrlByAmpName(string $ampName): ?string
    {
        if (isset($this->ampBundleConfig['libraries'][$ampName]['url'])) {
            return $this->ampBundleConfig['libraries'][$ampName]['url'];
        }
        return null;
    }

    /**
     * Return the libraries use by the template
     *
     * @param string $templateName
     * @return array
     */
    public function getLibrariesByTemplateName(string $templateName): array
    {
        $libraries = [];
        if (isset($this->ampBundleConfig['templates'][$templateName]['libraries']) &&
            !empty($this->ampBundleConfig['templates'][$templateName]['libraries']))
        {
            $libraryNames = $this->ampBundleConfig['templates'][$templateName]['libraries'];
            foreach ($libraryNames as $libraryName)
            {
                if (isset($this->ampBundleConfig['libraries'][$libraryName])) {
                    $libraries[] = $libraryName;
                }
            }
        }
        return $libraries;
    }

    /**
     * Return custom config names for the template
     *
     * @param string $templateName
     * @return array
     */
    public function getCustomConfigurationByTemplateName(string $templateName): array
    {
        if (isset($this->ampBundleConfig['templates'][$templateName]['custom']) &&
            !empty($this->ampBundleConfig['templates'][$templateName]['custom']))
        {
            return $this->ampBundleConfig['templates'][$templateName]['custom'];
        }
        return [];
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
