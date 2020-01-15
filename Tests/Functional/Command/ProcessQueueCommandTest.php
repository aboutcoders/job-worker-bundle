<?php

namespace Abc\JobWorkerBundle\Tests\Functional\Command;

use Abc\Job\Broker\Route;
use Abc\JobWorkerBundle\Tests\Functional\KernelTestCase;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class ProcessQueueCommandTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    public function testExecute()
    {
        $application = new Application(static::$kernel);

        $timestamp = strtotime('yesterday');
        $timeLimit = new \DateTime("@$timestamp");

        $command = $application->find('abc:process:queue');

        $input = [
            'command' => $command->getName(),

            // pass arguments to the helper
            'queues' => 'myQueueName',

            // limits extension
            '--message-limit' => 1,
            '--time-limit' => $timeLimit->format('Y-m-d H:i:s'),
            '--memory-limit' => 1024,
            '--niceness' => 1,

            // queue consumer options
            '--receive-timeout' => 10,

            // logger extension
            '--logger' => 'stdout',
            '--vvv',
        ];
        $options = ['verbosity' => OutputInterface::VERBOSITY_DEBUG];

        $this->mockRouteClientResponse();

        $commandTester = new CommandTester($command);
        $commandTester->execute($input, $options);

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertContains('Consumption has started', $output);
    }

    protected function mockRouteClientResponse(): void
    {
        $route_A = new Route('jobA', 'queueName', 'replyTo');
        $route_B = new Route('jobB', 'queueName', 'replyTo');

        $json = json_encode([(object) $route_A->toArray(), (object) $route_B->toArray()]);

        $response = new Response(200, [], $json);

        $mockHandler = static::$container->get('app.route_http_client.mock_handler');
        $mockHandler->append($response);
    }
}
