<?php

namespace AppVentus\MangopayBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app_ventus_mangopay');
        
        $rootNode
            ->children()
                ->booleanNode('debug_mode')->defaultValue(false)->end()
                ->scalarNode('client_id')->isRequired()->end()
                ->scalarNode('client_password')->isRequired()->end()
                ->scalarNode('base_url')->isRequired()->end()
            ->end();

        return $treeBuilder;
    }
}
