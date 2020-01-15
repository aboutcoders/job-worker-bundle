<?php

namespace Abc\JobWorkerBundle\Tests\Functional\Client;

use Abc\Job\Client\JobClient;
use Abc\Job\Symfony\DiUtils;

class JobClientTest
{
    public function testGetFromContainer()
    {
        $diUtils = new DiUtils();

        $consumer = static::$container->get($diUtils->format('job_client'));

        $this->assertInstanceOf(JobClient::class, $consumer);
    }
}
