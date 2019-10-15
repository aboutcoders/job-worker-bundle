<?php

namespace Abc\JobWorkerBundle\DependencyInjection\Compiler;

use Abc\Job\Broker\Route;
use Abc\Job\Broker\RouteCollection;
use Abc\Job\RouteProviderInterface;
use Abc\Job\Symfony\DiUtils;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BuildRouteProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $diUtils = DiUtils::create();

        $routeCollectionId = $diUtils->format('route_collection');
        if (false == $container->hasDefinition($routeCollectionId)) {
            throw new \LogicException(sprintf('Service "%s" not found', $routeCollectionId));
        }

        $defaultQueue = $container->getParameter($diUtils->parameter('default_queue'));
        $defaultReplyTo = $container->getParameter($diUtils->parameter('default_replyTo'));

        $tag = 'abc.job.route_provider';

        $routeCollection = new RouteCollection();

        foreach ($container->findTaggedServiceIds($tag) as $serviceId => $tagAttributes) {
            $routeProviderDefinition = $container->getDefinition($serviceId);
            $routeProviderClass = $routeProviderDefinition->getClass();
            if (false == class_exists($routeProviderClass)) {
                throw new \LogicException(sprintf('The provider class "%s" could not be found.', $routeProviderClass));
            }

            if (false == is_subclass_of($routeProviderClass, RouteProviderInterface::class)) {
                throw new \LogicException(sprintf('A provider must implement "%s" interface to be used with the tag "%s"', RouteProviderInterface::class, $tag));
            }

            /** @var RouteProviderInterface $routeProviderClass */
            $routes = $routeProviderClass::getRoutes();

            if (empty($routes)) {
                throw new \LogicException('Route provider must return something.');
            }

            if (! is_array($routes)) {
                throw new \LogicException('Route provider configuration is invalid. Should be an array.');
            }

            if (isset($routes['name'])) {
                $routeCollection->add(new Route($routes['name'], $routes['queue'] ?? $defaultQueue, $routes['replyTo'] ?? $defaultReplyTo));
            }
            else {
                foreach ($routes as $route) {
                    if (! isset($routes['name'])) {
                        throw new \LogicException('Route provider configuration is invalid. Array key "name" must be set.');
                    }
                    $routeCollection->add(new Route($routes['name'], $routes['queue'] ?? $defaultQueue, $routes['replyTo'] ?? $defaultReplyTo));
                }
            }
        }

        $rawRoutes = $routeCollection->toArray();

        $routeCollectionService = $container->getDefinition($routeCollectionId);
        $routeCollectionService->replaceArgument(0, array_merge(
            $routeCollectionService->getArgument(0),
            $rawRoutes
        ));
    }
}
