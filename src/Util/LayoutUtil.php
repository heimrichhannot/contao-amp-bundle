<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\Util;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\DataContainer;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\System;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;

class LayoutUtil implements FrameworkAwareInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;
    use FrameworkAwareTrait;

    private Utils        $utils;
    private RequestStack $requestStack;

    private bool $ampActive;

    public function __construct(Utils $utils, RequestStack $requestStack)
    {
        $this->utils = $utils;
        $this->requestStack = $requestStack;
    }

    public function modifyDca(DataContainer $dc)
    {
        $modelUtil = System::getContainer()->get('huh.utils.model');

        $layout = $modelUtil->findModelInstanceByPk('tl_layout', $dc->id);
        $dca = &$GLOBALS['TL_DCA']['tl_layout'];

        if (null !== $layout && true === (bool) $layout->addAmp) {
            $dca['palettes']['default'] = str_replace('addAmp', 'addAmp,addAmpAnalytics', $dca['palettes']['default']);
        }
    }

    /**
     * Check if amp is active for the current page.
     */
    public function isAmpActive(): bool
    {
        if (!isset($this->ampActive)) {
            $request = $this->requestStack->getCurrentRequest();

            if (!$request) {
                $this->ampActive = false;

                return $this->ampActive;
            }

            $currentPage = $this->utils->request()->getCurrentPageModel();
            $layout = $this->getAmpLayoutForCurrentPage($currentPage);

            if ($layout) {
                $this->ampActive = true;
            }
        }

        return $this->ampActive;
    }

    public function isAmpLayout(int $id)
    {
        return null !== System::getContainer()->get('huh.utils.model')->findModelInstancesBy('tl_layout', ['tl_layout.addAmp=1', 'tl_layout.id = ?'], [$id]);
    }

    /**
     * Get amp page layout based on current page.
     */
    public function getAmpLayoutForCurrentPage(PageModel $page): ?LayoutModel
    {
        // page has no amp support
        if ('inactive' === $page->enableAmp) {
            return null;
        }

        // page has amp support with custom amp layout
        if ('active' === $page->enableAmp) {
            if ($page->ampLayout > 0) {
                return $this->container->get('huh.utils.model')->findModelInstanceByPk('tl_layout', $page->ampLayout);
            }

            return null;
        }

        // get amp layout from parent amp layouts
        if ($page->pid && null !== ($layout = $this->getAmpLayoutForCurrentPage($this->container->get('huh.utils.model')->findModelInstanceByPk('tl_page', $page->pid)))) {
            return $layout;
        }

        return null;
    }
}
