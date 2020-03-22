<?php

namespace Abc\JobWorkerBundle\DependencyInjection;

use Abc\Job\Broker\RouteCollection;
use Abc\Job\Client\BrokerClient;
use Abc\Job\Client\BrokerHttpClient;
use Abc\Job\Client\BrokerRegistryClient;
use Abc\Job\Client\JobClient;
use Abc\Job\Client\JobHttpClient;
use Abc\Job\Client\RouteClient;
use Abc\Job\Client\RouteHttpClient;
use Abc\Job\Processor\ProcessorRegistry;
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
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');

        $diUtils = DiUtils::create();

        $container->setParameter($diUtils->parameter('default_queue'), $config['default_queue']);
        $container->setParameter($diUtils->parameter('default_replyTo'), $config['default_replyTo']);

        $container->register($diUtils->format('route_collection'), RouteCollection::class)
            ->addArgument([])
            ->setFactory([RouteCollection::class, 'fromArray']);

        $this->registerBrokerClient($container, $config, $diUtils);
        $this->registerRouteClient($container, $config, $diUtils);
        $this->registerJobClient($container, $config, $diUtils);
    }

    private function registerBrokerClient(ContainerBuilder $container, array $config, DiUtils $diUtils): void
    {
        // Broker Base Client
        $container->register($diUtils->format('broker_base_client'), Client::class)
            ->setPublic(true);

        // BrokerHttpClient
        $container->register($diUtils->format('broker_http_client'), BrokerHttpClient::class)
            ->addArgument($config['server_baseUrl'])
            ->addArgument(new Reference($diUtils->format('broker_base_client')));

        // BrokerClient
        $container->register($diUtils->format('broker_client'), BrokerClient::class)
            ->addArgument(new Reference($diUtils->format('broker_http_client')));

        // BrokerRegistry
        $container->register($diUtils->format('broker_registry'), BrokerRegistryClient::class)
            ->addArgument(new Reference($diUtils->format('broker_client')));
    }

    private function registerRouteClient(ContainerBuilder $container, array $config, DiUtils $diUtils): void
    {
        // Route Client
        $container->register($diUtils->format('route_base_client'), Client::class)
            ->setPublic(true);

        // RouteHttpClient
        $container->register($diUtils->format('route_http_client'), RouteHttpClient::class)
            ->addArgument($config['server_baseUrl'])
            ->addArgument(new Reference($diUtils->format('route_base_client')));

        // Route Client
        $container->register($diUtils->format('route_base_client'), Client::class);

        // RouteHttpClient
        $container->register($diUtils->format('route_http_client'), RouteHttpClient::class)
            ->addArgument($config['server_baseUrl'])
            ->addArgument(new Reference($diUtils->format('route_base_client')));

        // RouteClient
        $container->register($diUtils->format('route_client'), RouteClient::class)
            ->addArgument(new Reference($diUtils->format('route_http_client')))
            ->addArgument(new Reference('logger'));
    }

    private function registerJobClient(ContainerBuilder $container, array $config, DiUtils $diUtils)
    {
        // Job Client
        $container->register($diUtils->format('job_base_client'), Client::class)
            ->setPublic(true);;

        // JobHttpClient
        $container->register($diUtils->format('job_http_client'), JobHttpClient::class)
            ->addArgument($config['server_baseUrl'])
            ->addArgument(new Reference($diUtils->format('job_base_client')));

        // JobClient
        $container->register($diUtils->format('job_client'), JobClient::class)
            ->addArgument(new Reference($diUtils->format('job_http_client')))
            ->addArgument(new Reference('logger'))
            ->setPublic(true);;

        $processorRegistryId = $diUtils->format('processor_registry');
        $container->register($processorRegistryId, ProcessorRegistry::class);
    }
}
