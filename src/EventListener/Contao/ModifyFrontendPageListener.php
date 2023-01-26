<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener\Contao;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use HeimrichHannot\AmpBundle\Manager\AmpManager;
use HeimrichHannot\AmpBundle\Util\LayoutUtil;

/**
 * @Hook("modifyFrontendPage")
 */
class ModifyFrontendPageListener
{
    private LayoutUtil $layoutUtil;
    private AmpManager $ampManager;

    public function __construct(LayoutUtil $layoutUtil, AmpManager $ampManager)
    {
        $this->layoutUtil = $layoutUtil;
        $this->ampManager = $ampManager;
    }

    public function __invoke(string $buffer, string $templateName): string
    {
        if (!$this->layoutUtil->isAmpActive()) {
            return $buffer;
        }

        // add needed amp libs
        if (!empty($this->ampManager::getLibs())) {
            $scripts = [];

            foreach ($this->ampManager::getLibs() as $ampName => $url) {
                $scripts[] = '<script async custom-element="amp-'.$ampName.'" src="'.$url.'"></script>';
            }

            if (!empty($scripts)) {
                return str_replace('<!-- ##ampScripts## -->', implode("\n", $scripts), $buffer);
            }
        }

        return $buffer;
    }
}
