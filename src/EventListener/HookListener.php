<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener;

use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class HookListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private Utils              $utils;

    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }

    public function modifyFrontendPage(string $buffer, string $template): string
    {
        if (!$this->utils->container()->isFrontend()) {
            return $buffer;
        }

        $objPage = $this->utils->request()->getCurrentPageModel();

        if (!$objPage || !$this->container->get('huh.amp.util.layout_util')->isAmpLayout((int) $objPage->layout ?? 0)) {
            return $buffer;
        }

        // add needed amp libs
        if (!empty($this->container->get('huh.amp.manager.amp_manager')::getLibs())) {
            $scripts = [];

            foreach ($this->container->get('huh.amp.manager.amp_manager')::getLibs() as $ampName => $url) {
                $scripts[] = '<script async custom-element="amp-'.$ampName.'" src="'.$url.'"></script>';
            }

            if (!empty($scripts)) {
                return str_replace('<!-- ##ampScripts## -->', implode("\n", $scripts), $buffer);
            }
        }

        return $buffer;
    }
}
