<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
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
     * Check if given template is already amp prepared.
     *
     * @param string $template
     *
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
     * Return the components url by component name.
     *
     * @param string $ampName
     *
     * @return string|null
     */
    public function getComponentUrlByAmpName(string $ampName): ?string
    {
        if (isset($this->ampBundleConfig['components'][$ampName]['url'])) {
            return $this->ampBundleConfig['components'][$ampName]['url'];
        }

        return null;
    }

    /**
     * Return the amp components use by the template.
     *
     * @param string $templateName
     *
     * @return array
     */
    public function getComponentsByTemplateName(string $templateName): array
    {
        $components = [];

        if (isset($this->ampBundleConfig['templates'][$templateName]['components']) &&
            !empty($this->ampBundleConfig['templates'][$templateName]['components'])) {
            $componentNames = $this->ampBundleConfig['templates'][$templateName]['components'];

            foreach ($componentNames as $componentName) {
                if (isset($this->ampBundleConfig['components'][$componentName])) {
                    $components[] = $componentName;
                }
            }
        }

        return $components;
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
