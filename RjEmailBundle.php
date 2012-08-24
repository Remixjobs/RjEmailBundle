<?php

namespace Rj\EmailBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Rj\EmailBundle\DependencyInjection\TwigEmailTemplateLoaderPass;

class RjEmailBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TwigEmailTemplateLoaderPass());
        //$container->addCompilerPass(new SortingPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
    }
}
