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
        $dca = &$GLOBALS['TL_DCA']['tl_layout'];

        if (null !== $layout && !$layout->ampLayout && $this->isAmpLayout($dc->id)) {
            $dca['palettes']['default'] = str_replace('addAmp', 'addAmpAnalytics', $dca['palettes']['default']);
        }
    }

    public function isAmpLayout(int $id)
    {
        return null !== System::getContainer()->get('huh.utils.model')->findModelInstancesBy('tl_layout', ['ampLayout=?'], [$id]);
    }
}
