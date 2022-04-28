<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('routmoute_sirene');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('consumer_key')->end()
                ->scalarNode('consumer_secret')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
