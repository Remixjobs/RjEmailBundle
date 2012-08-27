<?php

namespace Rj\EmailBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (true === $container->getParameter('rj_email.custom_loader')) {
            $loader = $container->getParameter('twig.loader.class');
            if ($loader === "Symfony\Bundle\TwigBundle\Loader\FilesystemLoader") {
                $abTwigLoader = $container->getDefinition('rj_email.twig_chain_loader');
                $container->setDefinition('twig.loader', $abTwigLoader);
            } else if ($loader === "Twig\Loader\Chain") {
                $loader->addMethodCall('addLoader', new Reference('rj_email.email_template_loader'));
            } else {
                throw new \Exception("Invalid Twig loader");
            }
        }
    }
}
