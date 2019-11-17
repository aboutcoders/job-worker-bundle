<?php

namespace Abc\JobWorkerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $tb = new TreeBuilder('abc_job_worker');
            $rootNode = $tb->getRootNode();
        } else {
            $tb = new TreeBuilder();
            $rootNode = $tb->root('abc_job_worker');
        }

        $rootNode
            ->children()
                ->scalarNode('server_baseUrl')
                    ->cannotBeEmpty()
                    ->isRequired()
                    ->info('The URL of the job server')
                ->end()
                ->scalarNode('default_queue')
                    ->defaultValue('default')
                ->end()
                ->scalarNode('default_replyTo')
                    ->defaultValue('reply')
                ->end()
                ->scalarNode('jobs')
                    ->defaultNull()
                ->end()
            ->end();

        return $tb;
    }
}
