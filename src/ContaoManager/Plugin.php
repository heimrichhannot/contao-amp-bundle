<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\ManagerPlugin\Config\ExtensionPluginInterface;
use HeimrichHannot\AmpBundle\HeimrichHannotContaoAmpBundle;
use HeimrichHannot\HeadBundle\HeimrichHannotContaoHeadBundle;
use HeimrichHannot\UtilsBundle\Container\ContainerUtil;

class Plugin implements BundlePluginInterface, ExtensionPluginInterface
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
            BundleConfig::create(HeimrichHannotContaoAmpBundle::class)->setLoadAfter($loadAfter),
        ];
    }

    public function getExtensionConfig($extensionName, array $extensionConfigs, ContainerBuilder $container)
    {
        return ContainerUtil::mergeConfigFile(
            'huh_amp',
            $extensionName,
            $extensionConfigs,
            __DIR__.'/../Resources/config/config.yml'
        );
    }
}
