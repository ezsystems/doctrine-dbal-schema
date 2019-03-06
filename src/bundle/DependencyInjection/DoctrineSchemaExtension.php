<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchemaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DoctrineSchemaExtension extends Extension
{
    /**
     * Override default extension alias name to include eZ vendor in name.
     *
     * @return string
     */
    public function getAlias(): string
    {
        return 'ez_doctrine_schema';
    }

    /**
     * Load Doctrine Schema Extension config.
     *
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');
        $container->addResource(new FileResource(__DIR__ . '/../Resources/config/services.yaml'));

        $loader->load('api.yaml');
        $container->addResource(new FileResource(__DIR__ . '/../Resources/config/api.yaml'));

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (isset($config['tables']['options'])) {
            $container->setParameter(
                'ez_doctrine_schema.default_table_options',
                $config['tables']['options']
            );
        }
    }
}
