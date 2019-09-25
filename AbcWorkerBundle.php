<?php

namespace Abc\WorkerBundle;

use Abc\WorkerBundle\DependencyInjection\Compiler\BuildJobProcessorPass;
use Abc\WorkerBundle\DependencyInjection\Compiler\BuildJobSubscriberPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AbcWorkerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BuildJobProcessorPass());
        $container->addCompilerPass(new BuildJobSubscriberPass());
    }
}
