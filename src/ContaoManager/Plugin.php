<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use HeimrichHannot\AmpBundle\HeimrichHannotAmpBundle;
use HeimrichHannot\EncoreBundle\HeimrichHannotContaoEncoreBundle;
use HeimrichHannot\HeadBundle\HeimrichHannotContaoHeadBundle;
use Symfony\Component\Config\Loader\LoaderInterface;

class Plugin implements BundlePluginInterface, ConfigPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        $loadAfter = [
            ContaoCoreBundle::class,
            HeimrichHannotContaoHeadBundle::class,
        ];

        if (class_exists(HeimrichHannotContaoEncoreBundle::class)) {
            $loadAfter[] = HeimrichHannotContaoEncoreBundle::class;
        }

        return [
            BundleConfig::create(HeimrichHannotAmpBundle::class)->setLoadAfter($loadAfter),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig)
    {
        $loader->load('@HeimrichHannotAmpBundle/Resources/config/config.yml');
    }
}
