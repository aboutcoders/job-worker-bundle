<?php

namespace Abc\JobServerBundle\Tests\Functional\Command;

use Abc\JobWorkerBundle\Tests\Functional\KernelTestCase;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class SetupBrokerCommandTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    public function testExecute()
    {
        $application = new Application(static::$kernel);

        $command = $application->find('abc:broker:setup');

        $input = [
            'command' => $command->getName(),
        ];
        $options = ['verbosity' => OutputInterface::VERBOSITY_DEBUG];

        $this->mockClientResponse();

        $commandTester = new CommandTester($command);
        $commandTester->execute($input, $options);

        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('Successfully setup broker', $commandTester->getDisplay());
    }

    protected function mockClientResponse(): void
    {
        $response = new Response(204, []);

        $mockHandler = static::$container->get('app.broker_http_client.mock_handler');
        $mockHandler->append($response);
    }
}
