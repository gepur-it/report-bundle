<?php

namespace GepurIt\ReportBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package LdapBundle\DependencyInjection
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('report');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('reports_path')->isRequired()->end()
                ->arrayNode('types')
                    ->useAttributeAsKey('typeId')
                    ->arrayPrototype()
                        ->children()
                            ->enumNode('definition')
                                ->values(['yaml', 'service'])
                                ->defaultValue('yaml')
                            ->end()
                            ->scalarNode('folder')->end()
                            ->scalarNode('service')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
