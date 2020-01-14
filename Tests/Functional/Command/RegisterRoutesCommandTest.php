<?php

namespace Abc\JobWorkerBundle\Tests\Functional\Command;

use Abc\Job\Broker\Route;
use Abc\JobWorkerBundle\Tests\Functional\KernelTestCase;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class RegisterRoutesCommandTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    public function testExecute()
    {
        $application = new Application(static::$kernel);

        $command = $application->find('abc:register:routes');

        $input = [
            'command' => $command->getName(),
        ];
        $options = ['verbosity' => OutputInterface::VERBOSITY_DEBUG];

        $this->mockRouteClientResponse();

        $commandTester = new CommandTester($command);
        $commandTester->execute($input, $options);

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertContains('No routes to register', $output);
    }

    protected function mockRouteClientResponse(): void
    {
        $response = new Response(204, []);

        $mockHandler = static::$container->get('app.http_route_client.mock_handler');
        $mockHandler->append($response);
    }
}
