<?php

namespace Rj\EmailBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TwigEmailTemplateLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('twig');
        $definition->addMethodCall('setLoader', array(new Reference('rj_email.twig_chain_loader')));
    }
}
