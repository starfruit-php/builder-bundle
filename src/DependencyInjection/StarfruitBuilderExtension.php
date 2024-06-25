<?php

namespace Starfruit\BuilderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class StarfruitBuilderExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');

        $container->setParameter('starfruit_builder.object', $config['link_generate_objects'] ?? null);
        $container->setParameter('starfruit_builder.seo', $config['seo'] ?? null);
        $container->setParameter('starfruit_builder.mail', $config['mail'] ?? null);
        $container->setParameter('starfruit_builder.notification', $config['notification'] ?? null);
        $container->setParameter('starfruit_builder.security', $config['security'] ?? null);
        $container->setParameter('starfruit_builder.sitemap', $config['sitemap'] ?? null);
        $container->setParameter('starfruit_builder.template', $config['template'] ?? null);
    }
}
