<?php

namespace Rj\EmailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('rj_email');


		$rootNode->children()
            ->scalarNode('custom_loader')->defaultValue(true)->end()
            ;

        return $treeBuilder;
    }
}
