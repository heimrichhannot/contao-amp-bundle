<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\ManagerPlugin\Config\ExtensionPluginInterface;
use HeimrichHannot\AmpBundle\HeimrichHannotAmpBundle;
use HeimrichHannot\HeadBundle\HeimrichHannotContaoHeadBundle;
use HeimrichHannot\UtilsBundle\Container\ContainerUtil;
use Symfony\Component\Config\Loader\LoaderInterface;

class Plugin implements BundlePluginInterface, ConfigPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        $loadAfter = [ContaoCoreBundle::class, HeimrichHannotContaoHeadBundle::class];

        if (class_exists('HeimrichHannot\EncoreBundle\HeimrichHannotContaoEncoreBundle')) {
            $loadAfter[] = 'HeimrichHannot\EncoreBundle\HeimrichHannotContaoEncoreBundle';
        }

        return [
            BundleConfig::create(HeimrichHannotAmpBundle::class)->setLoadAfter($loadAfter),
        ];
    }

    /**
     * @inheritDoc
     */
    public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig)
    {
        $loader->load("@HeimrichHannotAmpBundle/Resources/config/config.yml");
    }
}
