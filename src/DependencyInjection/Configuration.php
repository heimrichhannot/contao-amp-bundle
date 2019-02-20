<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('huh');

        $rootNode
            ->children()
                ->arrayNode('amp')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('active')
                            ->defaultValue(false)
                        ->end()
                        ->arrayNode('ui_elements')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('template')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('ampName')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('libraries')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('ampName')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('url')
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
