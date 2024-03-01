<?php

namespace Starfruit\BuilderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('starfruit_builder');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('link_generate_objects')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('class_name')
                                ->info('Class name of object include slug field')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('field_create_slug')
                                ->info('Name of field used to create slug')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('field_for_slug')
                                ->info('Name of slug field in object')
                                ->cannotBeEmpty()
                            ->end()
                            ->booleanNode('update_while_empty')
                                ->info('Yes or no that creating slug from field_create_slug value while field_for_slug value is empty')
                                ->defaultTrue()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
