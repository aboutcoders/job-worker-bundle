<?php

namespace Abc\JobWorkerBundle\Tests\Functional\DependencyInjection;

use Abc\Job\Processor\ProcessorRegistry;
use Abc\Job\Symfony\DiUtils;
use Abc\JobWorkerBundle\Tests\Functional\WebTestCase;

/**
 * @group functional
 */
class ProcessorRegistryTest extends WebTestCase
{
    public function testGetFromContainer()
    {
        $diUtils = new DiUtils();

        $consumer = static::$container->get($diUtils->format('processor_registry'));
        $this->assertInstanceOf(ProcessorRegistry::class, $consumer);
    }
}
