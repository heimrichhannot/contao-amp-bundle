<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
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

        $rootNode = $treeBuilder->root('huh_amp');

        $rootNode
        ->children()
            ->arrayNode('templates')
                ->useAttributeAsKey('name')
                ->arrayPrototype()
                    ->children()
                        ->arrayNode('components')
                            ->defaultValue([])
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('custom')
                            ->defaultValue([])
                            ->scalarPrototype()->end()
                        ->end()
                        ->booleanNode('ampTemplate')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('components')
                ->useAttributeAsKey('name')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('url')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
