<?php

namespace Abc\JobWorkerBundle\Tests\Functional\DependencyInjection;

use Abc\Job\Interop\JobConsumer;
use Abc\Job\Symfony\DiUtils;
use Abc\JobWorkerBundle\Tests\Functional\WebTestCase;
use Interop\Queue\Processor;

/**
 * @group functional
 */
class JobConsumerTest extends WebTestCase
{
    public function testGetFromContainer()
    {
        $diUtils = new DiUtils();

        $consumer = static::$container->get($diUtils->format('job_consumer'));
        $this->assertInstanceOf(JobConsumer::class, $consumer);
        $this->assertInstanceOf(Processor::class, $consumer);
    }
}
