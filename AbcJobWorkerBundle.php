<?php

namespace Abc\JobWorkerBundle;

use Abc\JobWorkerBundle\DependencyInjection\Compiler\BuildJobProcessorPass;
use Abc\JobWorkerBundle\DependencyInjection\Compiler\BuildRouteProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AbcJobWorkerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BuildJobProcessorPass());
        $container->addCompilerPass(new BuildRouteProviderPass());
    }
}
