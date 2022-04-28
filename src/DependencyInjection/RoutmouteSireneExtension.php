<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class RoutmouteSireneExtension extends Extension {
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('routmoute_sirene.consumer_key', $config['consumer_key']);
        $container->setParameter('routmoute_sirene.consumer_secret', $config['consumer_secret']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yaml');
    }
}
