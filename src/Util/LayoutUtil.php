<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LayoutUtil implements FrameworkAwareInterface, ContainerAwareInterface
{
    use FrameworkAwareTrait;
    use ContainerAwareTrait;

    public function modifyDca(DataContainer $dc)
    {
        $modelUtil = System::getContainer()->get('huh.utils.model');

        $layout = $modelUtil->findModelInstanceByPk('tl_layout', $dc->id);
        $dca    = &$GLOBALS['TL_DCA']['tl_layout'];

        if (null !== $layout && true === (bool)$layout->addAmp) {
            $dca['palettes']['default'] = str_replace('addAmp', 'addAmp,addAmpAnalytics', $dca['palettes']['default']);
        }
    }

    public function isAmpLayout(int $id)
    {
        return null !== System::getContainer()->get('huh.utils.model')->findModelInstancesBy('tl_layout', ['tl_layout.addAmp=1', 'tl_layout.id = ?'], [$id]);
    }

    /**
     * Get amp page layout based on current page
     *
     * @param PageModel $page
     *
     * @return LayoutModel|null
     */
    public function getAmpLayoutForCurrentPage(PageModel $page): ?LayoutModel
    {
        // page has no amp support
        if ('inactive' === $page->amp) {
            return null;
        }


        // page has amp support with custom amp layout
        if ('active' === $page->amp) {

            if ($page->ampLayout > 0) {
                return $this->container->get('huh.utils.model')->findModelInstanceByPk('tl_layout', $page->ampLayout);
            }

            return null;
        }

        // get amp layout from parent amp layouts
        if (null !== ($parent = $this->container->get('huh.utils.model')->findModelInstanceByPk('tl_page', $page->pid)) && null !== ($layout = $this->getAmpLayoutForCurrentPage($parent))) {
            return $layout;
        }

        return null;
    }
}
