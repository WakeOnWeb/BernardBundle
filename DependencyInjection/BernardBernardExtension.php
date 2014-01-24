<?php

namespace Bernard\BernardBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class BernardBernardExtension extends \Symfony\Component\HttpKernel\DependencyInjection\Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $options = $config['options'];

        $container->getDefinition('bernard.driver.file')->replaceArgument(0, $options['directory']);
        $container->getDefinition('bernard.schema_listener')
            ->addTag('doctrine.event_listener', array('lazy' => true, 'connection' => $options['connection'], 'event' => 'postGenerateSchema'));

        $container->setAlias('bernard.dbal_connection', 'doctrine.dbal.' . $options['connection'] . '_connection');
        $container->setAlias('bernard.driver', 'bernard.driver.' . $config['driver']);
        $container->setAlias('bernard.serializer', 'bernard.serializer.' . $config['serializer']);
    }
}
