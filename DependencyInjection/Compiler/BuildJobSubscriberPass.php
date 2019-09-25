<?php

namespace Abc\AbcJobWorkerBundle\DependencyInjection\Compiler;

use Abc\Job\JobSubscriberInterface;
use Abc\Job\Symfony\DiUtils;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BuildJobSubscriberPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $diUtils = DiUtils::create();

        $processorRegistryId = $diUtils->format('processor_registry');
        $processorRegistryDefinition = $container->getDefinition($processorRegistryId);

        $tag = 'abc.job.subscriber';
        foreach ($container->findTaggedServiceIds($tag) as $serviceId => $tagAttributes) {
            $subscriberDefinition = $container->getDefinition($serviceId);
            $subscriberClass = $subscriberDefinition->getClass();
            if (false == class_exists($subscriberClass)) {
                throw new \LogicException(sprintf('The subscriber class "%s" could not be found.', $subscriberClass));
            }

            if (false == is_subclass_of($subscriberClass, JobSubscriberInterface::class)) {
                throw new \LogicException(sprintf('A subscriber must implement "%s" interface to be used with the tag "%s"', JobSubscriberInterface::class, $tag));
            }

            /** @var JobSubscriberInterface $subscriberClass */
            $jobs = $subscriberClass::getSubscribedJob();

            if (empty($jobs)) {
                throw new \LogicException('Job subscriber must return something.');
            }

            if (is_string($jobs)) {
                $jobs = [$jobs];
            }

            if (! is_array($jobs)) {
                throw new \LogicException('Job subscriber configuration is invalid. Should be an array or string.');
            }

            foreach ($jobs as $params) {
                if (is_string($params)) {
                    $processorRegistryDefinition->addMethodCall('add', [
                        $params,
                        new Reference($serviceId),
                    ]);
                } elseif (is_array($params)) {
                    $job = $params['job'] ?? null;
                    $processor = $params['processor'] ?? $serviceId;

                    $processorRegistryDefinition->addMethodCall('add', [
                        $job,
                        new Reference($processor),
                    ]);
                } else {
                    throw new \LogicException(sprintf('Job Subscriber configuration is invalid for "%s::getSubscribedJobs()". Got "%s"', $subscriberClass, json_encode($subscriberClass::getSubscribedJob())));
                }
            }
        }
    }
}
