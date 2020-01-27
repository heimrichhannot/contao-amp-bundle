<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\DataContainer;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class PageContainer implements ContainerAwareInterface, FrameworkAwareInterface
{
    use FrameworkAwareTrait;
    use ContainerAwareTrait;

    /**
     * Return all amp page layouts grouped by theme.
     *
     * @return array
     */
    public function getAmpPageLayouts()
    {
        $qb = new QueryBuilder($this->container->get('database_connection'));

        $qb->select(['l.id', 'l.name', 't.name as theme'])->from('tl_layout', 'l')->leftJoin('l', 'tl_theme', 't', 'l.pid=t.id')->orderBy('t.name, l.name')->where('l.addAmp = 1');

        $layouts = $qb->execute()->fetchAll();

        $return = [];

        foreach ($layouts as $layout) {
            $return[$layout['theme']][$layout['id']] = $layout['name'];
        }

        return $return;
    }
}
