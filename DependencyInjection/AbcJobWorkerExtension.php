<?php

namespace Abc\JobWorkerBundle\DependencyInjection;

use Abc\Job\Processor\ProcessorRegistry;
use Abc\Job\Symfony\DiUtils;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AbcJobWorkerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yml');

        $diUtils = DiUtils::create();

        /*$container->getDefinition($diUtils->format('job_consumer'))
            ->addMethodCall('limitJobs', $config['jobs']);*/

        $processorRegistryId = $diUtils->format('processor_registry');
        $container->register($processorRegistryId, ProcessorRegistry::class);
    }
}
