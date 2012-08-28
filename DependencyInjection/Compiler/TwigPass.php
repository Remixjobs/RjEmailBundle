<?php

namespace Rj\EmailBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (true === $container->getParameter('rj_email.custom_loader')) {
            $loader = $container->getDefinition('twig.loader');

            $class = $loader->getClass();
            if (preg_match("/%(.*)%/", $class, $m)) {
                $class = $container->getParameter($m[1]);
            }

            if ("Twig\Loader\Chain" === $class) {
                $loader->addMethodCall('addLoader', array($container->getDefinition('rj_email.email_template_loader')));
                return;
            }

            $customLoader = $container->getDefinition('rj_email.twig_loader');
            $customLoader->addMethodCall('addLoader', array($loader));
            $container->setDefinition('twig.loader', $customLoader);
        }
    }
}
