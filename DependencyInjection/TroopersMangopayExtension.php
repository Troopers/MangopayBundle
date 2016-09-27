<?php

namespace Troopers\MangopayBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TroopersMangopayExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        $container->setParameter('Troopers_mangopay.debug_mode',       $config['debug_mode'] === true);
        $container->setParameter('Troopers_mangopay.client_id',        $config['client_id']);
        $container->setParameter('Troopers_mangopay.client_password',  $config['client_password']);
        $container->setParameter('Troopers_mangopay.base_url',         $config['base_url']);
    }
}
