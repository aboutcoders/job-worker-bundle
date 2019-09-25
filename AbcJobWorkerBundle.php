<?php

namespace Abc\AbcJobWorkerBundle;

use Abc\AbcJobWorkerBundle\DependencyInjection\Compiler\BuildJobProcessorPass;
use Abc\AbcJobWorkerBundle\DependencyInjection\Compiler\BuildJobSubscriberPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AbcJobWorkerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BuildJobProcessorPass());
        $container->addCompilerPass(new BuildJobSubscriberPass());
    }
}
