<?php

namespace Abc\JobWorkerBundle\DependencyInjection;

use Abc\Job\Broker\RouteCollection;
use Abc\Job\Enqueue\Consumption\RegisterRoutesExtension;
use Abc\Job\HttpRouteClient;
use Abc\Job\Processor\ProcessorRegistry;
use Abc\Job\RouteClient;
use Abc\Job\Symfony\DiUtils;
use GuzzleHttp\Client;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class AbcJobWorkerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yml');

        $diUtils = DiUtils::create();

        $container->setParameter($diUtils->parameter('default_queue'), $config['default_queue']);
        $container->setParameter($diUtils->parameter('default_replyTo'), $config['default_replyTo']);

        $container->register($diUtils->format('route_collection'), RouteCollection::class)
            ->addArgument([])
            ->setFactory([RouteCollection::class, 'fromArray'])
        ;

        // Client
        $container->register($diUtils->format('http_route_client_client'), Client::class);

        $container->register($diUtils->format('http_route_client'), HttpRouteClient::class)
            ->addArgument($config['server_baseUrl'])
            ->addArgument(new Reference($diUtils->format('http_route_client_client')))
        ;

        // RouteClient
        $container->register($diUtils->format('route_client'), RouteClient::class)
            ->addArgument(new Reference($diUtils->format('http_route_client')))
            ->addArgument(new Reference('logger'))
        ;

        // RegisterRoutesExtension
        $container->register($diUtils->format('register_routes_extension'), RegisterRoutesExtension::class)
            ->addArgument(new Reference($diUtils->format('route_client')))
            ->addArgument(new Reference($diUtils->format('route_collection')))
            ->addTag('enqueue.transport.consumption_extension')
        ;

        $processorRegistryId = $diUtils->format('processor_registry');
        $container->register($processorRegistryId, ProcessorRegistry::class);
    }
}
