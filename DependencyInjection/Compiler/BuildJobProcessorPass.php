<?php

namespace Abc\AbcJobWorkerBundle\DependencyInjection\Compiler;

use Abc\Job\Processor\ProcessorInterface;
use Abc\Job\Symfony\DiUtils;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BuildJobProcessorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $tag = 'abc.job.processor';

        $diUtils = DiUtils::create();

        $processorRegistryId = $diUtils->format('processor_registry');
        $processorRegistryDefinition = $container->getDefinition($processorRegistryId);

        foreach ($container->findTaggedServiceIds($tag) as $serviceId => $tagAttributes) {

            $processorDefinition = $container->getDefinition($serviceId);
            $processorClass = $processorDefinition->getClass();
            if (false == class_exists($processorClass)) {
                throw new \LogicException(sprintf('The processor class "%s" could not be found.', $processorClass));
            }

            if (false == is_subclass_of($processorClass, ProcessorInterface::class)) {
                throw new \LogicException(sprintf('A processor must implement "%s" interface to be used with the tag "%s"', ProcessorInterface::class, $tag));
            }

            foreach ($tagAttributes as $tagAttribute) {
                if (! array_key_exists('jobName', $tagAttribute)) {
                    throw new \LogicException(sprintf('The attribute "%s" must be provided with the tag "%s"', 'jobName', $tag));
                }

                $processorRegistryDefinition->addMethodCall('add', [
                    $tagAttribute['jobName'],
                    new Reference($serviceId),
                ]);
            }
        }
    }
}
