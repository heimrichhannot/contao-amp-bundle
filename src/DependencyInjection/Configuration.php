<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
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
        $treeBuilder = new TreeBuilder('huh_amp');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('huh_amp');
        }

        $rootNode
        ->children()
            ->arrayNode('templates')
                ->info('Register amp templates.')
                ->useAttributeAsKey('name')
                ->arrayPrototype()
                    ->children()
                        ->arrayNode('components')
                            ->info('Required amp components for this template.')
                            ->defaultValue([])
                            ->scalarPrototype()->end()
                        ->end()
                        ->booleanNode('amp_template')->defaultFalse()->info('Use the original template instead of an _amp substitute.')->end()
                        ->booleanNode('convert_html')->defaultFalse()->info('Convert the html code to amp code. Requires lullabot/amp.')->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('components')
                ->info('Register additional amp components')
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
